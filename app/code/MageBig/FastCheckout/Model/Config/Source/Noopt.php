<?php
/**
 * Copyright © www.magebig.com - All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageBig\FastCheckout\Model\Config\Source;

/**
 * @api
 * @since 100.0.2
 */
class Noopt implements \Magento\Framework\Option\ArrayInterface
{
    public const VALUE_NO = '';
    public const VALUE_OPTIONAL = 'opt';

    /**
     * Convert option arry
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::VALUE_NO, 'label' => __('No')],
            ['value' => self::VALUE_OPTIONAL, 'label' => __('Yes')]
        ];
    }
}
