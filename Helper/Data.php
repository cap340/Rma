<?php

namespace Cap\Rma\Helper;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;
use Cap\Rma\Model\Config\Source\Request\Types as RequestTypes;
use Cap\Rma\Model\Config\Source\Request\Status as RequestStatus;

class Data extends AbstractHelper
{
    /**
     * System path configuration
     */
    const CONFIG_TYPES_OPTIONS = 'rma/settings/types';
    const CONFIG_ALLOWED_ORDERS = 'rma/settings/allowed_orders';
    const CONFIG_POLICY_URL = 'rma/settings/policy_url';
    const CONFIG_EMAIL_ADMIN = 'rma/email/admin';
    const CONFIG_EMAIL_SENDER = 'rma/email/sender';
    const CONFIG_EMAIL_TEMPLATE_ADMIN = 'rma/email/template_admin';
    const CONFIG_EMAIL_TEMPLATE_CUSTOMER = 'rma/email/template_customer';
    const CONFIG_EMAIL_TEMPLATE_ACCEPTED = 'rma/email/template_accepted';
    const CONFIG_EMAIL_TEMPLATE_REJECTED = 'rma/email/template_rejected';

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var RequestTypes
     */
    protected $requestTypes;

    /**
     * @var RequestStatus
     */
    protected $requestStatus;

    /**
     * Data constructor.
     *
     * @param Context $context
     * @param ScopeConfigInterface $scopeConfig
     * @param RequestTypes $requestTypes
     * @param RequestStatus $requestStatus
     */
    public function __construct(
        Context $context,
        ScopeConfigInterface $scopeConfig,
        RequestTypes $requestTypes,
        RequestStatus $requestStatus
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->requestTypes = $requestTypes;
        $this->requestStatus = $requestStatus;
        parent::__construct($context);
    }

    /**
     * System Configuration
     */

    /**
     * @return mixed
     */
    public function getConfigTypesOptions()
    {
        $storeScope = ScopeInterface::SCOPE_STORE;
        return $this->scopeConfig->getValue(self::CONFIG_TYPES_OPTIONS, $storeScope);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function getTypeOptionLabel($id)
    {
        $options = $this->requestTypes->toOptionArray();
        $key = array_search($id, array_column($options, 'value'));

        return $options[$key]['label'];
    }

    /**
     * @param $id
     * @return mixed
     */
    public function getStatusOptionLabel($id)
    {
        $options = $this->requestStatus->toOptionArray();
        $key = array_search($id, array_column($options, 'value'));

        return $options[$key]['label'];
    }

    /**
     * @return mixed
     */
    public function getConfigAllowedOrders()
    {
        $storeScope = ScopeInterface::SCOPE_STORE;
        return $this->scopeConfig->getValue(self::CONFIG_ALLOWED_ORDERS, $storeScope);
    }

    /**
     * @return mixed
     */
    public function getConfigPolicyUrl()
    {
        $storeScope = ScopeInterface::SCOPE_STORE;
        return $this->scopeConfig->getValue(self::CONFIG_POLICY_URL, $storeScope);
    }

    /**
     * @return mixed
     */
    public function getConfigEmailSender()
    {
        $storeScope = ScopeInterface::SCOPE_STORE;
        return $this->scopeConfig->getValue(self::CONFIG_EMAIL_SENDER, $storeScope);
    }

    /**
     * @return mixed
     */
    public function getConfigEmailAdmin()
    {
        $storeScope = ScopeInterface::SCOPE_STORE;
        return $this->scopeConfig->getValue(self::CONFIG_EMAIL_ADMIN, $storeScope);
    }

    /**
     * @return mixed
     */
    public function getConfigEmailTemplateAdmin()
    {
        $storeScope = ScopeInterface::SCOPE_STORE;
        return $this->scopeConfig->getValue(self::CONFIG_EMAIL_TEMPLATE_ADMIN, $storeScope);
    }

    /**
     * @return mixed
     */
    public function getConfigEmailTemplateCustomer()
    {
        $storeScope = ScopeInterface::SCOPE_STORE;
        return $this->scopeConfig->getValue(self::CONFIG_EMAIL_TEMPLATE_CUSTOMER, $storeScope);
    }

    /**
     * @return mixed
     */
    public function getConfigEmailTemplateAccepted()
    {
        $storeScope = ScopeInterface::SCOPE_STORE;
        return $this->scopeConfig->getValue(self::CONFIG_EMAIL_TEMPLATE_ACCEPTED, $storeScope);
    }

    /**
     * @return mixed
     */
    public function getConfigEmailTemplateRejected()
    {
        $storeScope = ScopeInterface::SCOPE_STORE;
        return $this->scopeConfig->getValue(self::CONFIG_EMAIL_TEMPLATE_REJECTED, $storeScope);
    }
}
