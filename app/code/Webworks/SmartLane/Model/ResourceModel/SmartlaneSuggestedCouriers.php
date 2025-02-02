<?php
/**
 * Created by PhpStorm.
 * User: waqas
 * Date: 8/25/20
 * Time: 10:34 AM
 */

namespace Webworks\SmartLane\Model\ResourceModel;


class SmartlaneSuggestedCouriers extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context
    )
    {
        parent::__construct($context);
    }

    protected function _construct()
    {
        $this->_init('smartlane_suggested_couriers', 'id');
    }

}