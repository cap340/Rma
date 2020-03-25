<?php

namespace Cap\Rma\Block\Customer;

use Magento\Framework\View\Element\Template;

class Request extends Template
{
    /**
     * @var \Cap\Rma\Model\Request
     */
    protected $requestCollection;

    /**
     * Request constructor.
     *
     * @param Template\Context $context
     * @param \Cap\Rma\Model\Request $requestCollection
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        \Cap\Rma\Model\Request $requestCollection,
        array $data = []
    ) {
        $this->requestCollection = $requestCollection;
        parent::__construct($context, $data);
    }

    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $this->pageConfig->getTitle()->set(__('My Reward History'));

        if ($this->getRewardHistory()) {
            $pager = $this->getLayout()->createBlock(
                'Magento\Theme\Block\Html\Pager',
                'reward.history.pager'
            )->setAvailableLimit([5=>5,10=>10,15=>15,20=>20])
                ->setShowPerPage(true)->setCollection(
                    $this->getRewardHistory()
                );
            $this->setChild('pager', $pager);
            $this->getRewardHistory()->load();
        }
        return $this;
    }

    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    public function getRewardHistory()
    {
        //get values of current page
        $page=($this->getRequest()->getParam('p'))? $this->getRequest()->getParam('p') : 1;
        //get values of current limit
        $pageSize=($this->getRequest()->getParam('limit'))? $this->getRequest(
        )->getParam('limit') : 5;

        $collection = $this->requestCollection->getCollection();
        $collection->setPageSize($pageSize);
        $collection->setCurPage($page);

        return $collection;
    }
}
