<?php

namespace Cap\Rma\Block\Customer;

use Cap\Rma\Model\Request;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Framework\View\Element\Template;
use Magento\Theme\Block\Html\Pager;
use Magento\Customer\Model\Session as CustomerSession;
use Cap\Rma\Model\Config\Source\Request\Status as RequestStatus;

class History extends Template
{
    /**
     * @var Request
     */
    protected $requestCollection;

    /**
     * @var CustomerSession
     */
    protected $customerSession;

    /**
     * @var RequestStatus
     */
    protected $requestStatus;

    /**
     * Request constructor.
     *
     * @param Template\Context $context
     * @param Request $requestCollection
     * @param CustomerSession $customerSession
     * @param RequestStatus $requestStatus
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        Request $requestCollection,
        CustomerSession $customerSession,
        RequestStatus $requestStatus,
        array $data = []
    ) {
        $this->requestCollection = $requestCollection;
        $this->customerSession = $customerSession;
        $this->requestStatus = $requestStatus;
        parent::__construct($context, $data);
    }

    /**
     * @return $this|Template
     * @throws LocalizedException
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $this->pageConfig->getTitle()->set(__('My Requests History'));

        if ($this->getRequestsHistory()) {
            $pager = $this->getLayout()->createBlock(
                Pager::class,
                'request.history.pager'
            )->setAvailableLimit(
                [10 => 10, 20 => 20, 30 => 30]
            )->setShowPerPage(
                true
            )->setCollection(
                $this->getRequestsHistory()
            );
            $this->setChild('pager', $pager);
            $this->getRequestsHistory()->load();
        }
        return $this;
    }

    /**
     * @return string
     */
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    /**
     * @return bool
     */
    public function isLoggedIn()
    {
        if ($this->customerSession->isLoggedIn()) {
            return true;
        }
        return false;
    }

    /**
     * @return AbstractCollection
     */
    public function getRequestsHistory()
    {
        $page = ($this->getRequest()->getParam('p')) ? $this->getRequest()->getParam('p') : 1;
        $pageSize = ($this->getRequest()->getParam('limit')) ? $this->getRequest()->getParam('limit') : 10;

        $collection = $this->requestCollection->getCollection();
        $collection->setOrder('created_at', 'desc');
        $collection->setPageSize($pageSize);
        $collection->setCurPage($page);

        return $collection;
    }

    /**
     * @param $optionId
     * @return mixed|null
     */
    public function getStatusOptionLabel($optionId)
    {
        $options = $this->requestStatus->toOptionArray();
        foreach ($options as $key => $option) {
            if (in_array($optionId, $option)) {
                return $option['label'];
            }
        }

        return null;
    }

    /**
     * @param Request $request
     * @return bool
     */
    public function checkUpdatedRequest(Request $request)
    {
        $createdAt = $request->getCreatedAt();
        $updatedAt = $request->getUpdatedAt();
        if ($updatedAt !== $createdAt) {
            return true;
        }
        return false;
    }
}
