<?php

namespace Cap\Rma\Block\Customer;

use Cap\Rma\Helper\Data;
use Cap\Rma\Model\Config\Source\Request\Types as RequestTypes;
use Cap\Rma\Model\Request;
use Cap\Rma\Model\ResourceModel\Request\Collection as RequestCollection;
use Cap\Rma\Model\ResourceModel\Request\CollectionFactory as RequestCollectionFactory;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\View\Element\Template;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\ResourceModel\Order\Collection as OrderCollection;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory as OrdersCollectionFactory;

class Dashboard extends Template
{
    /**
     * @var CustomerSession
     */
    protected $customerSession;

    /**
     * @var RequestCollectionFactory
     */
    protected $requestCollectionFactory;

    /**
     * @var Data
     */
    protected $helper;

    /**
     * @var RequestTypes
     */
    protected $requestTypes;

    /**
     * @var OrdersCollectionFactory
     */
    protected $orderCollectionFactory;

    /**
     * Dashboard constructor.
     *
     * @param Template\Context $context
     * @param CustomerSession $customerSession
     * @param RequestCollectionFactory $requestCollectionFactory
     * @param Data $helper
     * @param RequestTypes $requestTypes
     * @param OrdersCollectionFactory $ordersCollectionFactory
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        CustomerSession $customerSession,
        RequestCollectionFactory $requestCollectionFactory,
        Data $helper,
        RequestTypes $requestTypes,
        OrdersCollectionFactory $ordersCollectionFactory,
        array $data = []
    ) {
        $this->customerSession = $customerSession;
        $this->requestCollectionFactory = $requestCollectionFactory;
        $this->helper = $helper;
        $this->requestTypes = $requestTypes;
        $this->orderCollectionFactory = $ordersCollectionFactory;
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
     * @return RequestCollection
     */
    public function getCustomerRequests()
    {
        $customerData = $this->customerSession->getCustomerData();
        $email = $customerData->getEmail();
        return $this->requestCollectionFactory->create()->addFieldToSelect(
            '*'
        )->addFieldToFilter(
            'customer_email',
            $email
        )->setOrder(
            'created_at',
            'desc'
        );
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

    /**
     * Form action to send data in Form Controller.
     *
     * @return string
     */
    public function getFormAction()
    {
        return '/rma/customer/form';
    }

    //todo add empty value to disable request or config enable/disable type

    /**
     * @return array
     */
    public function getConfigTypesValue()
    {
        $options = $this->helper->getConfigRequestTypes();
        $options = explode(',', $options);
        $values = $this->requestTypes->toOptionArray();

        $types = [];
        foreach ($values as $key => $value) {
            if (in_array($key, $options)) {
                $types[] = $value['label']->getText();
            }
        }
        return $types;
    }

    /**
     * Returns customer orders with config status filter
     *
     * @return OrderCollection
     */
    public function getCustomerOrdersCanRequest()
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
     * @param Order $order
     * @return string
     */
    public function getOrderLink(Order $order)
    {
        $orderId = $order->getId();
        return $this->_urlBuilder->getDirectUrl('sales/order/view/order_id/' . $orderId);
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
}
