<?php

namespace Cap\Rma\Block\Customer;

use Cap\Rma\Model\Request;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Framework\View\Element\Template;
use Magento\Theme\Block\Html\Pager;

class History extends Template
{
    /**
     * @var Request
     */
    protected $requestCollection;

    /**
     * Request constructor.
     *
     * @param Template\Context $context
     * @param Request $requestCollection
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        Request $requestCollection,
        array $data = []
    ) {
        $this->requestCollection = $requestCollection;
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
                [5 => 5, 10 => 10, 15 => 15, 20 => 20]
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
     * @return AbstractCollection
     */
    public function getRequestsHistory()
    {
        $page = ($this->getRequest()->getParam('p')) ? $this->getRequest()->getParam('p') : 1;
        $pageSize = ($this->getRequest()->getParam('limit')) ? $this->getRequest()->getParam('limit') : 5;

        $collection = $this->requestCollection->getCollection();
        $collection->setPageSize($pageSize);
        $collection->setCurPage($page);

        return $collection;
    }
}
