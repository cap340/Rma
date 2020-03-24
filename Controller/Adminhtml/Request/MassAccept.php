<?php

namespace Cap\Rma\Controller\Adminhtml\Request;

use Cap\Rma\Controller\Adminhtml\AbstractMassAction;
use Cap\Rma\Helper\Email;
use Cap\Rma\Model\Config\Source\Request\Status;
use Cap\Rma\Model\Request;
use Cap\Rma\Model\ResourceModel\Request\CollectionFactory;
use Exception;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Framework\Stdlib\DateTime\DateTime;
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
     * @var DateTime
     */
    protected $dateTime;

    /**
     * @var Email
     */
    protected $emailSender;

    /**
     * MassReject constructor.
     *
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param DateTime $dateTime
     * @param Email $emailSender
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        DateTime $dateTime,
        Email $emailSender
    ) {
        parent::__construct($context, $filter);
        $this->collectionFactory = $collectionFactory;
        $this->dateTime = $dateTime;
        $this->emailSender = $emailSender;
    }

    /**
     * @inheritDoc
     */
    protected function massAction(AbstractCollection $collection)
    {
        $countAcceptRequest = 0;
        $now = $this->dateTime->gmtDate();

        /** @var Request $request */
        foreach ($collection->getItems() as $request) {
            $request->setStatus(self::ACCEPTED);
            $request->setUpdatedAt($now);
            $request->save();
            $countAcceptRequest++;

            //todo if comment add
            $emailData = [
                'customerEmail' => $request->getCustomerEmail(),
                'requestId' => $request->getRequestId(),
                'orderIncrementId' => $request->getIncrementId(),
                'customerName' => $request->getCustomerName(),
                'description' => $request->getDescription(),
                'createdAt' => $request->getCreatedAt()
            ];
            try {
                $template = $this->emailSender->getConfigEmailTemplateAccepted();
                $this->emailSender->sendEmailCustomer($emailData, $template);
            } catch (Exception $e) {
                $this->messageManager->addErrorMessage(__('Failed to send email.' . $e->getMessage()));
            }
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
