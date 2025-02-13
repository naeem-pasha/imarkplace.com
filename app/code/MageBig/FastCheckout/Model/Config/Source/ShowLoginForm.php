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
class ShowLoginForm implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * Show option
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 0, 'label' => __('Hide')],
            ['value' => 1, 'label' => __('Show')],
            ['value' => 2, 'label' => __('Auto')],
            ['value' => 3, 'label' => __('Click info icon to show Sign in popup')]
        ];
    }
}
