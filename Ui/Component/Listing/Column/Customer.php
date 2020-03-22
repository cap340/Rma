<?php

namespace Cap\Rma\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Ui\Component\Listing\Columns\Column;

class Customer extends Column
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
        $incrementId = $item['increment_id'];
        return $this->orderInterface->loadByIncrementId($incrementId)->getCustomerId();
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
