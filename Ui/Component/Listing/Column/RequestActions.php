<?php

namespace Cap\Rma\Ui\Component\Listing\Column;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

class RequestActions extends Column
{
    const URL_PATH_DELETE = 'cap_rma/request/delete';
    const URL_PATH_EDIT = 'cap_rma/request/edit';
    const URL_PATH_PRINT = 'cap_rma/request/pdf';

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * RequestActions constructor.
     *
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface $urlBuilder
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        array $components = [],
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                if (isset($item['request_id'])) {
                    $item[$this->getData('name')] = [
                        'print' => [
                            'href' => $this->urlBuilder->getUrl(
                                static::URL_PATH_PRINT,
                                [
                                    'request_id' => $item['request_id']
                                ]
                            ),
                            'label' => __('Print')
                        ],
                        'edit' => [
                            'href' => $this->urlBuilder->getUrl(
                                static::URL_PATH_EDIT,
                                [
                                    'request_id' => $item['request_id']
                                ]
                            ),
                            'label' => __('Edit')
                        ],
                        'delete' => [
                            'href' => $this->urlBuilder->getUrl(
                                static::URL_PATH_DELETE,
                                [
                                    'request_id' => $item['request_id']
                                ]
                            ),
                            'label' => __('Delete'),
                            'confirm' => [
                                'title' => __('Delete "${ $.$data.increment_id }"'),
                                'message' => __('Are you sure you wan\'t to delete "${ $.$data.increment_id }"?')
                            ]
                        ]
                    ];
                }
            }
        }

        return $dataSource;
    }
}
