<?php
/**
 * Created by PhpStorm.
 * User: waqas
 * Date: 8/25/20
 * Time: 10:34 AM
 */

namespace Webworks\SmartLane\Model\ResourceModel\SmartlaneSuggestedCouriers;


class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_idFieldName = 'id';
    protected $_eventPrefix = 'smartlane_suggested_couriers_collection';
    protected $_eventObject = 'couriers_collection';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Webworks\SmartLane\Model\SmartlaneSuggestedCouriers', 'Webworks\SmartLane\Model\ResourceModel\SmartlaneSuggestedCouriers');
    }


    /**
     * @param $condition
     * @param $columnData
     * @return int
     */
    public function setTableRecords($condition, $columnData)
    {
        return $this->getConnection()->update(
            $this->getTable('smartlane_suggested_couriers'),
            $columnData,
            $where = $condition
        );
    }

}
