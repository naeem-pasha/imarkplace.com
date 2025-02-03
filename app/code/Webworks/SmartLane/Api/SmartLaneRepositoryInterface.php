<?php
namespace Webworks\SmartLane\Api;

interface SmartLaneRepositoryInterface
{

    /**
     * Update Order Status with SmartLane API
     *
     * @api
     *
     * @param string $orderid
     *
     * @param string $status
     *
     * @param mixed $cndetails
     *
     * @param mixed $couriercndetails
     *
     * @return mixed
     **/

    public function updateOrderStatus($orderid,$status,$cndetails=null,$couriercndetails=null);
    /**
     * Verify Auth Keys are fine
     *
     * @api
     *
     * @param int $key
     *
     * @return void
     **/
    public function VerifyAuthKeys($key);

}