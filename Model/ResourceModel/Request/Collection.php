<?php

namespace Cap\Rma\Model\ResourceModel\Request;

use Cap\Rma\Api\Data\RequestInterface;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = RequestInterface::REQUEST_ID;

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \Cap\Rma\Model\Request::class,
            \Cap\Rma\Model\ResourceModel\Request::class
        );
    }
}
