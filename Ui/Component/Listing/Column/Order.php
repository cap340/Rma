<?php

namespace Cap\Rma\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Sales\Model\Order as ModelOrder;
use Magento\Ui\Component\Listing\Columns\Column;

class Order extends Column
{
    /**
     * @var ModelOrder
     */
    protected $order;

    /**
     * Customer constructor.
     *
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param ModelOrder $order
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        ModelOrder $order,
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
            foreach ($dataSource['data']['items'] as &$item) {
                $entityId = $item['order_id'];
                $item['order_id_url'] = $this->getLink($entityId);
            }
        }

        return $dataSource;
    }

    /**
     * @param $entityId
     * @return string
     */
    private function getLink($entityId)
    {
        return $this->context->getUrl('sales/order/view', ['order_id' => $entityId]);
    }
}
