<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace MageBig\FastCheckout\Model\Config\Source;

/**
 * @api
 * @since 100.0.2
 */
class Noreq implements \Magento\Framework\Option\ArrayInterface
{
    public const VALUE_NO = '';
    public const VALUE_REQUIRED = 'req';

    /**
     * Convert option to array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::VALUE_NO, 'label' => __('No')],
            ['value' => self::VALUE_REQUIRED, 'label' => __('Yes')]
        ];
    }
}
