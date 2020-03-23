<?php

namespace Cap\Rma\Model\Config\Source\Request;

use Magento\Framework\Data\OptionSourceInterface;

class Status implements OptionSourceInterface
{
    /**
     * Types of request
     */
    const STATUS_PENDING = 0;
    const STATUS_ACCEPTED = 1;
    const STATUS_REJECTED = 2;

    /**
     * To Option Array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::STATUS_PENDING, 'label' => __('Pending')],
            ['value' => self::STATUS_ACCEPTED, 'label' => __('Accepted')],
            ['value' => self::STATUS_REJECTED, 'label' => __('Rejected')]
        ];
    }
}
