<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

/**
 * Used in creating options for Add|Crop config value selection
 *
 */

namespace MageWorx\XmlSitemap\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class AddCrop implements ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [['value' => 1, 'label' => __('Add')], ['value' => 0, 'label' => __('Crop')]];
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return [0 => __('Crop'), 1 => __('Add')];
    }
}
