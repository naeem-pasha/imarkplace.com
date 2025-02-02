<?php

namespace Webworks\SmartLane\Observer;


use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Webworks\SmartLane\Helper\Data;

class Withdraworder implements ObserverInterface
{

    protected $order;

    protected $logger;

    protected $smarthelper;

    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Magento\Sales\Api\Data\OrderInterface $order,
        \Webworks\SmartLane\Helper\Data $smarthelper
    ) {
     $this->order = $order;
     $this->logger = $logger;
     $this->smarthelper = $smarthelper;

 }

 public function execute(Observer $observer)
 {


    $order= $observer->getData('order');
    // $forder= $this->order->create();
    // if($order->getStatus=='smartlane_cancel')
    // {
    //     $this->logger->info('Observer Called for Order'.$order->getIncrementId());
    //     $apicall = $this->smarthelper->cancelrdersonSL($order->getIncrementId());

    //     $this->logger->info($apicall);
        
    // }
    return $this;

}
}