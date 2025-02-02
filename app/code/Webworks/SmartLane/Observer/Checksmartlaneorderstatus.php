<?php

namespace Webworks\SmartLane\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Webworks\SmartLane\Model\SmartLaneOrderStatusRepository;

class Checksmartlaneorderstatus implements ObserverInterface
{
	protected $order;
	protected $logger;
	protected $smarthelper;
    protected $_orderRepository;
    protected $dataPersistor;
    protected $smartLaneOrderStatusRepository;

	private $slstatuses = array(
		'smartlane_book' ,
		'smartlane_ready',
		'smartlane_dispatched',
		'smartlane_attempted_delivery',
		'smartlane_delivered' ,
		'smartlane_refused',
		'smartlane_returned',
        'smartlane_cancel',
        'smartlane_on_hold',
        'smartlane_un_hold'
	);

	private $slAllowedStatuses = [
        'smartlane_cancel',
        'smartlane_on_hold',
        'smartlane_un_hold'
    ];

	public function __construct(
        \Magento\Sales\Model\OrderRepository $orderRepository,
		\Psr\Log\LoggerInterface $logger,
		\Magento\Sales\Api\Data\OrderInterface $order,
		\Webworks\SmartLane\Helper\Data $smarthelper,
        \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor,
        SmartLaneOrderStatusRepository $smartLaneOrderStatusRepository
	) {
		$this->order = $order;
		$this->logger = $logger;
		$this->smarthelper = $smarthelper;
        $this->_orderRepository = $orderRepository;
        $this->dataPersistor = $dataPersistor;
        $this->smartLaneOrderStatusRepository = $smartLaneOrderStatusRepository;
	}


	public function execute(Observer $observer)
	{
	    if(!$this->dataPersistor->get('update_order_status_via_api')){
            $order= $observer->getData('order');
            //get old and new statuses
            $status_transition_from  = $order->getOrigData('status');
            $status_transition_to    = $order->getData('status');

            $this->logger->info('From Status'.$status_transition_from);
            $this->logger->info('To Status'.$status_transition_to);

            $change_state_from = (in_array($status_transition_from, $this->slstatuses)) ? 1 : 0;
            $change_state_to = (in_array($status_transition_to, $this->slstatuses)) ? 1 : 0;

            $this->logger->info('old state 53'.$change_state_from);
            $this->logger->info('new state 54'.$change_state_to);

            //If changing to Book Now at index 0
            if ($change_state_from == 0 && $change_state_to == 1 && $status_transition_to == $this->slstatuses[0]) {
                if (!$this->dataPersistor->get('disable_webworks_smartlane_observer_checksmartlaneorderstatus_on_success')) {
                    $result = $this->smarthelper->smartlaneBookSingleOrder($order);
                    if($result && $result->code ==200){
                        $order->setStatus($status_transition_to);
                        $order->addStatusToHistory($status_transition_to, 'Successfully booked at smartlane!');
                        $this->logger->info('old status 66'.$change_state_from);
                    }else{
                        $order->setStatus($status_transition_from);
                        $order->addStatusToHistory($status_transition_from, 'Order not booked at smartlane');
                        $this->logger->info('old status 71'.$change_state_from);
                    }
                }
                //clear session
                $this->dataPersistor->clear('disable_webworks_smartlane_observer_checksmartlaneorderstatus_on_success');
            }

            //if changing from zero to one entering in smartlane statuses but not from book now
            if ($change_state_from == 0 && $change_state_to == 1 && $status_transition_to != $this->slstatuses[0]) {
                //Not Allowed - Revert Back / Rolback
                $order->setStatus($status_transition_from);
                $order->addStatusToHistory($status_transition_from, 'Not Authorised. Order status change request denied by Smartlane!');
            }

            //changing from one to one within smartlane statuses but not to cancel/smartlane on-hold
            if ($change_state_from == 1 && $change_state_to == 1 && !in_array($status_transition_to, $this->slAllowedStatuses)) {
                //Not Allowed - Revert Back
                $order->setStatus($status_transition_from);
                $order->addStatusToHistory($status_transition_from, 'Not Authorised. Order status change request denied by Smartlane!');
            }elseif($change_state_from == 1 && $change_state_to == 1 && in_array($status_transition_to, $this->slAllowedStatuses)) {
                //to do cancel from smartlane through API
                $apicall = $this->smartLaneOrderStatusRepository->processOrderStatus($order, $status_transition_to);
                $result = json_decode($apicall);
                if($result && $result->code==200){
                    $order->setStatus($status_transition_to);
                    $order->addStatusToHistory($status_transition_to, 'Successfully '.$status_transition_to.' from smartlane!');
                    $this->logger->info('old status 95'.$change_state_from);
                }else{
                    $order->setStatus($status_transition_from);
                    $order->addStatusToHistory($status_transition_from, 'Order not '.$status_transition_to.' successfully');
                    $this->logger->info('old status 98'.$change_state_from);
                }
            }

            //changing from one to zero via cancel status to change
            if ($change_state_from == 1 && $change_state_to == 0 && in_array($status_transition_from, $this->slAllowedStatuses)) {
                //API Hit for withdrawn status on slane app
                $order->setStatus($status_transition_to);
                $order->addStatusToHistory($status_transition_to, 'Order processed successfully with reference');
            } else if ($change_state_from == 1 && $change_state_to == 0 && !in_array($status_transition_from, $this->slAllowedStatuses)) {
                //Not Allowed - Revert Back
                $order->setStatus($status_transition_from);
                $order->addStatusToHistory($status_transition_from, 'Not Authorised. Order status change request denied by Smartlane!');
            }

            return $this;

        }else{
	        //clear if exists
            $this->dataPersistor->clear('update_order_status_via_api');
        }

	}

}