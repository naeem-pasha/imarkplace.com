<?php
namespace Webworks\SmartLane\Model;

use Psr\Log\LoggerInterface;
use Webworks\SmartLane\Helper\Data;

class SmartLaneOrderStatusRepository
{
    /**
     * @var LoggerInterface
    */

    protected $logger;
    /**
     * @var Data
    */
    protected $smartHelper;
    /**
     * @var OrderFactory
    */


    public function __construct(
        LoggerInterface $logger,
        \Webworks\SmartLane\Helper\Data $smartHelper
    )
    {
        $this->logger = $logger;
        $this->smartHelper = $smartHelper;
    }

    /**
     * @param $order
     * @param $status
     * @return string
     */
    public function processOrderStatus($order, $status)
    {
        $statusList = $this->smartHelper->smartLaneStatusList();
        $statusList = array_flip($statusList);
        //create the method
        $method = $this->smartHelper->strToCamelCase($status, '_', true);
        $orderStatusMethod = 'process'.$method.'Status';
        $payload = array(
            'store_order_id' => $order->getIncrementId(),
            'shipment_status' => $statusList[$status]

        );

        $response =  $this->smartHelper->postOrderToSmartlane($payload);
        $content = json_decode($response);
        if(method_exists('\Webworks\SmartLane\Model\SmartLaneOrderStatusRepository', $orderStatusMethod) && $content && $content->code ==200){
            $this->$orderStatusMethod($order);
        }

        return $response;
    }

    public function processSmartlaneCancelStatus($order){
        //delete the shipments
        if($order->getShipmentsCollection()->count()){
            $shipments = $order->getShipmentsCollection();
            foreach ($shipments as $shipment){
                $shipment->delete();
            }
        }
    }

}