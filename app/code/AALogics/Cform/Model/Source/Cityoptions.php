<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace AALogics\Cform\Model\Source;

/**
 * @api
 * @since 100.0.2
 */
class Cityoptions implements \Magento\Framework\Option\ArrayInterface
{
    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource
    ) {
        $this->_resource = $resource;
    }

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $optionArray = [];
         foreach ($this->getCities() as $field) {
            $options[] = ['label' => $field['default_name'], 'value' => $field['code']];
        }
        return $options;
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return [0 => __('No'), 1 => __('Yes')];
    }
    public function getCities()
    {
        $adapter = $this->_resource->getConnection(\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION);
        $select = $adapter->select()
        ->from('pakistan_cities')->order('default_name');
        return $adapter->fetchAll($select);
    }
}