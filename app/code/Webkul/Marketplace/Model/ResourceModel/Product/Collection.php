<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_Marketplace
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\Marketplace\Model\ResourceModel\Product;

use \Webkul\Marketplace\Model\ResourceModel\AbstractCollection;

/**
 * Webkul Marketplace ResourceModel product collection
 */
class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'entity_id';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \Webkul\Marketplace\Model\Product::class,
            \Webkul\Marketplace\Model\ResourceModel\Product::class
        );
        $this->_map['fields']['entity_id'] = 'main_table.entity_id';
    }

    /**
     * Retrieve clear select
     *
     * @return \Magento\Framework\DB\Select
     */
    protected function _getClearSelect()
    {
        return $this->_buildClearSelect();
    }

    /**
     * Add filter by store for seller's products
     *
     * @param int|array|\Magento\Store\Model\Store $store
     * @param bool $withAdmin
     * @return $this
     */
    public function addStoreFilter($store, $withAdmin = true)
    {
        if (!$this->getFlag('store_filter_added')) {
            $this->performAddStoreFilter($store, $withAdmin);
        }
        return $this;
    }

    /**
     * Build clear select
     *
     * @param \Magento\Framework\DB\Select $select
     * @return \Magento\Framework\DB\Select
     */
    protected function _buildClearSelect($select = null)
    {
        if (null === $select) {
            $select = clone $this->getSelect();
        }
        $select->reset(
            \Magento\Framework\DB\Select::ORDER
        );
        $select->reset(
            \Magento\Framework\DB\Select::LIMIT_COUNT
        );
        $select->reset(
            \Magento\Framework\DB\Select::LIMIT_OFFSET
        );
        $select->reset(
            \Magento\Framework\DB\Select::COLUMNS
        );

        return $select;
    }

    /**
     * Retrieve all  Assign Products for collection
     *
     * @param int|string $limit
     * @param int|string $offset
     * @return array
     */
    public function getAllAssignProducts($condition, $limit = null, $offset = null)
    {
        $idsSelect = $this->_getClearSelect();
        $idsSelect->columns('mageproduct_id');
        $idsSelect->where($condition);
        $idsSelect->limit($limit, $offset);
        $idsSelect->resetJoinLeft();

        return $this->getConnection()->fetchCol($idsSelect, $this->_bindParams);
    }
    public function getAllProducts($limit = null, $offset = null) {
         $idsSelect = $this->_getClearSelect();
         $idsSelect->columns('mageproduct_id');
         $idsSelect->limit($limit, $offset);
         $idsSelect->resetJoinLeft();
 
         return $this->getConnection()->fetchCol($idsSelect, $this->_bindParams);
     }

    /**
     * Retrieve all mageproduct_id for collection
     *
     * @param int|string $limit
     * @param int|string $offset
     * @return array
     */
    public function getAllIds($limit = null, $offset = null)
    {
        $idsSelect = $this->_getClearSelect();
        $idsSelect->columns('mageproduct_id');
        $idsSelect->limit($limit, $offset);
        $idsSelect->resetJoinLeft();

        return $this->getConnection()->fetchCol($idsSelect, $this->_bindParams);
    }

    /**
     * Retrieve all seller_id for collection
     *
     * @param int|string $limit
     * @param int|string $offset
     * @return array
     */
    public function getAllSellerIds($limit = null, $offset = null)
    {
        $idsSelect = $this->_getClearSelect();
        $idsSelect->columns('seller_id');
        $idsSelect->limit($limit, $offset);
        $idsSelect->resetJoinLeft();

        return $this->getConnection()->fetchCol($idsSelect, $this->_bindParams);
    }

    /**
     * Set product data for given condition
     *
     * @param array $condition
     * @param array $attributeData
     * @return void
     */
    public function setProductData($condition, $attributeData)
    {
        return $this->getConnection()->update(
            $this->getTable('marketplace_product'),
            $attributeData,
            $where = $condition
        );
    }

    /**
     * Join with Customer Grid Flat Table
     */
    public function joinCustomer()
    {
        $joinTable = $this->getTable('customer_grid_flat');
        $this->getSelect()->join($joinTable.' as cgf', 'main_table.seller_id = cgf.entity_id');
    }

    /**
     * Add Colums from userdata Table
     */
    public function addSellerColumns()
    {
        if (empty($this->mappedFields)) {
            $this->mappedFields = ["shop_url", "shop_title"];
        }

        $this->addStoreWiseSellerColumns();
    }

    /**
     * Join with Product Table
     */
    public function joinProductTable()
    {
        $productTable = $this->getTable('catalog_product_entity');
        $fields = ['entity_id', 'count(main_product.entity_id) as count'];
        $this->getSelect()->joinRight(
            $productTable.' as main_product',
            'main_table.mageproduct_id = main_product.entity_id',
            $fields
        );
    }
}
