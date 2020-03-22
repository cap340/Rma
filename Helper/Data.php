<?php

namespace Cap\Rma\Helper;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;

class Data extends AbstractHelper
{
    //todo: types vs type in system.xml

    /**
     * System path configuration
     */
    const CONFIG_TYPES = 'rma/settings/type';
    const CONFIG_ALLOWED_ORDERS = 'rma/settings/allowed_orders';

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * Data constructor.
     *
     * @param Context $context
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        Context $context,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
        parent::__construct($context);
    }

    /**
     * Backend
     */

    /**
     * @return mixed
     */
    public function getConfigRequestTypes()
    {
        $storeScope = ScopeInterface::SCOPE_STORE;
        return $this->scopeConfig->getValue(self::CONFIG_TYPES, $storeScope);
    }

    /**
     * @return mixed
     */
    public function getConfigAllowedOrders()
    {
        $storeScope = ScopeInterface::SCOPE_STORE;
        return $this->scopeConfig->getValue(self::CONFIG_ALLOWED_ORDERS, $storeScope);
    }
}
