<?php

namespace Cap\Rma\Controller\Adminhtml\Request;

use Cap\Rma\Controller\Adminhtml\AbstractMassAction;
use Cap\Rma\Helper\Data;
use Cap\Rma\Helper\Email;
use Cap\Rma\Model\Config\Source\Request\Status;
use Cap\Rma\Model\Request;
use Cap\Rma\Model\ResourceModel\Request\CollectionFactory;
use Exception;
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
     * @var Email
     */
    protected $emailSender;

    /**
     * @var Data
     */
    protected $helper;

    /**
     * MassReject constructor.
     *
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param DateTime $dateTime
     * @param Email $emailSender
     * @param Data $helper
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        DateTime $dateTime,
        Email $emailSender,
        Data $helper
    ) {
        parent::__construct($context, $filter);
        $this->collectionFactory = $collectionFactory;
        $this->dateTime = $dateTime;
        $this->emailSender = $emailSender;
        $this->helper = $helper;
    }

    /**
     * @inheritDoc
     * @noinspection DuplicatedCode
     */
    protected function massAction(AbstractCollection $collection)
    {
        $countRejectRequest = 0;
        $now = $this->dateTime->gmtDate();

        /** @var Request $request */
        foreach ($collection->getItems() as $request) {
            $request->setStatus(self::REJECTED);
            $request->setUpdatedAt($now);
            $request->save();
            $countRejectRequest++;

            //todo if comment add
            $emailData = [
                'customerEmail' => $request->getCustomerEmail(),
                'requestId' => $request->getRequestId(),
                'requestType' => $this->helper->getTypeOptionLabel($request->getType()),
                'orderIncrementId' => $request->getIncrementId(),
                'customerName' => $request->getCustomerName(),
                'description' => $request->getDescription(),
                'createdAt' => $request->getCreatedAt()
            ];
            try {
                $template = $this->emailSender->getConfigEmailTemplateRejected();
                $this->emailSender->sendEmailCustomer($emailData, $template);
            } catch (Exception $e) {
                $this->messageManager->addErrorMessage(__('Failed to send email.' . $e->getMessage()));
            }
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
