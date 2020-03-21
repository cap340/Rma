<?php

namespace Cap\Rma\Model\ResourceModel\Request;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
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
