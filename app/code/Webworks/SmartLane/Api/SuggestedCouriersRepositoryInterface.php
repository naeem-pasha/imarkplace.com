<?php
namespace Webworks\SmartLane\Api;

interface SuggestedCouriersRepositoryInterface
{


    /**
     *
     * Create Suggested Couriers for bulk actions with smartlane API
     *
     * @api
     *
     * @param mixed $data
     * @return mixed
     **/

    public function createSuggestedCouriers($data);

}