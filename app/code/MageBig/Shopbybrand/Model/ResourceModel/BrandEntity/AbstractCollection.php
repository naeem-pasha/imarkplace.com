<?php
/**
 * Copyright © 2020 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace MageBig\Shopbybrand\Model\ResourceModel\BrandEntity;

/**
 * Catalog EAV collection resource abstract model
 * Implement using different stores for retrieve attribute values
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class AbstractCollection extends \Magento\Eav\Model\Entity\Collection\AbstractCollection
{
    /**
     * Current scope (store Id)
     *
     * @var int
     */
    protected $_storeId;

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @param \Magento\Framework\Data\Collection\EntityFactory $entityFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \Magento\Framework\App\ResourceConnection $resource
     * @param \Magento\Eav\Model\EntityFactory $eavEntityFactory
     * @param \Magento\Eav\Model\ResourceModel\Helper $resourceHelper
     * @param \Magento\Framework\Validator\UniversalFactory $universalFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\DB\Adapter\AdapterInterface $connection
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactory $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Eav\Model\EntityFactory $eavEntityFactory,
        \Magento\Eav\Model\ResourceModel\Helper $resourceHelper,
        \Magento\Framework\Validator\UniversalFactory $universalFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null
    ) {
        $this->_storeManager = $storeManager;
        parent::__construct(
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $eavConfig,
            $resource,
            $eavEntityFactory,
            $resourceHelper,
            $universalFactory,
            $connection
        );
    }

    /**
     * Set store scope
     *
     * @param int|string|\Magento\Store\Model\Store $store
     * @return $this
     */
    public function setStore($store)
    {
        $this->setStoreId($this->_storeManager->getStore($store)->getId());
        return $this;
    }

    /**
     * Set store scope
     *
     * @param int|string|\Magento\Store\Model\Store $storeId
     * @return $this
     */
    public function setStoreId($storeId)
    {
        if ($storeId instanceof \Magento\Store\Model\Store) {
            $storeId = $storeId->getId();
        }
        $this->_storeId = (int)$storeId;
        return $this;
    }

    /**
     * Return current store id
     *
     * @return int
     */
    public function getStoreId()
    {
        if ($this->_storeId === null) {
            $this->setStoreId($this->_storeManager->getStore()->getId());
        }
        return $this->_storeId;
    }

    /**
     * Retrieve default store id
     *
     * @return int
     */
    public function getDefaultStoreId()
    {
        return \Magento\Store\Model\Store::DEFAULT_STORE_ID;
    }

    /**
     * Retrieve attributes load select
     *
     * @param string $table
     * @param array|int $attributeIds
     * @return \Magento\Eav\Model\Entity\Collection\AbstractCollection
     */
    protected function _getLoadAttributesSelect($table, $attributeIds = [])
    {
        if (empty($attributeIds)) {
            $attributeIds = $this->_selectAttributes;
        }
        $storeId = $this->getStoreId();
        $connection = $this->getConnection();
        $entityIdField = $this->getEntity()->getEntityIdField();

        if ($storeId) {
            $joinCondition = [
                't_s.attribute_id = t_d.attribute_id',
                't_s.entity_id = t_d.entity_id',
                $connection->quoteInto('t_s.store_id = ?', $storeId),
            ];

            $select = $connection->select()->from(
                ['t_d' => $table],
                [$entityIdField, 'attribute_id']
            )->where(
                "t_d.{$entityIdField} IN (?)",
                array_keys($this->_itemsById)
            )->where(
                't_d.attribute_id IN (?)',
                $attributeIds
            )->joinLeft(
                ['t_s' => $table],
                implode(' AND ', $joinCondition),
                []
            )->where(
                't_d.store_id = ?',
                $connection->getIfNullSql('t_s.store_id', \Magento\Store\Model\Store::DEFAULT_STORE_ID)
            );
        } else {
            $select = $connection->select()->from(
                $table,
                [$entityIdField, 'attribute_id']
            )->where(
                "{$entityIdField} IN (?)",
                array_keys($this->_itemsById)
            )->where(
                'attribute_id IN (?)',
                $attributeIds
            )->where(
                'store_id = ?',
                $this->getDefaultStoreId()
            );
        }
        return $select;
    }

    /**
     * @param \Magento\Framework\DB\Select $select
     * @param string $table
     * @param string $type
     * @return \Magento\Framework\DB\Select
     */
    protected function _addLoadAttributesSelectValues($select, $table, $type)
    {
        $storeId = $this->getStoreId();
        if ($storeId) {
            $connection = $this->getConnection();
            $valueExpr = $connection->getCheckSql('t_s.value_id IS NULL', 't_d.value', 't_s.value');

            $select->columns(
                ['default_value' => 't_d.value', 'store_value' => 't_s.value', 'value' => $valueExpr]
            );
        } else {
            $select = parent::_addLoadAttributesSelectValues($select, $table, $type);
        }
        return $select;
    }

    /**
     * Adding join statement to collection select instance
     *
     * @param string $method
     * @param object $attribute
     * @param string $tableAlias
     * @param array $condition
     * @param string $fieldCode
     * @param string $fieldAlias
     * @return \Magento\Eav\Model\Entity\Collection\AbstractCollection
     */
    protected function _joinAttributeToSelect($method, $attribute, $tableAlias, $condition, $fieldCode, $fieldAlias)
    {
        if (isset($this->_joinAttributes[$fieldCode]['store_id'])) {
            $storeId = $this->_joinAttributes[$fieldCode]['store_id'];
        } else {
            $storeId = $this->getStoreId();
        }

        $connection = $this->getConnection();

        if ($storeId != $this->getDefaultStoreId() && !$attribute->isScopeGlobal()) {
            /**
             * Add joining default value for not default store
             * if value for store is null - we use default value
             */
            $defCondition = '(' . implode(') AND (', $condition) . ')';
            $defAlias = $tableAlias . '_default';
            $defAlias = $this->getConnection()->getTableName($defAlias);
            $defFieldAlias = str_replace($tableAlias, $defAlias, $fieldAlias);
            $tableAlias = $this->getConnection()->getTableName($tableAlias);

            $defCondition = str_replace($tableAlias, $defAlias, $defCondition);
            $defCondition .= $connection->quoteInto(
                " AND " . $connection->quoteColumnAs("{$defAlias}.store_id", null) . " = ?",
                $this->getDefaultStoreId()
            );

            $this->getSelect()->{$method}(
                [$defAlias => $attribute->getBackend()->getTable()],
                $defCondition,
                []
            );

            $method = 'joinLeft';
            $fieldAlias = $this->getConnection()->getCheckSql(
                "{$tableAlias}.value_id > 0",
                $fieldAlias,
                $defFieldAlias
            );
            $this->_joinAttributes[$fieldCode]['condition_alias'] = $fieldAlias;
            $this->_joinAttributes[$fieldCode]['attribute'] = $attribute;
        } else {
            $storeId = $this->getDefaultStoreId();
        }
        $condition[] = $connection->quoteInto(
            $connection->quoteColumnAs("{$tableAlias}.store_id", null) . ' = ?',
            $storeId
        );
        return parent::_joinAttributeToSelect($method, $attribute, $tableAlias, $condition, $fieldCode, $fieldAlias);
    }
}
