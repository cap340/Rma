<?php

namespace Cap\Rma\Model\Config\Source\Request;

use Magento\Framework\Data\OptionSourceInterface;

class Types implements OptionSourceInterface
{
    /**
     * Types of request
     */
    const TYPE_REFUND = 0;
    const TYPE_EXCHANGE = 1;
    const TYPE_WARRANTY = 2;

    /**
     * To Option Array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::TYPE_REFUND, 'label' => __('Refund')],
            ['value' => self::TYPE_EXCHANGE, 'label' => __('Exchange')],
            ['value' => self::TYPE_WARRANTY, 'label' => __('Warranty')]
        ];
    }
}
