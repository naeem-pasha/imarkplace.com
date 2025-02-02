<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Paypro\PayproCheckout\Controller\Index;

use Magento\Sales\Model\Order;
use Magento\Framework\Controller\ResultFactory;

class Index extends \Magento\Framework\App\Action\Action
{

    public function __construct (
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\Data\Form\FormKey $formKey,
        \Magento\Checkout\Model\Cart $cart,
        \Magento\Catalog\Model\ProductFactory $product,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        $this->_messageManager = $messageManager;
        $this->formKey = $formKey;
        $this->cart = $cart;
        $this->product = $product;
        $this->_storeManager = $storeManager;
        $this->_resultPageFactory = $resultPageFactory;
        $this->_resultFactory = $context->getResultFactory();
        parent::__construct($context);
    }

    /**
    This method executes when place order is clicked on checkout page. It is responsible for 1st redirection.
     **/
    public function execute() {
        $resultPage = $this->_resultPageFactory->create();
        $block = $resultPage->getLayout()->getBlock('paypro_payprocheckout')->toHtml();
        $this->getResponse()->setBody($block);
    }

    /**
    It is responsible for 2nd redirection.Called from Confirm Class
     **/
    protected function confirm($authToken) {
        $resultPage = $this->_resultPageFactory->create();
        $block = $resultPage->getLayout()->createBlock('\Paypro\PayproCheckout\Block\Info\PayproCheckout')->setData('authToken', $authToken)->setTemplate('Paypro_PayproCheckout::confirm.phtml')->toHtml();
        $this->getResponse()->setBody($block);
    }

    /**
    It is executed when data is posted to postback url.Called from Status Class
     **/
    protected function status($status,$desc,$orderRefNumber) {
        $resultRedirect = $this->_resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setUrl($this->_redirect->getRefererUrl());
        return $resultRedirect;
    }

    public function isEmptyResponse($status, $desc, $orderRefNumber) {
        return empty($status) || empty($orderRefNumber);
    }


    /**
     * CHECK ORDER STATUS ON PAYPRO
     */
    function checkPayProOrderStatus($orderID,$key,$tcode,$transactionID)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $block      = $objectManager->get('Paypro\PayproCheckout\Block\Info\PayproCheckout');
        $mId        = $block->getConfig('payment/payprocheckout/username');
        $mpw        = $block->getConfig('payment/payprocheckout/password');
        $live       = $block->getConfig('payment/payprocheckout/live');
        if($live==1){
            $targerurl="https://api.paypro.com.pk";
        }
        else{
            $targerurl="https://demoapi.paypro.com.pk";
        }
        //Curl Request
        $url = $targerurl.'/cpay/gos?userName=' . $mId . '&password=' . $mpw . '&cpayId=' . $transactionID;
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);

        // Submit the GET request
        $result = curl_exec($ch);

        if (curl_errno($ch))  //catch if curl error exists and show it
            echo 'Curl error: ' . curl_error($ch);
        else {
            $res = json_decode($result, true);
            $returnedOrderID = explode('-',$res[1]['OrderNumber']);
            if ($returnedOrderID[1]===$orderID) {
                if (strtoupper($res[1]['OrderStatus']) == "PAID") {

                    $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                    $order = $objectManager->create('Magento\Sales\Model\Order')->load($orderID);
                    $order->setState(\Magento\Sales\Model\Order::STATE_PROCESSING, true);
                    $order->setStatus(\Magento\Sales\Model\Order::STATE_PROCESSING);
                    $order->addStatusToHistory($order->getStatus(), 'Order processed successfully against TransactionID : ' . $transactionID);
                    $order->save();
                    $this->_messageManager->addSuccess('Your Order Via PayPro is Successfully Placed');
                    $this->_redirect('checkout/onepage/success');

                } elseif (strtoupper($res[1]['OrderStatus']) == "BLOCKED") {
                    $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                    $order = $objectManager->create('Magento\Sales\Model\Order')->load($orderID);
                    $order->setState(\Magento\Sales\Model\Order::STATE_CANCELED, true);
                    $order->setStatus(\Magento\Sales\Model\Order::STATE_CANCELED);
                    $order->addStatusToHistory($order->getStatus(), 'Order processing failed order has been canceled');
                    $order->save();
                }
                elseif (strtoupper($res[1]['OrderStatus']) == "UNPAID") {
                    $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                    $order = $objectManager->create('Magento\Sales\Model\Order')->load($orderID);
                    $order->addStatusToHistory($order->getStatus(), 'Fake Processing Call Been Made Order is Unpaid');
                    $order->save();
                    $this->_messageManager->addError('Kindly Refer to Administrator The Order is not Mark Paid over PayPro\'s System');
                    $this->_redirect('checkout/cart/');
                }
            }
        }
        // Close cURL session handle
        curl_close($ch);
    }

    /**
     * CANCEL ORDER
     */
    function cancelPayProOrder($orderID,$key,$tcode,$transactionID)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $block      = $objectManager->get('Paypro\PayproCheckout\Block\Info\PayproCheckout');
        $mId        = $block->getConfig('payment/payprocheckout/username');
        $mpw        = $block->getConfig('payment/payprocheckout/password');
        $live       = $block->getConfig('payment/payprocheckout/live');
        if($live==1){
            $targerurl="https://api.paypro.com.pk";
        }
        else{
            $targerurl="https://demoapi.paypro.com.pk";
        }
        //Curl Request
        if($transactionID!=""){
            $url = $targerurl.'/cpay/gos?userName=' . $mId . '&password=' . $mpw . '&cpayId=' . $transactionID;
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLINFO_HEADER_OUT, true);

            // Submit the GET request
            $result = curl_exec($ch);

            if (curl_errno($ch))  //catch if curl error exists and show it
                echo 'Curl error: ' . curl_error($ch);
            else {
                $res = json_decode($result, true);
                $returnedOrderID = explode('-',$res[1]['OrderNumber']);
                if ($returnedOrderID[1]===$orderID) {
                    if (strtoupper($res[1]['OrderStatus']) == "PAID") {

                        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                        $order = $objectManager->create('Magento\Sales\Model\Order')->load($orderID);
                        $order->setState(\Magento\Sales\Model\Order::STATE_PROCESSING, true);
                        $order->setStatus(\Magento\Sales\Model\Order::STATE_PROCESSING);
                        $order->addStatusToHistory($order->getStatus(), 'Order processed successfully against TransactionID : ' . $transactionID);
                        $order->save();
                        $this->_messageManager->addSuccess('Your Order Via PayPro is Successfully Placed');
                        $this->_redirect('checkout/onepage/success');

                    } elseif (strtoupper($res[1]['OrderStatus']) == "BLOCKED") {

                        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                        $order = $objectManager->create('Magento\Sales\Model\Order')->load($orderID);
                        $order->setState(\Magento\Sales\Model\Order::STATE_CANCELED, true);
                        $order->setStatus(\Magento\Sales\Model\Order::STATE_CANCELED);
                        $order->addStatusToHistory($order->getStatus(), 'Order processing failed order has been canceled');
                        $order->save();
                        $this->addCanceledOrderItemsToCart($orderID);
                        $this->_redirect('checkout/');

                    }
                }
            }
            // Close cURL session handle
            curl_close($ch);
        }
        else{
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $order = $objectManager->create('Magento\Sales\Model\Order')->load($orderID);
            $order->setState(\Magento\Sales\Model\Order::STATE_CANCELED, true);
            $order->setStatus(\Magento\Sales\Model\Order::STATE_CANCELED);
            $order->addStatusToHistory($order->getStatus(), 'Order processing failed order has been canceled');
            $order->save();
            $this->addCanceledOrderItemsToCart($orderID);
            $this->_redirect('checkout/');
        }
    }

    protected function addCanceledOrderItemsToCart($orderNo){
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $order      = $objectManager->create('\Magento\Sales\Model\OrderRepository')->get($orderNo);
        $items      = $order->getAllItems();
        //get order items
        foreach($items as $item) {
            if ($item->getParentItem()) continue;
            $product_options = $item->getProductOptions();
            $quantity= intval($item->getQtyOrdered());
            $productID =$item->getProductId();

            $params = array(
                'form_key' => $this->formKey->getFormKey(),
                'product_id' => $productID, //product Id
                'qty'   =>$quantity //quantity of product
            );
            if(isset($product_options['info_buyRequest']['super_attribute'])&&$product_options['info_buyRequest']['super_attribute']!=''){
                $params['super_attribute'] = $product_options['info_buyRequest']['super_attribute'];
            }
            $_product = $this->product->create()->load($productID);
            $this->cart->addProduct($_product, $params);
        }
        $this->cart->save();
    }
}
