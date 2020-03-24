<?php

namespace Cap\Rma\Controller\Customer;

use Cap\Rma\Helper\Data;
use Cap\Rma\Helper\Email;
use Cap\Rma\Model\Config\Source\Request\Status as RequestStatus;
use Cap\Rma\Model\RequestFactory;
use Exception;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Data\Form\FormKey\Validator;
use Magento\Framework\Stdlib\DateTime\DateTime;

class Form extends Action
{
    /**
     * @var RequestFactory
     */
    protected $requestFactory;

    /**
     * @var Email
     */
    protected $emailSender;

    /**
     * @var Data
     */
    protected $helper;

    /**
     * @var Validator
     */
    protected $formKeyValidator;

    /**
     * @var DateTime
     */
    protected $dateTime;

    /**
     * Form constructor.
     *
     * @param Context $context
     * @param RequestFactory $requestFactory
     * @param Email $emailSender
     * @param Data $helper
     * @param Validator $formKeyValidator
     * @param DateTime $dateTime
     */
    public function __construct(
        Context $context,
        RequestFactory $requestFactory,
        Email $emailSender,
        Data $helper,
        Validator $formKeyValidator,
        DateTime $dateTime
    ) {
        $this->requestFactory = $requestFactory;
        parent::__construct($context);
        $this->emailSender = $emailSender;
        $this->helper = $helper;
        $this->formKeyValidator = $formKeyValidator;
        $this->dateTime = $dateTime;
    }

    /**
     * @return ResponseInterface|ResultInterface
     * @throws Exception
     */
    public function execute()
    {
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        if (!$this->formKeyValidator->validate($this->getRequest())) {
            $this->messageManager->addErrorMessage('Invalid request!');
            return $resultRedirect->setUrl('dashboard');
        }

        $post = (array)$this->getRequest()->getPost();

        //todo: required fields
        if (!empty($post)) {
            $model = $this->requestFactory->create();
            $model->setCustomerName($post['customerName']);
            $model->setCustomerEmail($post['customerEmail']);
            $model->setIncrementId($post['orderIncrementId']);
            if (isset($post['requestType'])) {
                $model->setType($post['requestType']);
            }
            $model->setDescription($post['description']);
            $model->setStatus(RequestStatus::STATUS_PENDING);
            $model->save();

            $data = $post;
            $data['requestId'] = $model->getRequestId();
            $data['createdAt'] = $this->dateTime->gmtDate();
            try {
                $this->emailSender->sendEmailAdmin($data);
                $this->emailSender->sendEmailCustomer($data);
                $this->messageManager->addSuccessMessage(
                    __('You\'re request number #%1 have been submitted.', $data['requestId'])
                );
                $resultRedirect->setUrl('dashboard');
                return $resultRedirect;
            } catch (Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                $resultRedirect->setUrl('dashboard');
            }
            $resultRedirect->setUrl('dashboard');
            return $resultRedirect;
        }

        $this->_view->loadLayout();
        $this->_view->renderLayout();
        //todo: fix missing return statement
    }
}
