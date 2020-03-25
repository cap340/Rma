<?php

namespace Cap\Rma\Block\Customer;

use Cap\Rma\Helper\Data;
use Magento\Framework\View\Element\Template;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\ResourceModel\Order\Collection as OrderCollection;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory as OrdersCollectionFactory;
use Cap\Rma\Model\Config\Source\Request\Types as RequestTypes;

class Request extends Template
{
    /**
     * @var CustomerSession
     */
    protected $customerSession;

    /**
     * @var Data
     */
    protected $helper;

    /**
     * @var OrdersCollectionFactory
     */
    protected $orderCollectionFactory;

    /**
     * @var RequestTypes
     */
    protected $requestTypes;

    /**
     * Request constructor.
     *
     * @param Template\Context $context
     * @param CustomerSession $customerSession
     * @param Data $helper
     * @param OrdersCollectionFactory $ordersCollectionFactory
     * @param RequestTypes $requestTypes
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        CustomerSession $customerSession,
        Data $helper,
        OrdersCollectionFactory $ordersCollectionFactory,
        RequestTypes $requestTypes,
        array $data = []
    ) {
        $this->customerSession = $customerSession;
        $this->helper = $helper;
        $this->orderCollectionFactory = $ordersCollectionFactory;
        $this->requestTypes = $requestTypes;
        parent::__construct($context, $data);
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
     * Form action to send data in Form Controller.
     *
     * @return string
     */
    public function getFormAction()
    {
        return '/rma/customer/form';
    }

    /**
     * @return OrderCollection
     */
    public function getOrdersCanRequest()
    {
        $customerId = $this->customerSession->getCustomer()->getId();
        $options = $this->helper->getConfigAllowedOrders();
        $options = explode(',', $options);

        $orders = $this->orderCollectionFactory->create();
        return $orders->addFieldToSelect('*')
            ->addFieldToFilter('customer_id', $customerId)
            ->addFieldToFilter('status', ['in' => $options])
            ->setOrder('created_at', 'desc');
    }

    /**
     * @return mixed
     */
    public function getPolicyLink()
    {
        if ($this->helper->getConfigPolicyUrl()) {
            return $this->_urlBuilder->getDirectUrl($this->helper->getConfigPolicyUrl());
        }

        return null;
    }

    /**
     * @return string
     */
    public function getCustomerEditLink()
    {
        return $this->_urlBuilder->getDirectUrl('customer/account/edit/');
    }

    /**
     * @return string
     */
    public function getCustomerName()
    {
        $customerData = $this->customerSession->getCustomerData();
        $name = $customerData->getFirstname();
        $name .= ' ';
        $name .= $customerData->getLastname();

        return $name;
    }

    /**
     * @return string
     */
    public function getCustomerEmail()
    {
        $customerData = $this->customerSession->getCustomerData();
        return $customerData->getEmail();
    }

    /**
     * @return bool
     */
    public function getTypesEnable()
    {
        if ($this->helper->getConfigTypesEnable()) {
            return true;
        }
        return false;
    }

    /**
     * @return array
     */
    public function getTypesOptions()
    {
        $options = $this->helper->getConfigTypesOptions();
        $options = explode(',', $options);
        $values = $this->requestTypes->toOptionArray();

        $types = [];
        foreach ($values as $key => $value) {
            if (in_array($key, $options)) {
                $types[] = [
                    'value' => $value['value'],
                    'label' => $value['label']
                ];
            }
        }
        return $types;
    }

    /**
     * @param Order $order
     * @return string
     */
    public function getOrderLink(Order $order)
    {
        $orderId = $order->getId();
        return $this->_urlBuilder->getDirectUrl('sales/order/view/order_id/' . $orderId);
    }
}
