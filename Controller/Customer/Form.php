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
     * Form constructor.
     *
     * @param Context $context
     * @param RequestFactory $requestFactory
     * @param Email $emailSender
     * @param Data $helper
     */
    public function __construct(
        Context $context,
        RequestFactory $requestFactory,
        Email $emailSender,
        Data $helper
    ) {
        $this->requestFactory = $requestFactory;
        parent::__construct($context);
        $this->emailSender = $emailSender;
        $this->helper = $helper;
    }

    /**
     * @return ResponseInterface|ResultInterface
     * @throws Exception
     */
    public function execute()
    {
        //todo: formKey validator
//        if (!$this->formKeyValidator->validate($this->getRequest())) {
//            $this->messageManager->addErrorMessage("Invalid request!");
//            return $resultRedirect->setPath('customer/account/');
//        }
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

            try {
                $this->sendEmail();
                $requestId = $model->getRequestId();
                $this->messageManager->addSuccessMessage(__(
                    'You\'re request number #%1 have been submitted.',
                    $requestId
                ));
            } catch (Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
                $resultRedirect->setUrl('/rma/customer/dashboard');
            }
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            $resultRedirect->setUrl('/rma/customer/dashboard');

            return $resultRedirect;
        }

        $this->_view->loadLayout();
        $this->_view->renderLayout();

        //todo: fix missing return statement
    }

    protected function sendEmail()
    {
        $emailTemplate = $this->helper->getConfigEmailTemplate();
        $adminEmail = $this->helper->getConfigEmailAdmin();
        $adminEmails = explode(',', $adminEmail);
        $countEmail = count($adminEmails);
        if ($countEmail > 1) {
            foreach ($adminEmails as $value) {
                $value = str_replace(' ', '', $value);
                $emailTemplateData = [
                    'adminEmail' => $value,
//                    'incrementId' => $orderData->getIncrementId(),
//                    'customerName' => $orderData->getCustomerName(),
//                    'createdAt' => $orderData->getCreatedAt(),
                ];
                $this->emailSender->sendEmail($value, $emailTemplate, $emailTemplateData);
            }
        } else {
            $emailTemplateData = [
                'adminEmail' => $adminEmail,
//                'incrementId' => $orderData->getIncrementId(),
//                'customerName' => $orderData->getCustomerName(),
//                'createdAt' => $orderData->getCreatedAt(),
            ];
            $this->emailSender->sendEmail($adminEmail, $emailTemplate, $emailTemplateData);
        }
    }
}
