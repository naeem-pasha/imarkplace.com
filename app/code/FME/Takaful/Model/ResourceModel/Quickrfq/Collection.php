<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace FME\Takaful\Model\ResourceModel\Quickrfq;

use FME\Takaful\Model\ResourceModel\AbstractCollection;

/**
 * CMS page collection
 */
class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'takaful_id';

    
    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('FME\Takaful\Model\Quickrfq', 'FME\Takaful\Model\ResourceModel\Quickrfq');
        $this->_map['fields']['takaful_id'] = 'main_table.takaful_id';
    }

    
    public function addStoreFilter($store, $withAdmin = true)
    {
        return $this;
    }
}
