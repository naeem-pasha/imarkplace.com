<?php

namespace Apps\Payfast\Block;

use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\View\Element\Template\Context;
use Magento\Checkout\Model\Session;
use Magento\Customer\Model\SessionFactory;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\OrderFactory;
use Apps\Payfast\Logger\Logger;
use Magento\Framework\App\Response\Http;
use Magento\Customer\Api\AddressRepositoryInterface;
use Magento\Sales\Model\Order\Payment\Transaction\Builder as TransactionBuilder;

class Main extends \Magento\Framework\View\Element\Template {

    protected $_objectmanager;
    protected $checkoutSession;
    protected $orderFactory;
    protected $urlBuilder;
    private $logger;
    protected $response;
    protected $config;
    protected $messageManager;
    protected $transactionBuilder;
    protected $inbox;
    protected $_customerSession;
    protected $_addressRepo;
    public $publicConfig;
    public $authToken;
    public $basketId;
    public $orderAmount;
    public $mobileNumber;
    public $email;
    public $customerData = [];
    public $successUrl;
    public $failureUrl;
    private $_tokenUrl = 'https://ipg1.apps.net.pk/Ecommerce/api/Transaction/GetAccessToken';
    private $_webcheckoutUrl = 'https://ipg1.apps.net.pk/Ecommerce/api/Transaction/PostTransaction';

    public function __construct(Context $context, Session $checkoutSession, OrderFactory $orderFactory, Http $response, TransactionBuilder $tb, SessionFactory $customerSession, AddressRepositoryInterface $addressRepo
    ) {


        $this->checkoutSession = $checkoutSession;
        $this->orderFactory = $orderFactory;
        $this->response = $response;
        $this->config = $context->getScopeConfig();
        $this->transactionBuilder = $tb;
        $this->publicConfig = $this->config;
        $this->urlBuilder = \Magento\Framework\App\ObjectManager::getInstance()
                ->get('Magento\Framework\UrlInterface');
        $this->_customerSession = $customerSession->create();
        $this->_addressRepo = $addressRepo;

        parent::__construct($context);
    }

    protected function _prepareLayout() {
        $method_data = array();

        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $merchant_id = $this->publicConfig->getValue("payment/payfast/payfast_merchant_id", $storeScope);
        $secured_key = $this->publicConfig->getValue("payment/payfast/payfast_secured_key", $storeScope);

        $this->getPayfastAuthToken($merchant_id, $secured_key);

        $orderId = $this->checkoutSession->getLastOrderId();
        $this->basketId = $orderId;
        $order = $this->orderFactory->create()->load($orderId);
        
        $this->getCustomerData();

        $customerId = $this->_customerSession->getCustomerId();
        $customer = $this->_customerSession->getById($customerId);

        $_ShippingObject = $order->getShippingAddress();
        if($_ShippingObject){
            $mobile_number = $_ShippingObject->getData('telephone');
            $this->customerData['mobile'] = $mobile_number;
        } else {
            $this->customerData['mobile'] = '';
        }
        
        $this->responseUrl  = $this->urlBuilder->getUrl("payfast/response");
        $this->responseUrl = $this->responseUrl . '?ref=m2';
        
        if ($order) {

            $payment = $order->getPayment();
            $this->orderAmount = $order->getGrandTotal();

            $payment->save();
            $order->save();
        }
    }

    private function getPayfastAuthToken($merchant_id, $secured_key) {

        $tokenUrl = sprintf($this->_tokenUrl . '?MERCHANT_ID=%d&SECURED_KEY=%s', $merchant_id, $secured_key);
        $this->authToken = $this->curl_request($tokenUrl);
    }

    private function getCustomerData() {
         
        if ($this->_customerSession->isLoggedIn()) {
            $this->customerData['email'] = $this->_customerSession->getCustomer()->getEmail();            
        } else {
			$this->customerData['email'] = "customer@example.com";          
		}
    }

    private function curl_request($url) {

        $certificate = __DIR__ . "/cacert.pem";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CAINFO, $certificate);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'application/json; charset=utf-8    '
        ));
		curl_setopt($ch, CURLOPT_USERAGENT, 'PHP-CURL Addon APPS PayFast');

        $response = curl_exec($ch);
        
        curl_close($ch);
        $response_decode = json_decode($response);

        if (isset($response_decode->ACCESS_TOKEN)) {
            return $response_decode->ACCESS_TOKEN;
        }
        return;
    }

    public function getAction() {
        return $this->_webcheckoutUrl;
    }

}
