<?php
/**
 * Created by PhpStorm.
 * User: waqas
 * Date: 8/25/20
 * Time: 10:34 AM
 */

namespace Webworks\SmartLane\Model;


class SmartlaneSuggestedCouriers extends \Magento\Framework\Model\AbstractModel
{

    const CACHE_TAG = 'smartlane_suggested_couriers';

    protected $_cacheTag = 'smartlane_suggested_couriers';

    protected $_eventPrefix = 'smartlane_suggested_couriers';

    protected function _construct()
    {
        $this->_init('Webworks\SmartLane\Model\ResourceModel\SmartlaneSuggestedCouriers');
    }

}