<?php
/**
 * Webkul Odoomarketplaceconnect Collection
 * @category  Webkul
 * @package   Webkul_Odoomarketplaceconnect
 * @author    Webkul
 * @copyright Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\Odoomarketplaceconnect\Model\ResourceModel\Order;

use \Webkul\Odoomagentoconnect\Model\ResourceModel\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            'Webkul\Odoomarketplaceconnect\Model\Order',
            'Webkul\Odoomarketplaceconnect\Model\ResourceModel\Order'
        );
        $this->_map['fields']['entity_id'] = 'main_table.entity_id';
    }
}
