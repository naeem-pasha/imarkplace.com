<?php
	namespace Meezan\MeezanBank\Block;
	use Magento\Framework\App\Config\ScopeConfigInterface;
	class Index extends \Magento\Payment\Block\Info 
	{
		protected $_curl;
		protected $paymentConfig;
		protected $_customerSession;
		public function __construct(
			\Magento\Framework\View\Element\Template\Context $context,
			\Magento\Framework\HTTP\Client\Curl $curl, 
			\Magento\Payment\Model\Config $paymentConfig,
			ScopeConfigInterface $scopeConfig,
			\Magento\Checkout\Model\Session $checkoutSession,
			\Magento\Sales\Model\Order $order,
			\Magento\Framework\App\Request\Http $request,
			\Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
			\Magento\Framework\App\Cache\Frontend\Pool $cacheFrontendPool,
			\Magento\Customer\Model\Session $customerSession,
			array $data = []
		) {
			$this->_curl = $curl;
			parent::__construct($context, $data);
			$this->paymentConfig = $paymentConfig;
			$this->scopeConfig = $scopeConfig;
			$this->_checkoutSession = $checkoutSession;
			$this->_order = $order;
			$this->_request = $request;
			$this->_cacheTypeList = $cacheTypeList;
			$this->_cacheFrontendPool = $cacheFrontendPool;
			$this->_customerSession = $customerSession;
		} // END OF __construct FUNCTION
		protected function _prepareSpecificInformation($transport = null) {
			if ($this->_paymentSpecificInformation !== null) {
				return $this->_paymentSpecificInformation;
			}
			return $transport;
		} // END OF _prepareSpecificInformation FUNCTION
		public function getConfig($config_path) {
			return $this->scopeConfig->getValue($config_path,\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
		} // END OF getConfig FUNCTION
		public function getFormUrl() {
			 return $this->_customerSession;
		} // END OF getFormUrl FUNCTION
	} // END OF Index CLASS