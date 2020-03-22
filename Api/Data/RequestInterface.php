<?php

namespace Cap\Rma\Api\Data;

interface RequestInterface
{
    const REQUEST_ID = 'request_id';
    const INCREMENT_ID = 'increment_id';
    const CUSTOMER_NAME = 'customer_name';
    const CUSTOMER_EMAIL = 'customer_email';
    const TYPE = 'type';
    const STATUS = 'status';
    const DESCRIPTION = 'description';
    const COMMENT = 'comment';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    /**
     * Get request_id
     *
     * @return string|null
     */
    public function getRequestId();

    /**
     * Set request_id
     *
     * @param string $requestId
     * @return RequestInterface
     */
    public function setRequestId($requestId);

    /**
     * Get increment_id
     *
     * @return string|null
     */
    public function getIncrementId();

    /**
     * Set increment_id
     *
     * @param string $incrementId
     * @return RequestInterface
     */
    public function setIncrementId($incrementId);

    /**
     * Get customer_name
     *
     * @return string|null
     */
    public function getCustomerName();

    /**
     * Set customer_name
     *
     * @param string $customerName
     * @return RequestInterface
     */
    public function setCustomerName($customerName);

    /**
     * Get customer_email
     *
     * @return string|null
     */
    public function getCustomerEmail();

    /**
     * Set customer_email
     *
     * @param string $customerEmail
     * @return RequestInterface
     */
    public function setCustomerEmail($customerEmail);

    /**
     * Get type
     *
     * @return string|null
     */
    public function getType();

    /**
     * Set type
     *
     * @param string $type
     * @return RequestInterface
     */
    public function setType($type);

    /**
     * Get status
     *
     * @return string|null
     */
    public function getStatus();

    /**
     * Set status
     *
     * @param string $status
     * @return RequestInterface
     */
    public function setStatus($status);

    /**
     * Get description
     *
     * @return string|null
     */
    public function getDescription();

    /**
     * Set description
     *
     * @param string $description
     * @return RequestInterface
     */
    public function setDescription($description);

    /**
     * Get comment
     *
     * @return string|null
     */
    public function getComment();

    /**
     * Set comment
     *
     * @param string $comment
     * @return RequestInterface
     */
    public function setComment($comment);

    /**
     * Get created_at
     *
     * @return string|null
     */
    public function getCreatedAt();

    /**
     * Set created_at
     *
     * @param string $createdAt
     * @return RequestInterface
     */
    public function setCreatedAt($createdAt);

    /**
     * Get updated_at
     *
     * @return string|null
     */
    public function getUpdatedAt();

    /**
     * Set updated_at
     *
     * @param string $updatedAt
     * @return RequestInterface
     */
    public function setUpdatedAt($updatedAt);
}
