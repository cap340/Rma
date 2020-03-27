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

        if (!empty($post)) {
            $model = $this->requestFactory->create();
            $model->setCustomerName($post['customerName']);
            $model->setCustomerEmail($post['customerEmail']);
            $model->setIncrementId($post['orderIncrementId']);
            $model->setType($post['requestType']);
            $model->setDescription($post['description']);
            $model->setStatus(RequestStatus::STATUS_PENDING);
            $model->save();

            $emailData = $post;
            $emailData['requestId'] = $model->getRequestId();
            $emailData['createdAt'] = $this->dateTime->gmtDate();
            try {
                $this->emailSender->sendEmailAdmin($emailData);
                $emailTemplate = $this->helper->getConfigEmailTemplateCustomer();
                $this->emailSender->sendEmailCustomer($emailData, $emailTemplate);
                $this->messageManager->addSuccessMessage(
                    __('You\'re request number #%1 have been submitted.', $emailData['requestId'])
                );
                $resultRedirect->setUrl('history');
                return $resultRedirect;
            } catch (Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                $resultRedirect->setUrl('history');
            }
            $resultRedirect->setUrl('history');
            return $resultRedirect;
        }

        $this->_view->loadLayout();
        $this->_view->renderLayout();
        //todo: fix missing return statement
    }
}
