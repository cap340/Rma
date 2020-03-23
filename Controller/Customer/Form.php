<?php

namespace Cap\Rma\Controller\Customer;

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
     * Form constructor.
     *
     * @param Context $context
     * @param RequestFactory $requestFactory
     */
    public function __construct(
        Context $context,
        RequestFactory $requestFactory
    ) {
        $this->requestFactory = $requestFactory;
        parent::__construct($context);
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
            $model->save();

            $requestId = $model->getRequestId();
            $this->messageManager->addSuccessMessage(__('You\'re request number #%1 have been submitted.', $requestId));

            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            $resultRedirect->setUrl('/rma/customer/dashboard');

            return $resultRedirect;
        }

        $this->_view->loadLayout();
        $this->_view->renderLayout();

        //todo: fix missing return statement
    }
}
