<?php
namespace Webworks\SmartLane\Model;

use Psr\Log\LoggerInterface;
use Webworks\SmartLane\Helper\Data;
use Magento\Sales\Model\OrderFactory;

class SmartLaneRepository implements \Webworks\SmartLane\Api\SmartLaneRepositoryInterface
{
    /**
     * @var LoggerInterface
    */

    protected $logger;
    /**
     * @var Data
    */
    protected $smarthelper;
    /**
     * @var OrderFactory
    */
    protected $orderFactory;

   /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
   protected $_orderRepository;

    /**
     * @var \Magento\Sales\Model\Convert\Order
     */
    protected $_convertOrder;

    /**
     * @var \Magento\Shipping\Model\ShipmentNotifier
     */
    protected $_shipmentNotifier;
    /**
    * @var \Magento\Sales\Model\Order\Shipment\TrackFactory
    */
    protected $trackFactory;

    /**
     * @var \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor
     */
    protected $dataPersistor;


    /**
     * SmartLaneRepository constructor.
     * @param LoggerInterface $logger
     * @param Data $smarthelper
     * @param OrderFactory $orderFactory
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
     * @param \Magento\Sales\Model\Convert\Order $convertOrder
     * @param \Magento\Shipping\Model\ShipmentNotifier $shipmentNotifier
     * @param \Magento\Sales\Model\Order\Shipment\TrackFactory $trackFactory
     * @param \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor
     */
    public function __construct(
        LoggerInterface $logger,
        \Webworks\SmartLane\Helper\Data $smarthelper,
        OrderFactory $orderFactory,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Magento\Sales\Model\Convert\Order $convertOrder,
        \Magento\Shipping\Model\ShipmentNotifier $shipmentNotifier,
        \Magento\Sales\Model\Order\Shipment\TrackFactory $trackFactory,
        \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor
    )
    {
        $this->logger = $logger;
        $this->smarthelper = $smarthelper;
        $this->orderFactory = $orderFactory;
        $this->_orderRepository = $orderRepository;
        $this->_convertOrder = $convertOrder;
        $this->_shipmentNotifier = $shipmentNotifier;
        $this->trackFactory = $trackFactory;
        $this->dataPersistor = $dataPersistor;
    }

    public function updateOrderStatus($orderid,$status,$cndetails=null, $couriercndetails=null)
    {
        //clear datapersistor that we will set below
        $this->dataPersistor->clear('update_order_status_via_api');
        $return= array(
           'code' => '',
           'status' => '',
           'message' => ''
       );
        // Map SL and Magento Statuses
        $mstatus= $this->smarthelper->smartLaneStatusList($status);

        // Condition to check if Status exist in magento?
        if($mstatus=='Error')
        {
            $return['code']='422';
            $return['status']='Validation Error';
            $return['message'] = 'Undefined Order Status';
            return json_encode($return);

        }
        // Get respective State to move order State
        $mstate= $this->smarthelper->getRespectiveState($mstatus);
        $order = $this->orderFactory->create()->loadByIncrementId($orderid);
        if($order->getId()>1)
        {
            $trackingCN= isset($cndetails['trackingCN'])?$cndetails['trackingCN']:"smartCode";
            $trackingCR= $cndetails['trackingCR'];
            $trackingCRTitle= $cndetails['trackingCRTitle'];

            //delete shipments if exists on cancel the order
            if($status=='cancelled' && $order->getShipmentsCollection()->count())
            {
                $shipments = $order->getShipmentsCollection();
                foreach ($shipments as $shipment){
                    $shipment->delete();
                }

            }

            if($status=='ready_for_pickup')
            {
                $this->dataPersistor->set('update_order_status_via_api', true);
                $this->createshipment($order, $trackingCR, $trackingCRTitle, $trackingCN);
            }

            //create shipment in case of if something went wrong during "ready_for_pickup"
            if (!$order->getShipmentsCollection()->count() && $status!='ready_for_pickup' && $status!='cancelled') {
                $this->createshipment($order, $trackingCR, $trackingCRTitle, $trackingCN);
            }

            //update custom tracking information
            if(isset($couriercndetails['courierName']) && isset($couriercndetails['courierCN'])){
                $couriercndetails['sl_tracking'] = $trackingCN;
                $order->setSmartlaneCourierTrackingInfo(json_encode($couriercndetails));
            }
            //set the data persistor when call via api from smartlane
            $this->dataPersistor->set('update_order_status_via_api', true);
            $order->setState($mstate)->setStatus($mstatus)->addStatusToHistory($mstatus, 'Order Status has been changed Via SmartLane API');
            $order->save();

            $return['code']='200';
            $return['status']='Success';
            $return['message'] = 'Order Updated with status : '. $status;
        }
        else{
            $return['code']='404';
            $return['status']='Not Found';
            $return['message'] = 'Sorry Order could not found on Store. Please contact administrator';
        }
        return json_encode($return);
    }


    protected function createshipment($order, $shippingMethod, $title, $trackingNumber) {
        $shipments = $order->getShipmentsCollection();
        if ($shipments->getSize() == 0) {
            // Check if order can be shipped or has already shipped
            if (!$order->canShip()) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('You can\'t create an shipment for ' . $order->getIncrementId() . '.')
                );
            }
            $order = $this->_orderRepository->get($order->getId());
            $convertOrderM = $this->_convertOrder;
            $shipment = $convertOrderM->toShipment($order);

            // Loop through order items
            foreach ($order->getAllItems() AS $orderItem) {
                // Check if order item has qty to ship or is virtual
                if (!$orderItem->getQtyToShip() || $orderItem->getIsVirtual()) {
                    continue;
                }

                $qtyShipped = $orderItem->getQtyToShip();

                // Create shipment item with qty
                $shipmentItem = $convertOrderM->itemToShipmentItem($orderItem)->setQty($qtyShipped);
                // Add shipment item to shipment
                $shipment->addItem($shipmentItem);
            }
            //$this->logger->info('OrderNumber: ' . $order->getIncrementId() . ' Registering Shipment');
            $shipment->register();


            try {
                // Save created shipment and order
                $shippingMethod = $order->getShippingMethod();
                $data = array(
                    'carrier_code' => $shippingMethod,
                    'title' => $title,
                    'number' => $trackingNumber,
                );
                $track = $this->trackFactory->create()->addData($data);
                $shipment->addTrack($track);
                $shipment->save();
                $shipment->getOrder()->setIsInProcess(true);
                $order->setIsInProcess(true);
                $order->save();
            } catch (\Exception $e) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __($e->getMessage())
                );
            }
        }
    }

    public function VerifyAuthKeys($key)
    {
        $return= array('code'=>'200', 'status'=>'success','message'=>'You are successfully connected with Magento Server!');
        return json_encode($return);

    }

}