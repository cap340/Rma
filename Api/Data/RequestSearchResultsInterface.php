<?php

namespace Cap\Rma\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

interface RequestSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get Request list.
     *
     * @return RequestInterface[]
     */
    public function getItems();

    /**
     * Set increment_id list.
     *
     * @param RequestInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
