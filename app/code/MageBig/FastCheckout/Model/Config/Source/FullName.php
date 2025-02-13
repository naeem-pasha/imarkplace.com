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
class FullName implements \Magento\Framework\Option\ArrayInterface
{

    /**
     * Option array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 0, 'label' => __('No')],
            ['value' => 1, 'label' => __('Full Name - First Name is the last word')],
            ['value' => 2, 'label' => __('Full Name - First Name is the first word')]
        ];
    }
}
