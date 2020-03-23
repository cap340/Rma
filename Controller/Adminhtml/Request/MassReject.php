<?php

namespace Cap\Rma\Controller\Adminhtml\Request;

use Cap\Rma\Controller\Adminhtml\AbstractMassAction;
use Cap\Rma\Model\Config\Source\Request\Status;
use Cap\Rma\Model\ResourceModel\Request\CollectionFactory;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Ui\Component\MassAction\Filter;

class MassReject extends AbstractMassAction
{
    /**
     * Authorization level of a basic admin session.
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Cap_Rma::request_reject';
    const REJECTED = Status::STATUS_REJECTED;

    /**
     * @var DateTime
     */
    protected $dateTime;

    /**
     * MassReject constructor.
     *
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param DateTime $dateTime
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        DateTime $dateTime
    ) {
        parent::__construct($context, $filter);
        $this->collectionFactory = $collectionFactory;
        $this->dateTime = $dateTime;
    }

    /**
     * @inheritDoc
     */
    protected function massAction(AbstractCollection $collection)
    {
        $countRejectRequest = 0;
        $now = $this->dateTime->gmtDate();

        foreach ($collection->getItems() as $request) {
            $request->setStatus(self::REJECTED);
            $request->setUpdatedAt($now);
            $request->save();
            //todo emailSender
            $countRejectRequest++;
        }

        if ($countRejectRequest) {
            $this->messageManager->addSuccessMessage(__('We rejected %1 request(s).', $countRejectRequest));
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
