<?php

namespace Sonic\Booking\Block\Adminhtml;

use \Magento\Framework\View\Element\Template;
use \Magento\Framework\View\Element\Template\Context;
use Zend\Http\Client;
use Zend\Http\Request;
use Zend\Json\Json;

use Magento\Framework\Controller\ResultFactory;
class OrderResult extends Template
{
    protected $_timezone;
    protected $_storeManager;
    protected $messageManager;
    protected $resultFactory;
    protected $_url;
    protected $_responseFactory;
    protected $_backendUrl;
    public function __construct(Context $context,      
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        ResultFactory $resultFactory,
        \Magento\Framework\UrlInterface $url,
        \Magento\Framework\App\ResponseFactory $responseFactory
    )
    {        
        $this->_storeManager = $storeManager;
        $this->_timezone = $timezone;
        $this->resultFactory = $resultFactory;
        $this->messageManager = $messageManager;
        $this->_url = $url;
        $this->_responseFactory = $responseFactory;
        parent::__construct($context);
    }

    public function getBaseUrl()
    {
        return $this->_storeManager->getStore()->getBaseUrl();
    }

    public function getApiKey(){
        $api_key = $this->_scopeConfig->getValue('sonicextension/general/api_key', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        return $api_key;
    }
    public function getAccountType(){
        $account_type = $this->_scopeConfig->getValue('sonicextension/general/account_type', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        return $account_type;
    }
    public function isShipmentEnabled(){
        $shipment = $this->_scopeConfig->getValue('sonicextension/general/shipment', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        return $shipment;
    }

    public function bookOrder()
    {
        $date = $this->_timezone->date();
        $date = $date->format('Y-m-d');
        $SONIC_URL = 'https://sonic.pk/api/';
        $account_type = (int)$this->getAccountType();
        $isShipmentEnabled = (int)$this->isShipmentEnabled();
        $success_array = array();
        $failure_array = array();
        $post = $this->getRequest()->getPostValue();

        // return $post;
        if(!$post){
            $this->messageManager->addError(__('No order selected!'));
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $this->_backendUrl = $objectManager->get('\Magento\Backend\Model\UrlInterface'); 
            $url = $this->_backendUrl->getUrl("sales/order/index");
            $CustomRedirectionUrl = $this->_url->getUrl($url);
            $this->_responseFactory->create()->setRedirect($CustomRedirectionUrl)->sendResponse();
            exit();
        }
        try{
            $api_key = $this->getApiKey();
            $error = false;
            // $selected_order_items = array();
            foreach ($post['row_order_id'] as $key => $order) {
                $consignee_city = $post['city'][$order];
                $pickup_address_id = $post['pickup_address'][$order];
                $shipping_mode_id = $post['shipping_mode'][$order];
                $consignee_phone = $post['consignee_phone'][$order];
                $consignee_address = $post['consignee_address'][$order];
                // $special_instructions = null;
                $special_instructions = $post['special_instruction'][$order];
                $total_price = $post['amount'][$order];
                // return $post['special_instruction'][$order];
                // if (!\Zend_Validate::is(trim($post[$key]['consignee_phone']), 'NotEmpty')) {
                //     $error = true;
                // }
                // if (!\Zend_Validate::is(trim($post[$key]['consignee_address']), 'NotEmpty')) {
                //     $error = true;
                // }
                // if (!\Zend_Validate::is(trim($post[$key]['amount']), 'NotEmpty')) {
                //     $error = true;
                // }
                $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

                $storeManager  = $objectManager->get('\Magento\Store\Model\StoreManagerInterface');
                $store_name = $storeManager->getStore()->getName();

                $order_detail = $objectManager->create('Magento\Sales\Model\Order')->load($order);
                if($order_detail->getState() == 'new'){
                    $first_name = $order_detail->getCustomerFirstname();
                    if($first_name == ''){
                        $consignee_name = 'Guest';
                    }
                    else{
                        $consignee_name = $order_detail->getCustomerFirstname() . ' ' . $order_detail->getCustomerLastname();
                    }
                    
                    $consignee_email_address = $order_detail->getCustomerEmail();
                    $item_description = '';
                    $item_quantity = 0;
                    $total_weight = 0;
                    $current_quantity = 0;
                    $current_weight = 0;
                    $orderItems = $order_detail->getAllVisibleItems();
                    $orderId = $order_detail->getIncrementId();;

                    if($orderItems){
                        foreach($orderItems as $item){
                            
                            $item_description .= $item->getName() . ' (x' . (int)$item->getQtyOrdered() . '), ';
                            $current_quantity =  (int)$item->getQtyOrdered();
                            $current_weight =  (float)$item->getWeight();
                            $item_quantity += $current_quantity;
                            if($current_quantity > 1){
                                $total_weight += $current_weight * $current_quantity;
                            }
                            else{

                                $total_weight += $current_weight;
                            }
                             
                            
                        }
                    }
                    
                    $item_description = substr($item_description, 0, -2);

                    if (strlen($item_description) > 500) {
                        $item_description = substr($item_description, 0, 497) . '...';
                    }
                    if($total_weight == 0){
                        $total_weight = 1;
                    }
                    try 
                   {
                        $url = $SONIC_URL.'shipment/book';
                        
                        $form_data = array();
                        if($account_type == 1){
                            $form_data = [
                                'service_type_id' => 1,
                                'pickup_address_id' => (int)$pickup_address_id,
                                'information_display' => 0,
                                'consignee_city_id' => (int)$consignee_city,
                                'consignee_name' => $consignee_name,
                                'consignee_address' => $consignee_address,
                                'consignee_phone_number_1' => $consignee_phone,
                                'consignee_email_address' => $consignee_email_address,
                                'order_id' => $orderId,
                                'item_product_type_id' => 1,
                                'item_description' => $item_description,
                                'item_quantity' => $item_quantity,
                                'item_insurance' => 0,
                                'pickup_date' => $date,
                                'estimated_weight' => $total_weight,
                                'shipping_mode_id' => (int)$shipping_mode_id,
                                'amount' => (int)$total_price,
                                'payment_mode_id' => 1
                            ];
                            

                        }else{
                            $form_data = [
                                'service_type_id' => 1,
                                'pickup_address_id' => (int)$pickup_address_id,
                                'information_display' => 0,
                                'consignee_city_id' => (int)$consignee_city,
                                'consignee_name' => $consignee_name,
                                'consignee_address' => $consignee_address,
                                'consignee_phone_number_1' => $consignee_phone,
                                'consignee_email_address' => $consignee_email_address,
                                'order_id' => $orderId,
                                'item_product_type_id' => 1,
                                'item_description' => $item_description,
                                'item_quantity' => $item_quantity,
                                'item_insurance' => 0,
                                'pickup_date' => $date,
                                'estimated_weight' => $total_weight,
                                'shipping_mode_id' => (int)$shipping_mode_id,
                                'amount' => (int)$total_price,
                                'payment_mode_id' => 1,
                                'delivery_type_id' => 1
                            ];

                        }
                        if($special_instructions != '' || $special_instructions != null){
                            $form_data['special_instructions'] = $special_instructions;
                        }
                        
                        $client = new Client($url);
                        $client
                            ->setHeaders([
                                'Content-Type' => 'application/json',
                                'Authorization' => $api_key
                            ])
                            // ->setOptions(['sslverifypeer' => false])
                            ->setMethod('POST')
                            ->setOptions(array('timeout' => 120))
                            ->setRawBody(Json::encode($form_data));
                        
                        $client->send();
                        $response = $client->getResponse();
                        $status_code = $response->getStatusCode();
                        if($status_code == 200){
                            $response_body = $response->getBody();
                            $response_data = Json::decode($response_body);
                            
                            if($response_data->status == 0){
                                $tracking_number = $response_data->tracking_number;
                                $success_array[$order] = $tracking_number;
                                $order_detail->addStatusHistoryComment('Shipment is booked to Trax with Tracking Number: '.$tracking_number);
                                $order_detail->save();
                                if($isShipmentEnabled){
                                    $this->createShipment($order, $tracking_number);
                                }
                            }else{
                                $failure_array[] = $response_data;
                            }

                        }
                    }
                    catch (\Zend\Http\Exception\RuntimeException $runtimeException) 
                    {
                        echo $runtimeException->getMessage();
                    }
                }
            }
            
            if(count($failure_array) > 0){
                foreach ($failure_array as $errors) {
                    foreach ($errors->errors as $key => $value) {
                        if(is_array($value)){
                                $this->messageManager->addError(($value[0]));
                        }else{
                            $this->messageManager->addError(__($value));
                        }
                    }
                    
                }
            }

            
            if(count($success_array) > 0){
                foreach ($success_array as $okey => $success) {
                    $this->messageManager->addSuccess(__('Order No: '. $okey .' booked with tracking number: '. $success . ' successfully!'));
                }
            }
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $this->_backendUrl = $objectManager->get('\Magento\Backend\Model\UrlInterface'); 
            $url = $this->_backendUrl->getUrl("sales/order/index");
            $CustomRedirectionUrl = $this->_url->getUrl($url);
            $this->_responseFactory->create()->setRedirect($CustomRedirectionUrl)->sendResponse();
            exit();
        }catch (\Exception $e){
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $this->_backendUrl = $objectManager->get('\Magento\Backend\Model\UrlInterface'); 
            $url = $this->_backendUrl->getUrl("sales/order/index");
            $CustomRedirectionUrl = $this->_url->getUrl($url);
            $this->_responseFactory->create()->setRedirect($CustomRedirectionUrl)->sendResponse();
            exit();
        }
    }

    public function createShipment($order_id, $tracking_number){
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        // Load the order increment ID
        $order = $objectManager->create('Magento\Sales\Model\Order')->load($order_id);

        // Check if order can be shipped or has already shipped
        if (! $order->canShip()) {
            throw new \Magento\Framework\Exception\LocalizedException(
                            __('You can\'t create an shipment.')
                        );
        }

        // Initialize the order shipment object
        $convertOrder = $objectManager->create('Magento\Sales\Model\Convert\Order');
        $shipment = $convertOrder->toShipment($order);

        // Loop through order items
        foreach ($order->getAllItems() AS $orderItem) {
            // Check if order item has qty to ship or is virtual
            if (! $orderItem->getQtyToShip() || $orderItem->getIsVirtual()) {
                continue;
            }

            $qtyShipped = $orderItem->getQtyToShip();

            // Create shipment item with qty
            $shipmentItem = $convertOrder->itemToShipmentItem($orderItem)->setQty($qtyShipped);

            // Add shipment item to shipment
            $shipment->addItem($shipmentItem);
        }

        // Register shipment
        $shipment->register();

        //Add Tracking number
        $data = array(
            'carrier_code' => 'Trax',
            'title' => 'Trax Logistics',
            'number' => $tracking_number, // Replace with your tracking number
        );

        $shipment->getOrder()->setIsInProcess(true);

        try {
            $track = $objectManager->create('Magento\Sales\Model\Order\Shipment\TrackFactory')->create()->addData($data);
            $shipment->addTrack($track)->save();
            
            // Save created shipment and order
            $shipment->save();
            $shipment->getOrder()->save();

            // Send email
            // $objectManager->create('Magento\Shipping\Model\ShipmentNotifier')
            //     ->notify($shipment);

            $shipment->save();
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\LocalizedException(
                            __($e->getMessage())
                        );
        }
    }
}