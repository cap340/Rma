<?php

namespace Cap\Rma\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Sales\Model\Order;
use Magento\Ui\Component\Listing\Columns\Column;

class Customer extends Column
{
    /**
     * @var Order
     */
    protected $order;

    /**
     * Customer constructor.
     *
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param Order $order
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        Order $order,
        array $components = [],
        array $data = []
    ) {
        $this->order = $order;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * {@inheritdoc}
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                if (isset($item['customer_name'])) {
                    $customerId = $this->getCustomerId($item);
                    $item['customer_name_url'] = $this->getLink($customerId);
                }
            }
        }

        return $dataSource;
    }

    /**
     * @param $item
     * @return int|null
     */
    private function getCustomerId($item)
    {
        $orderId = $item['order_id'];
        $order = $this->order->loadByAttribute('entity_id', $orderId);

        return $order->getCustomerId();
    }

    /**
     * @param $entityId
     * @return string
     */
    private function getLink($entityId)
    {
        return $this->context->getUrl('customer/index/edit', ['id' => $entityId]);
    }
}
