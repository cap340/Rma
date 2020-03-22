<?php

namespace Cap\Rma\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;

class Email extends AbstractHelper
{
    /**
     * @var Data
     */
    protected $helper;

    /**
     * Email constructor.
     *
     * @param Context $context
     * @param Data $helper
     */
    public function __construct(
        Context $context,
        Data $helper
    ) {
        $this->helper = $helper;
        parent::__construct($context);
    }
}
