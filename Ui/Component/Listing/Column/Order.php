<?php

namespace Cap\Rma\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Ui\Component\Listing\Columns\Column;

class Order extends Column
{
    /**
     * @var OrderInterface
     */
    protected $orderInterface;

    /**
     * Customer constructor.
     *
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param OrderInterface $orderInterface
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        OrderInterface $orderInterface,
        array $components = [],
        array $data = []
    ) {
        $this->orderInterface = $orderInterface;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                $entityId = $this->getEntityId($item);
                $item['increment_id_url'] = $this->getLink($entityId);
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

    /**
     * @param $item
     * @return mixed
     */
    public function getEntityId($item)
    {
        $incrementId = $item['increment_id'];
        return $this->orderInterface->loadByIncrementId($incrementId)->getEntityId();
    }
}
