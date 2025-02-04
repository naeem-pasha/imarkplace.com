<?php
namespace Webworks\SmartLane\ViewModel;

use Psr\Log\LoggerInterface;
use Magento\Framework\App\Request\Http;

class TrackingInfo implements \Magento\Framework\View\Element\Block\ArgumentInterface
{
    protected $logger;
    protected $request;
    protected $order;

    public function __construct(
        LoggerInterface $logger,
        Http $request,
        \Magento\Sales\Api\OrderRepositoryInterface $order
    ) {
        $this->logger = $logger;
        $this->request = $request;
        $this->order = $order;
    }
    public function getTrackingInfo()
    {
        $order = $this->order->get($this->request->getParam('order_id'));
        $courierCNDetails = $order->getData('smartlane_courier_tracking_info') ?? null;
        if($courierCNDetails!=null){
            $courierInfo = json_decode($courierCNDetails,  true);
            $trackingInfo = [
                'courier_name' => strtoupper($courierInfo['courierName']) ?? null,
                'cn' => $courierInfo['courierCN'] ?? null,
                'sl_tracking' => $courierInfo['sl_tracking'] ?? null,
                'courier_remarks' => $courierInfo['courierRemarks'] ?? null
            ];

            return $trackingInfo;
        }
    }

}