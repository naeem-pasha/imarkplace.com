<?php
/**
* @author  Khurram Malik <khurram.malik@webworks.pk>
* @category    SmartLane Connect
* @package     Webworks\SmartLane
* @copyright   Copyright (c) 2017 Webworks.pk
*/


namespace Webworks\SmartLane\Controller\Adminhtml\Bookorder;

use Braintree\Exception;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Backend\App\Action\Context;
use Magento\Sales\Controller\Adminhtml\Order\AbstractMassAction;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\OrderFactory;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;
use Magento\Sales\Api\OrderManagementInterface;
use Psr\Log\LoggerInterface;
use Webworks\SmartLane\Helper\Data;
use Magento\Framework\App\RequestInterface;


class Booknow extends AbstractMassAction
{
/**
* @const: API_FAILED_CODE
**/
const API_FAILED_CODE = 422;
/**
* @const: API_SUCCESS_CODE
**/
const API_SUCCESS_CODE = 200;
/**
* @const: API_EXCEPTION_CODE
**/
const API_EXCEPTION_CODE = 500;
/**
* @var OrderManagementInterface
*/
protected $orderManagement;
/**
* @var OrderFactory
*/
protected $orderFactory;
/**
* @var CollectionFactory
*/

protected $collectionFactory;
/**
* @var LoggerInterface
*/

protected $logger;
    protected $dataPersistor;
/**
* @var Data
*/

protected $smarthelper;
protected  $request;

/**
* @param Context $context
* @param Filter $filter
* @param CollectionFactory $collectionFactory
* @param OrderManagementInterface $orderManagement
*/
public function __construct(
    Context $context,
    Filter $filter,
    CollectionFactory $collectionFactory,
    OrderManagementInterface $orderManagement,
    OrderFactory $orderFactory,
    LoggerInterface $logger,
    Data $smarthelper,
    \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor,
    RequestInterface $request
)
{
    parent::__construct($context, $filter);
    $this->collectionFactory = $collectionFactory;
    $this->orderManagement = $orderManagement;
    $this->orderFactory = $orderFactory;
    $this->logger = $logger;
    $this->smarthelper = $smarthelper;
    $this->dataPersistor = $dataPersistor;
    $this->request = $request;
}

/**
* Hold selected orders
*
* @param AbstractCollection $collection
* @return Redirect
*/
protected function massAction(AbstractCollection $collection)
{
    $meta = [];
    $courierSlug = $this->request->getParam('courier_slug');
    $meta = ['courier_slug' => $courierSlug];
    foreach ($collection->getItems() as $order) {

        $rtr= $this->smartlanebookorder($order, $meta);
        if($rtr)
        {
            break;
        }
    }

    $resultRedirect = $this->resultRedirectFactory->create();
    $resultRedirect->setPath('sales/order/index/*/');

    return $resultRedirect;
}

protected function smartlanebookorder($sorder, $meta = [])
{

    /** @var Order $order */
    $processOrder = $this->orderFactory->create()->load($sorder->getEntityId());
    //clear the session
    $this->dataPersistor->clear('disable_webworks_smartlane_observer_checksmartlaneorderstatus_on_success');
    $this->logger->info('SmartLane: OrderId=' . $processOrder->getIncrementId());
    if($processOrder->getStatus()!='smartlane_book' && $processOrder->getStatus()!='smartlane_cancel')
    {
        $apicall = $this->smarthelper->pushorderstoSL($processOrder, $meta);
        $this->logger->info($apicall);
        $result = json_decode($apicall);
        $this->logger->info('SmartLane: OrderId=' . $processOrder->getIncrementId(). ', API Results: '.print_r($result,1));

        if($result)
        {
            $orderState = Order::STATE_PROCESSING;
            if($result->code == self::API_SUCCESS_CODE) 
            {
                //if already ran the observer then don't need to book the order
                $this->dataPersistor->set('disable_webworks_smartlane_observer_checksmartlaneorderstatus_on_success', true);
                $processOrder->setState($orderState);
                $processOrder->setStatus('smartlane_book');
            } 
            elseif ($result->code == self::API_FAILED_CODE) 
            {
                $ostatus=$processOrder->getStatus();
                $processOrder->setStatus($ostatus);
                $processOrder->addStatusToHistory($ostatus, 'Validation Failed, '.$result->message);
                $this->logger->info("Order can't be pushed, Status Failed");
            }
            else 
            {
                $this->logger->info("Order can't be pushed, Status Failed Exception at API End");
                return  $this->messageManager->addError("Order ".$processOrder->getIncrementId()." Error= ".$result->message);
            }
            $processOrder->save();

        }
        else
        {
          return  $this->messageManager->addError("Order ".$processOrder->getIncrementId()." API not Responding Please Call SL Team");  
        }


    }
}

}
