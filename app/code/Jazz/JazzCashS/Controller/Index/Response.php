<?php

namespace Jazz\JazzCashS\Controller\Index;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Sales\Model\Order;
use Magento\Framework\Controller\Result\JsonFactory;

class Response extends \Magento\Framework\App\Action\Action {
	protected $request;
	protected $scopeConfig;
	public function __construct(\Magento\Framework\App\Action\Context $context, \Magento\Framework\App\Request\Http $request, ScopeConfigInterface $scopeConfig) 

	{
		parent::__construct ( $context );
		$this->scopeConfig = $scopeConfig;
		$this->request = $request;
	}
	public function execute() {
		
		$isResponseOk = false;
		
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
		$orderDatamodel = $objectManager->get ( 'Magento\Sales\Model\Order' )->getCollection ()->getLastItem ();
		$orderId =  $orderDatamodel->getId();
		
		$order = $objectManager->create ( '\Magento\Sales\Model\Order' )->load ( $orderId );
		$orderItems = $order->getAllItems ();
		
		$Total_Amount = ($order->getBaseGrandTotal ());
		$orderState = Order::STATE_PROCESSING;
		// $order->setExtOrderId ( 'T2018112233' );
		// $order->setState ( $orderState )->setStatus ( Order::ACTION_FLAG_HOLD );
		
		// $orderId = 70;
		// $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		// $order = $objectManager->create('\Magento\Sales\Model\Order') ->load($orderId);
		// $orderState = Order::STATE_PROCESSING;
		// $order->setExtOrderId('T2018112233');
		// $order->setState($orderState)->setStatus(Order::ACTION_FLAG_HOLD);
		// $order->save();
		
		if ($this->getRequest ()->isPost ()) {
			
			if (! empty ( $_POST )) {
				
				foreach ( $_POST as $key => $val ) {
					
					$comment .= $key . "[" . $val . "],<br/>";
					
					$sortedResponseArray [$key] = $val;
				}
			}
			
			$_MerchantID = $this->scopeConfig->getValue ( 'payment/jazz_cashs/merchant_ID', ScopeInterface::SCOPE_STORE );
			$_Password = $this->scopeConfig->getValue ( 'payment/jazz_cashs/password', ScopeInterface::SCOPE_STORE );
			$_IntegritySalt = $this->scopeConfig->getValue ( 'payment/jazz_cashs/integrity_salt', ScopeInterface::SCOPE_STORE );
			$_ValidateHash = $this->scopeConfig->getValue ( 'payment/jazz_cashs/hash_check', ScopeInterface::SCOPE_STORE );
			
			// $_ResponseMessage = $this->getRequest()->getParam('pp_ResponseMessage');
			// $_ResponseCode = $this->getRequest()->getParam('pp_ResponseCode');
			// $_TxnRefNo = $this->getRequest()->getParam('pp_TxnRefNo');
			// $_BillReference = $this->getRequest()->getParam('pp_BillReference');
			// $_SecureHash = $this->getRequest()->getParam('pp_SecureHash');
			// $_TxnType = $this->getEmptyIfNullFromPOST('pp_TxnType');
			// $_RetreivalReferenceNo = $this->getEmptyIfNullFromPOST('pp_RetreivalReferenceNo');
			
			if ($_ValidateHash) {
				
				if (! $this->isNullOrEmptyString ( $_SecureHash )) {
					// removing pp_SecureHash key
					unset ( $sortedResponseArray ['pp_SecureHash'] );
					// sorting array w.r.t key
					ksort ( $sortedResponseArray );
					$sortedResponseValuesArray = array ();
					// Populating Sorted Array
					array_push ( $sortedResponseValuesArray, $_IntegritySalt );
					
					foreach ( $sortedResponseArray as $key => $val ) {
						if (! $this->isNullOrEmptyString ( $val )) {
							array_push ( $sortedResponseValuesArray, $val );
						}
					}
					
					// joining array of sorted response values
					$sortedResponseValuesForHash = implode ( '&', $sortedResponseValuesArray );
					// Calculating Hash
					$CalSecureHash = hash_hmac ( 'sha256', $sortedResponseValuesForHash, $_IntegritySalt );
					
					if (strtolower ( $CalSecureHash ) == strtolower ( $_SecureHash )) {
						$isResponseOk = true;
					} else {
						$isResponseOk = false;
						$errorMsg = 'JazzCashS: Secure Hash mismatched!';
						$comment .= "Secure Hash mismatched.";
					}
				} else {
					$isResponseOk = false;
					$errorMsg = 'JazzCashS: Secure Hash is empty!';
					$comment .= "Secure Hash is empty.";
				}
			} else {
				$isResponseOk = true;
			}
			
			$payment_success_codes = array (
					"000" 
			);
			
			$payment_success_pending = array (
					"124" 
			);
			
			if ($isResponseOk) {
				
				if (in_array ( $_ResponseCode, $payment_success_codes )) {
					
					$successFlag = true;
					// $order->setStatus('complete');
					// $order->setExtOrderId($_TxnRefNo);
					
					if ($_TxnType == "MWALLET") {
						
						$order->setStatus ( 'mw_success' );
						$order->setExtOrderId ( $_TxnRefNo );
					} else if ($_TxnType == "OTC") {
						
						$order->setStatus ( 'otc_success' );
						$order->setExtOrderId ( $_TxnRefNo );
					} else if ($_TxnType == "MIGS") {
						
						$order->setStatus ( 'migs_success' );
						$order->setExtOrderId ( $_TxnRefNo );
					}
					
					$returnUrl = 'checkout/onepage/success';
				} else if (in_array ( $_ResponseCode, $payment_success_pending )) {
					$successFlag = true;
					$order->setStatus ( 'pending_payment' );
					$order->setExtOrderId ( $_TxnRefNo );
					$returnUrl = 'checkout/onepage/success';
				} else {
					$successFlag = false;
					// $order->setStatus('JazzCashS_failed');
					// $order->setExtOrderId($_TxnRefNo);
					
					if ($_TxnType == "MWALLET") {
						
						$order->setStatus ( 'mw_success' );
						$order->setExtOrderId ( $_TxnRefNo );
					} else if ($_TxnType == "OTC") {
						
						$order->setStatus ( 'otc_failure' );
						$order->setExtOrderId ( $_TxnRefNo );
					} else if ($_TxnType == "MIGS") {
						
						$order->setStatus ( 'migs_failure' );
						$order->setExtOrderId ( $_TxnRefNo );
					}
					$this->cancelAction ();
					$returnUrl = 'checkout/onepage/failure';
				}
			} else {
				$order->setStatus ( 'fraud' );
				$returnUrl = 'checkout/onepage/failure';
			}
			$order->save ();
		} // end of post
		
	/**
	 * *****
	 */
	}
}	 