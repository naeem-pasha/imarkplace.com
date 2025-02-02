<?php
	namespace Meezan\MeezanBank\Controller\Index;
	use Magento\Framework\App\Config\ScopeConfigInterface;
	use Magento\Store\Model\ScopeInterface;
	use Magento\Sales\Model\Order;
	use Magento\Framework\Controller\Result\JsonFactory;
	use Magento\Framework\App\CsrfAwareActionInterface;
	use Magento\Framework\App\RequestInterface;
	use Magento\Framework\App\Request\InvalidRequestException;
	class Response extends \Magento\Framework\App\Action\Action implements CsrfAwareActionInterface {
		protected $_curl;
		protected $orderManagement;
		protected $request;
		protected $scopeConfig;
		protected $_checkoutSession;
		protected $_customerSession;
		public function __construct(
			\Magento\Framework\App\Action\Context $context,
			\Magento\Framework\HTTP\Client\Curl $curl, 
			\Magento\Framework\App\Request\Http $request,
			ScopeConfigInterface $scopeConfig,
			\Magento\Checkout\Model\Session $checkoutSession,
			\Magento\Sales\Api\OrderManagementInterface $orderManagement,
			\Magento\Customer\Model\Session $customerSession
		) {
			$this->_curl = $curl;
			$this->orderManagement = $orderManagement;
			parent::__construct ( $context );
			$this->scopeConfig = $scopeConfig;
			$this->request = $request;
			$this->_checkoutSession = $checkoutSession;
			$this->_customerSession = $customerSession;
		} // END OF __construct FUNCTION
		public function createCsrfValidationException(RequestInterface $request): ? InvalidRequestException {
			return null;
		}
		public function validateForCsrf(RequestInterface $request): ? bool {
			return true;
		}
		public function execute() 
		{
			$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
			$orderDatamodel = $objectManager->get ( 'Magento\Sales\Model\Order' )->getCollection ()->getLastItem ();
			$orderId = $orderDatamodel->getId ();
			$order = $objectManager->create ( '\Magento\Sales\Model\Order' )->load ( $orderId );
			$customerSession = $objectManager->create('Magento\Customer\Model\Session');
			$Form_id= $customerSession->getformid();
			$_ApiUrl= $this->scopeConfig->getValue ( 'payment/meezan_bank/api_url', ScopeInterface::SCOPE_STORE );
			$_Username = $this->scopeConfig->getValue ( 'payment/meezan_bank/user_name', ScopeInterface::SCOPE_STORE );
			$_Password = $this->scopeConfig->getValue ( 'payment/meezan_bank/password', ScopeInterface::SCOPE_STORE );
			// Api Request for OrderStatus
			$url="$_ApiUrl/getOrderStatus.do?password=$_Password&userName=$_Username&orderId=$Form_id";
			$headers = array(
                "Content-Type: application/json",
                "Content-Length: 0",
                );
			$this->_curl->setHeaders($headers);
			$this->_curl->get($url);
			$res = $this->_curl->getBody();
			$response = json_decode ($res);
			if($Form_id != "")
			{
				if($response->OrderStatus== 2)
				{    
					$order->setIsCustomerNotified(false);
					$order->setStatus('processing')->setState('processing');
					$comment = "Payment Success via Meezan Bank";
					$historyItem = $order->addStatusHistoryComment($comment, 'processing');
					$historyItem->setIsCustomerNotified(1)->save();
					$order->save();
					$orderCommentSender = $objectManager->create('Magento\Sales\Model\Order\Email\Sender\OrderCommentSender');
					$orderCommentSender->send($order, true, $comment);
					$customerSession->unsformid();
					return $this->_redirect('checkout/onepage/success'); exit();
				} 
				else if($response->OrderStatus== 6)
				{    
					$this->cancel($orderId);
					$order->setIsCustomerNotified(false);
					$order->setStatus('canceled')->setState('canceled');
					$comment = "Payment Failed via Meezan Bank";
					$historyItem = $order->addStatusHistoryComment($comment, 'canceled');
					$historyItem->setIsCustomerNotified(1)->save();
					$order->save();
					$orderCommentSender = $objectManager->create('Magento\Sales\Model\Order\Email\Sender\OrderCommentSender');
					$orderCommentSender->send($order, true, $comment);
					$customerSession->unsformid();
					return $this->_redirect('checkout/onepage/failure'); exit();
				} 
				else 
				{
					$this->cancel($orderId);
					$order->setIsCustomerNotified(false);
					$order->setStatus('canceled')->setState('canceled');
					$comment = "Payment Failed via Meezan Bank";
					$historyItem = $order->addStatusHistoryComment($comment, 'canceled');
					$historyItem->setIsCustomerNotified(1)->save();
					$order->save();
					$orderCommentSender = $objectManager->create('Magento\Sales\Model\Order\Email\Sender\OrderCommentSender');
					$orderCommentSender->send($order, true, $comment);
					$customerSession->unsformid();
					return $this->_redirect('checkout/onepage/failure'); exit();
				}
			}
			else
			{
				return $this->_redirect('checkout/cart'); exit();
			}
		} // END OF execute FUNCTION
		protected function cancel( $orderId ) {
			$this->orderManagement->cancel($orderId);
		} // END OF cancel FUNCTION
	} // END OF Response CLASS