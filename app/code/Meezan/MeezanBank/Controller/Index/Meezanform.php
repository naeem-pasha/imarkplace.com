<?php
	namespace Meezan\MeezanBank\Controller\Index;
	use Magento\Framework\App\Config\ScopeConfigInterface;
	use Magento\Store\Model\ScopeInterface;
	use Magento\Sales\Model\Order;
	use Magento\Framework\Controller\Result\JsonFactory;
	class Meezanform extends \Magento\Framework\App\Action\Action {
		protected $_curl;
		protected $scopeConfig;
		protected $_checkoutSession;
		protected $_customerSession;
		private $jsonResultFactory;
		public function __construct(
			\Magento\Framework\App\Action\Context $context,
			\Magento\Framework\HTTP\Client\Curl $curl,
			ScopeConfigInterface $scopeConfig,
			\Magento\Checkout\Model\Session $checkoutSession,
			\Magento\Customer\Model\Session $customerSession,
			\Magento\Framework\Controller\Result\JsonFactory $jsonResultFactory
		) {
			$this->_curl = $curl;
			parent::__construct ( $context );
			$this->scopeConfig = $scopeConfig;
			$this->_checkoutSession = $checkoutSession;
			$this->_customerSession = $customerSession;
			$this->jsonResultFactory = $jsonResultFactory;
		} // END OF __construct FUNCTION
		public function execute() 
		{			
			$orderId = $this->_checkoutSession->getQuote()->getReservedOrderId();		
			$_ApiUrl= $this->scopeConfig->getValue ( 'payment/meezan_bank/api_url', \Magento\Store\Model\ScopeInterface::SCOPE_STORE );
			$_Username = $this->scopeConfig->getValue ( 'payment/meezan_bank/user_name', \Magento\Store\Model\ScopeInterface::SCOPE_STORE );
			$_Password = $this->scopeConfig->getValue ( 'payment/meezan_bank/password', \Magento\Store\Model\ScopeInterface::SCOPE_STORE );
			$_ReturnUrl = $this->scopeConfig->getValue ( 'payment/meezan_bank/return_url', \Magento\Store\Model\ScopeInterface::SCOPE_STORE );
			$_Return=urlencode($_ReturnUrl);
			$Amount=$this->_checkoutSession->getQuote()->getGrandTotal ();
			$AmountTmp = $Amount * 100;
			$AmtSplitArray = explode ( '.', $AmountTmp );
			$_FormattedAmount = $AmtSplitArray [0];
			$result = $this->jsonResultFactory->create();
			// Api Request For Meezan Form
			try
			{
				$url = $_ApiUrl."/register.do?password=$_Password&userName=$_Username&amount=$_FormattedAmount&currency=586&orderNumber=$orderId&returnUrl=$_Return";
				$headers = array(
					"Content-Type: application/json",
					"Content-Length: 0",
					);
				$this->_curl->setHeaders($headers);
				$this->_curl->setOption(CURLOPT_CONNECTTIMEOUT, 3);
				$this->_curl->setOption(CURLOPT_RETURNTRANSFER, true);
				$this->_curl->get($url);
				$res = $this->_curl->getBody();
				$response = json_decode ($res);
				if( $response !="")
				{
					if($response->errorCode == 0){
						$this->_customerSession->setformid($response->orderId);
						$this->_customerSession->setformUrl($response->formUrl);
					}
					$result->setData($response);
					return $result;
				}
    		}
			catch (\Exception $e) {
			//echo json_encode($e);
			}
		} // END OF execute FUNCTION
	} // END OF Response CLASS