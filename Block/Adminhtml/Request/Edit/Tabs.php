<?php

namespace Cap\Rma\Block\Adminhtml\Request\Edit;

class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    /**
     * Tabs constructor.
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('request_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('REQUEST EDIT'));
    }
}
