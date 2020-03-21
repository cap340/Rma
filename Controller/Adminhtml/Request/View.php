<?php

namespace Cap\Rma\Controller\Adminhtml\Request;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Result\PageFactory;
use Cap\Rma\Model\RequestRepository;

class View extends Action
{
    /**
     * Authorization level of a basic admin session
     */
    const ADMIN_RESOURCE = 'Cap_Rma::request_view';

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var RequestRepository
     */
    protected $requestRepository;

    /**
     * Index constructor.
     *
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param RequestRepository $requestRepository
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        RequestRepository $requestRepository
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->requestRepository = $requestRepository;
        parent::__construct($context);
    }

    /**
     * Index action
     *
     * @return ResultInterface
     * @throws LocalizedException
     */
    public function execute()
    {
        $requestId = $this->getRequest()->getParam('request_id');
        $model = $this->requestRepository->getById($requestId);
        $incrementId = $model->getIncrementId();
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->prepend(__('Request #%1', $incrementId));
        return $resultPage;
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed(self::ADMIN_RESOURCE);
    }
}
