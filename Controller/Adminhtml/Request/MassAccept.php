<?php

namespace Cap\Rma\Controller\Adminhtml\Request;

use Cap\Rma\Controller\Adminhtml\AbstractMassAction;
use Cap\Rma\Model\Config\Source\Request\Status;
use Cap\Rma\Model\ResourceModel\Request\CollectionFactory;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Ui\Component\MassAction\Filter;

class MassAccept extends AbstractMassAction
{
    /**
     * Authorization level of a basic admin session.
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Cap_Rma::request_accept';
    const ACCEPTED = Status::STATUS_ACCEPTED;

    /**
     * MassReject constructor.
     *
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory
    ) {
        parent::__construct($context, $filter);
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * @inheritDoc
     */
    protected function massAction(AbstractCollection $collection)
    {
        $countAcceptRequest = 0;
        foreach ($collection->getItems() as $request) {
            $request->setStatus(self::ACCEPTED);
            $request->save();
            $countAcceptRequest++;
        }

        if ($countAcceptRequest) {
            $this->messageManager->addSuccessMessage(__('We accepted %1 request(s).', $countAcceptRequest));
        }
        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath($this->getComponentRefererUrl());
        return $resultRedirect;
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed(self::ADMIN_RESOURCE);
    }
}
