<?php

namespace Webworks\SmartLane\Helper;


use Magento\Directory\Model\CountryFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Sales\Api\OrderManagementInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\OrderFactory;
use Magento\Store\Model\ScopeInterface;
use mysql_xdevapi\Exception;
use Psr\Log\LoggerInterface;
use Magento\Sales\Model\Order\Status;
use Magento\Sales\Model\Order\StatusFactory;
use Magento\Sales\Model\ResourceModel\Order\Status as StatusResource;
use Magento\Sales\Model\ResourceModel\Order\StatusFactory as StatusResourceFactory;

class Data extends AbstractHelper
{
    /**
     * @const: CONFIG_SL_API_URL
     */
    const CONFIG_SL_API_URL = 'webworks/smartlane/api_url';
    /**
     * @const: CONFIG_SL_API_KEY
     */
    const CONFIG_SL_API_KEY = 'webworks/smartlane/api_key';

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

    /**
     * Core store config
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * Logger
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * Country Code
     * @var CountryFactory
     */
    protected $countryFactory;

    protected $orderFactory;

    protected $orderManagement;
    protected $messageManager;
    protected $dataPersistor;

    /**
     * Status Factory
     *
     * @var StatusFactory
     */
    protected $statusFactory;

    /**
     * Status Resource Factory
     *
     * @var StatusResourceFactory
     */
    protected $statusResourceFactory;

    /**
    * @var \Magento\Backend\Model\Auth\Session
    **/
    protected $authSession;

    /**
     * SmartLane constructor.
     *
     * @param Context $context
     * @param LoggerInterface $logger
     * @param CountryFactory $countryFactory
     * @param \Magento\Backend\Model\Auth\Session $authSession
     * @param OrderFactory $orderFactory
     * @param OrderManagementInterface $orderManagement
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor
     */
    public function __construct(
        Context $context,
        LoggerInterface $logger,
        CountryFactory $countryFactory,
        \Magento\Backend\Model\Auth\Session $authSession,
        OrderFactory $orderFactory,
        OrderManagementInterface $orderManagement,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor,
        StatusFactory $statusFactory,
        StatusResourceFactory $statusResourceFactory
    )
    {
        parent::__construct($context);
        $this->scopeConfig = $context->getScopeConfig();
        $this->logger = $logger;
        $this->countryFactory = $countryFactory;
        $this->authSession = $authSession;
        $this->orderFactory = $orderFactory;
        $this->orderManagement = $orderManagement;
        $this->messageManager = $messageManager;
        $this->dataPersistor = $dataPersistor;
        $this->statusFactory = $statusFactory;
        $this->statusResourceFactory = $statusResourceFactory;
    }

    public function getCurrentUser() {
        return $this->authSession->getUser();
    }


    /**
     * SmartLane order status mapping with magento statuses
     * @param null $status
     * @return mixed|string
     */

    public function smartLaneStatusList($status = null)
    {
        $statusList = array(
            'book_now' => 'smartlane_book',
            'ready_for_pickup'=> 'smartlane_ready',
            'dispatched'=> 'smartlane_dispatched',
            'attempted_delivery'=> 'smartlane_attempted_delivery',
            'delivered'=> 'smartlane_delivered' ,
            'customer_refused'=> 'smartlane_refused',
            'returned'=> 'smartlane_returned',
            'cancelled'=> 'smartlane_cancel',
            'on_hold'=> 'smartlane_on_hold',
            'un_hold'=> 'smartlane_un_hold'
        );

        if($status !=null){
            if(array_key_exists($status, $statusList))
            {
                return $statusList[$status];
            }
            else
            {
                return 'Error';
            }
        }else{
            return $statusList;
        }
        
    } 

    public function getRespectiveState($status)
    {
        $statevsstatus= array(
         'smartlane_book' => 'processing',
         'smartlane_ready'=> 'processing',
         'smartlane_dispatched'=> 'processing',
         'smartlane_attempted_delivery'=> 'processing',
         'smartlane_delivered'=> 'complete' ,
         'smartlane_refused'=> 'complete',
         'smartlane_returned'=> 'complete',
         'smartlane_cancel'=> 'processing',
         'smartlane_on_hold'=> 'processing',
         'smartlane_un_hold'=> 'processing'
     );
        return $statevsstatus[$status];
    }

    /***
     *  Push Order to SmartLane for booking
     **/

    public function pushorderstoSL($order, $meta = [])
    {
        $jsondata = $this->orderJsonforSmartLane($order, $meta);
        $this->logger->info('Pusing OrderJson to API: '.$jsondata);
        try {
            $apicall = $this->callApiCurl('/api/post/data/order', $jsondata);
            return $apicall;
        } catch (\Exception $e) {
            $this->logger->critical("Error: Api Can't be executed, Reason= " . $e->getMessage());
            return;

        }
        return;

    }

    public function smartlaneBookSingleOrderOldFunction($sorder)
    {

        /** @var Order $order */
        $processOrder = $this->orderFactory->create()->load($sorder->getEntityId());
        //if already ran then don't need to book the order
        $this->dataPersistor->clear('update_order_status_via_api');
        $this->dataPersistor->clear('disable_webworks_smartlane_observer_checksmartlaneorderstatus_on_success');
        $this->logger->info('SmartLanebookSingle order: OrderId=' . $processOrder->getIncrementId());

        if($processOrder->getStatus()!='smartlane_book')
        {
            $apicall = $this->pushorderstoSL($processOrder);
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
                    $this->dataPersistor->set('update_order_status_via_api', true);
                    $processOrder->setState($orderState);
                    //$processOrder->setStatus('smartlane_book');
                }
                elseif ($result->code == self::API_FAILED_CODE)
                {
                    $ostatus=$processOrder->getStatus();
                    $this->logger->info("Statatatatat" . $ostatus);
                    $processOrder->setState($orderState);
                    $processOrder->setStatus($ostatus);
                    $processOrder->addStatusToHistory($ostatus, 'Validation Failed, '.$result->message);
                    $this->logger->info("Order can't be pushed, Status Failed");
                }
                else
                {
                    $this->logger->info("Order can't be pushed, Status Failed Exception at API End");
                    return  $this->messageManager->addError("Order ".$processOrder->getIncrementId()." Error= ".$result->message);
                }
                $this->logger->info("saveveve");
                $processOrder->save();
            }
            else
            {
                return  $this->messageManager->addError("Order ".$processOrder->getIncrementId()." API not Responding Please Call SL Team");
            }
        }
    }

    /**
     * @param $sorder
     * @return mixed
     */
    public function smartlaneBookSingleOrder($sorder)
    {
        /** @var Order $order */
        $processOrder = $this->orderFactory->create()->load($sorder->getEntityId());
        //if already ran then don't need to book the order
        $this->dataPersistor->clear('update_order_status_via_api');
        $this->dataPersistor->clear('disable_webworks_smartlane_observer_checksmartlaneorderstatus_on_success');
        $this->logger->info('SmartLanebookSingle order: OrderId=' . $processOrder->getIncrementId());

        if ($processOrder->getStatus() != 'smartlane_book') {
            $apicall = $this->pushorderstoSL($processOrder);
            $this->logger->info($apicall);
            $result = json_decode($apicall);
            $this->logger->info('SmartLane: OrderId=' . $processOrder->getIncrementId() . ', API Results: ' . print_r($result, 1));

            if($result->code == self::API_SUCCESS_CODE)
            {
                //if already ran the observer then don't need to book the order
                $this->dataPersistor->set('disable_webworks_smartlane_observer_checksmartlaneorderstatus_on_success', true);
                //$this->dataPersistor->set('update_order_status_via_api', true);
                return $result;

            }else{
                return $result;
            }
        }
    }

    /**
    * Push Auth Keys to SmartLane
    *
    **/
    public function sendAuthKeysToSL($data)
    {
        $this->logger->info('Helper: '.$data);
        $apicall = $this->callApiCurl('/api/post/data/auth-keys', $data);
        return $apicall;
    }

    /**
     * Create Json Format for Payload
     *
     **/

    protected function orderJsonforSmartLane($order, $meta = [])
    {
        $orderarray = array();

        $orderarray['order_info'] = array(
            'store_order_id' => $order->getIncrementId(),
            'iso_currency_code' => $order->getOrderCurrencyCode(),
            'ordered_at' => $order->getCreatedAt()
        );


        // Customer Data
        $orderarray['customer'] = array(
            "firstname" => $order->getCustomerFirstname() ?? $order->getShippingAddress()->getFirstName(),
            "lastname" => $order->getCustomerLastname() ?? $order->getShippingAddress()->getLastName(),
            "email" => $order->getCustomerEmail(),
            "telephone" => $order->getShippingAddress()->getTelephone()
        );

        // Customer Shipping Address
        $street = $order->getShippingAddress()->getStreet();
        $orderarray['shipping_address'] = array(
            "firstname" => $order->getShippingAddress()->getFirstName(),
            "lastname" => $order->getShippingAddress()->getLastName(),
            "address_line_1" => $street[0],
            "address_line_2" => (isset($street[1]) ? $street[1] : $street[0]),
            "postal_code" => $order->getShippingAddress()->getPostCode(),
            "city" => $order->getShippingAddress()->getCity(),
            "state" => $order->getShippingAddress()->getRegion(),
            "iso_state_code" => "",
            "country" => $this->getCountryname($order->getShippingAddress()->getCountryId()),
            "iso_country_code" => $order->getShippingAddress()->getCountryId(),
            "telephone" => $order->getShippingAddress()->getTelephone()
        );

        // Order Items
        $orderarray['items'] = $this->getOrderLineItems($order);

        // Shipping Method
        $orderarray['shipping_method'] = array(
            "label" => $order->getShippingDescription(),
            "price" => $order->getShippingAmount()
        );

        // Payment Method
        $payment = $order->getPayment();
        $method = $payment->getMethodInstance();
        $orderarray['payment_method'] = array(
            "label" => $method->getTitle(),
            "type" => "",//$method->getCcType(),
            "last_4_digits" => ""//$method->getCcLast4()
        );

        // Payment Address
        $Billingstreet = $order->getBillingAddress()->getStreet();
        $orderarray['payment_address'] = array(
            "firstname" => $order->getBillingAddress()->getFirstName(),
            "lastname" => $order->getBillingAddress()->getLastName(),
            "address_line_1" => $Billingstreet[0],
            "city" => $order->getBillingAddress()->getCity(),
            "country" => $this->getCountryname($order->getShippingAddress()->getCountryId())
        );

        // Discount Details
        if ($order->getCouponCode()) {
            $orderarray['discount_coupon'] = array(
                "code" => $order->getCouponCode(),
                "type" => 'fixed',
                "amount" => $order->getDiscountAmount()
            );
        }

        $orderarray['remarks'] = $order->getMagecompOrderComment() ?? $order->getMagecompOrderComment() ?? $order->getUmOrderComment() ?? $order->getUmOrderComment() ??  '';
        $orderarray['admin_comments'] = $order->getStatusHistoryCollection()->getFirstItem()->getComment();

        $orderarray['sub_total'] = $order->getSubtotal();
        $orderarray['total_discount'] = $order->getDiscountAmount();
        $orderarray['total_tax'] = $order->getTaxAmount();
        $orderarray['shipping'] = $order->getShippingAmount();
        $orderarray['total'] = $order->getGrandTotal();
        if(isset($meta) && $meta['courier_slug'] !=null){
            $orderarray['courier'] = array(
                "name" => $meta['courier_slug'],
                "policy" => "hard"
            );
        }

//        $this->logger->info('SmartLane: OrderJson= ' . print_r($orderarray, 1));

        $orderjson = \GuzzleHttp\json_encode($orderarray);

//        $this->logger->info('SmartLane: OrderJson= ' . $orderjson);

        return $orderjson;


    }

    /**
     * Get Country Name from Country ISO Code
     *
     **/
    public function getCountryname($countryCode)
    {
        $country = $this->countryFactory->create()->loadByCode($countryCode);
        return $country->getName();
    }

    /**
     * Get list of order items
     *
     **/

    private function getOrderLineItems($order)
    {
        $lineitems = array();
        $totalitems = array();
        foreach ($order->getAllVisibleItems() AS $orderItem) {
            $lineitems['product_id'] = $orderItem->getProductId();
            $lineitems['product_name'] = $orderItem->getName();
            $lineitems['product_sku'] = $orderItem->getSku();
            $lineitems['quantity'] = $orderItem->getQtyOrdered();
            $lineitems['price'] = $orderItem->getPrice();
            $lineitems['total_price'] = $orderItem->getRowTotal();
            $lineitems['variant_id'] = $orderItem->getProductId();
            $lineitems['variant_name'] = $orderItem->getName();
            $lineitems['variant_sku'] = $orderItem->getSku();
            $lineitems['tax_amount'] = $orderItem->getTaxAmount();
            $lineitems['tax_percentage'] = $orderItem->getTaxPercent();

            $totalitems[] = $lineitems;
        }

        return $totalitems;
    }

    /**
     * Call API with Curl
     **/
    private function callApiCurl($endpoint, $jsondata)
    {
        $apiurl = $this->getApipath();
        $apikey = $this->getApikey();
        $ch = curl_init($apiurl . $endpoint);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsondata);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // curl_setopt($ch1, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER,
            array(
                "Content-Type: application/json",
                "subKey:" . $apikey)
        );
        $result = curl_exec($ch);
        return $result;
    }

    /**
     * get SmartLane Portal url from configuration
     *
     **/

    public function getApipath()
    {
        return $this->scopeConfig->getValue(self::CONFIG_SL_API_URL, ScopeInterface::SCOPE_STORE);
    }

    /**
     * get SmartLane Portal auth key from configuration
     *
     **/
    public function getApikey()
    {
        return $this->scopeConfig->getValue(self::CONFIG_SL_API_KEY, ScopeInterface::SCOPE_STORE);
    }

    /***
     *  Push Order to SmartLane for booking
     * @param $payload
     * @return mixed|void
     */

    public function postOrderToSmartlane($payload)
    {
        $payload = \GuzzleHttp\json_encode($payload);
        try {
            $apiCall = $this->callApiCurl('/api/post/data/order/status', $payload);
            $this->logger->info(json_encode($apiCall));
            return $apiCall;

        } catch (\Exception $e) {
            $this->logger->critical("Error: Api Can't be executed, Reason= " . $e->getMessage());
            return;

        }
        return;

    }

    public function createOrderStatusWithProcessingState($orderStatus , $label){

        $state = Order::STATE_PROCESSING;
        /** @var StatusResource $statusResource */
        $statusResource = $this->statusResourceFactory->create();
        /** @var Status $status */
        $status = $this->statusFactory->create();
        $status->setData([
            'status' => $orderStatus,
            'label' => $label
        ]);
        try {
            $statusResource->save($status);
        } catch (AlreadyExistsException $exception) {

            return true;
        }
        catch (\Exception $exception){
            return false;
        }
        try{
            $status->assignState($state, false, true);
        }catch (\Exception $exception){
            return false;
        }

        return true;
    }

    public function cleanStringSpecialCharactersWithSpace($string){
        return preg_replace("/[^0-9a-zA-Z]/", ' ', $string);
    }

    public function strToCamelCase($string, $delimiter, $capitalizeFirstCharacter = false, $separateWith = '', $lower = false) {
        if($lower){
            $string = strtolower($string);
        }

        $str = str_replace($delimiter, $separateWith, ucwords($string, $delimiter));

        if (!$capitalizeFirstCharacter) {
            $str = lcfirst($str);
        }

        return $str;
    }

}