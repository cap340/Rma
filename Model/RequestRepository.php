<?php

namespace Cap\Rma\Model;

use Cap\Rma\Api\Data\RequestInterface;
use Cap\Rma\Api\Data\RequestInterfaceFactory;
use Cap\Rma\Api\Data\RequestSearchResultsInterfaceFactory;
use Cap\Rma\Api\RequestRepositoryInterface;
use Cap\Rma\Model\ResourceModel\Request as ResourceRequest;
use Cap\Rma\Model\ResourceModel\Request\CollectionFactory as RequestCollectionFactory;
use Exception;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Store\Model\StoreManagerInterface;

class RequestRepository implements RequestRepositoryInterface
{
    /**
     * @var ResourceRequest
     */
    protected $resource;

    /**
     * @var RequestFactory
     */
    protected $requestFactory;

    /**
     * @var RequestCollectionFactory
     */
    protected $requestCollectionFactory;

    /**
     * @var RequestSearchResultsInterfaceFactory
     */
    protected $searchResultsFactory;

    /**
     * @var DataObjectHelper
     */
    protected $dataObjectHelper;

    /**
     * @var RequestInterfaceFactory
     */
    protected $dataRequestFactory;

    /**
     * @var DataObjectProcessor
     */
    protected $dataObjectProcessor;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * RequestRepository constructor.
     *
     * @param ResourceRequest $resource
     * @param RequestFactory $requestFactory
     * @param RequestInterfaceFactory $dataRequestFactory
     * @param RequestCollectionFactory $requestCollectionFactory
     * @param RequestSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        ResourceRequest $resource,
        RequestFactory $requestFactory,
        RequestInterfaceFactory $dataRequestFactory,
        RequestCollectionFactory $requestCollectionFactory,
        RequestSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager
    ) {
        $this->resource = $resource;
        $this->requestFactory = $requestFactory;
        $this->requestCollectionFactory = $requestCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataRequestFactory = $dataRequestFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
    }

    /**
     * {@inheritdoc}
     */
    public function save(
        RequestInterface $request
    ) {
        /* if (empty($request->getStoreId())) {
            $storeId = $this->storeManager->getStore()->getId();
            $request->setStoreId($storeId);
        } */
        try {
            $this->resource->save($request);
        } catch (Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the request: %1',
                $exception->getMessage()
            ));
        }
        return $request;
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        SearchCriteriaInterface $criteria
    ) {
        $collection = $this->requestCollectionFactory->create();
        foreach ($criteria->getFilterGroups() as $filterGroup) {
            $fields = [];
            $conditions = [];
            foreach ($filterGroup->getFilters() as $filter) {
                if ($filter->getField() === 'store_id') {
                    $collection->addStoreFilter($filter->getValue(), false);
                    continue;
                }
                $fields[] = $filter->getField();
                $condition = $filter->getConditionType() ?: 'eq';
                $conditions[] = [$condition => $filter->getValue()];
            }
            $collection->addFieldToFilter($fields, $conditions);
        }

        $sortOrders = $criteria->getSortOrders();
        if ($sortOrders) {
            /** @var SortOrder $sortOrder */
            foreach ($sortOrders as $sortOrder) {
                $collection->addOrder(
                    $sortOrder->getField(),
                    ($sortOrder->getDirection() == SortOrder::SORT_ASC) ? 'ASC' : 'DESC'
                );
            }
        }
        $collection->setCurPage($criteria->getCurrentPage());
        $collection->setPageSize($criteria->getPageSize());

        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);
        $searchResults->setTotalCount($collection->getSize());
        $searchResults->setItems($collection->getItems());
        return $searchResults;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($requestId)
    {
        return $this->delete($this->getById($requestId));
    }

    /**
     * {@inheritdoc}
     */
    public function delete(
        RequestInterface $request
    ) {
        try {
            $this->resource->delete($request);
        } catch (Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the Request: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getById($requestId)
    {
        $request = $this->requestFactory->create();
        $this->resource->load($request, $requestId);
        if (!$request->getId()) {
            throw new NoSuchEntityException(__('Request with id "%1" does not exist.', $requestId));
        }
        return $request;
    }
}
