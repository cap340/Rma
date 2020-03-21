<?php

namespace Cap\Rma\Api;

use Cap\Rma\Api\Data\RequestInterface;
use Cap\Rma\Api\Data\RequestSearchResultsInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

interface RequestRepositoryInterface
{
    /**
     * Save Request
     *
     * @param RequestInterface $request
     * @return RequestInterface
     * @throws LocalizedException
     */
    public function save(
        RequestInterface $request
    );

    /**
     * Retrieve Request
     *
     * @param string $requestId
     * @return RequestInterface
     * @throws LocalizedException
     */
    public function getById($requestId);

    /**
     * Retrieve Request matching the specified criteria.
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return RequestSearchResultsInterface
     * @throws LocalizedException
     */
    public function getList(
        SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete Request
     *
     * @param RequestInterface $request
     * @return bool true on success
     * @throws LocalizedException
     */
    public function delete(
        RequestInterface $request
    );

    /**
     * Delete Request by ID
     *
     * @param string $requestId
     * @return bool true on success
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function deleteById($requestId);
}
