<?php

namespace Jazz\JazzCashC\Block;

use Magento\Framework\App\Config\ScopeConfigInterface;

class Rmessage extends \Magento\Payment\Block\Info {
	/**
	 *
	 * @var \Magento\Payment\Model\Config
	 */
	protected $paymentConfig;
	
	/**
	 * Constructor
	 *
	 * @param \Magento\Framework\View\Element\Template\Context $context        	
	 * @param \Magento\Payment\Model\Config $paymentConfig        	
	 * @param array $data        	
	 */
	public function __construct(\Magento\Framework\View\Element\Template\Context $context, \Magento\Payment\Model\Config $paymentConfig, ScopeConfigInterface $scopeConfig, \Magento\Checkout\Model\Session $checkoutSession, \Magento\Sales\Model\Order $order, \Magento\Framework\App\Request\Http $request, \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList, \Magento\Framework\App\Cache\Frontend\Pool $cacheFrontendPool, array $data = []) {
		parent::__construct ( $context, $data );
		$this->paymentConfig = $paymentConfig;
		$this->scopeConfig = $scopeConfig;
		$this->_checkoutSession = $checkoutSession;
		$this->_order = $order;
		$this->_request = $request;
		$this->_cacheTypeList = $cacheTypeList;
		$this->_cacheFrontendPool = $cacheFrontendPool;
	}
	
	/**
	 * Prepare information specific to current payment method
	 *
	 * @param
	 *        	null | array $transport
	 * @return \Magento\Framework\DataObject
	 */
	protected function _prepareSpecificInformation($transport = null) {
		if ($this->_paymentSpecificInformation !== null) {
			return $this->_paymentSpecificInformation;
		}
		return $transport;
	}
	
	/**
	 * Returns admin config parameters
	 */
	public function getConfig($config_path) {
		return $this->scopeConfig->getValue ( $config_path, \Magento\Store\Model\ScopeInterface::SCOPE_STORE );
	}
	
	/**
	 * Retreives current order information to be posted to Easypay
	 */
	public function getOrder() {
		// output: /myproject/index.php
		$currentPath = $_SERVER ['PHP_SELF'];
		
		// output: Array ( [dirname] => /myproject [basename] => index.php [extension] => php [filename] => index )
		$pathInfo = pathinfo ( $currentPath );
		
		// output: localhost
		$hostName = $_SERVER ['HTTP_HOST'];
		
		// output: http://
		$protocol = strtolower ( substr ( $_SERVER ["SERVER_PROTOCOL"], 0, 5 ) ) == 'https://' ? 'https://' : 'http://';
		
		// return: http://localhost/myproject/
		$URL = $protocol . $hostName . $pathInfo ['dirname'] . "/";
		
		$param = $this->getRequest ()->getParam ( '1' );
		
		return array (
				$URL,
				$param 
		);
		
		// $param = $this->getRequest ()->getParam ( '1' );
	}
}