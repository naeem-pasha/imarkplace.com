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
class Layout implements \Magento\Framework\Option\ArrayInterface
{

    /**
     * Option array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 0, 'label' => __('2 columns - default')],
            ['value' => 2, 'label' => __('2 columns - sidebar background')],
            ['value' => 1, 'label' => __('1 column')]
        ];
    }
}
