<?php
namespace Sonic\Booking\Block\Adminhtml;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Framework\Data\Form\FormKey;
use Magento\Framework\Controller\ResultFactory;
class OrderDetailBlock extends \Magento\Framework\View\Element\Template
{
	protected $formKey;
	protected $collectionFactory;
	protected $filter;
	protected $zendClient;
	protected $jsonHelper;
	protected $messageManager;
	protected $_url;
    protected $_responseFactory;
    protected $_backendUrl;
	public function __construct(\Magento\Framework\View\Element\Template\Context $context,
	 array $data = [],
	 CollectionFactory $collectionFactory,
	 Filter $filter,
	 \Zend\Http\Client $zendClient,
	 \Magento\Framework\Json\Helper\Data $jsonHelper,
     FormKey $formKey,
     \Magento\Framework\Message\ManagerInterface $messageManager,
     \Magento\Framework\UrlInterface $url,
     \Magento\Framework\App\ResponseFactory $responseFactory
	)
	{
		parent::__construct($context,$data);
		$this->collectionFactory = $collectionFactory;
		$this->filter = $filter;
		$this->zendClient = $zendClient;
		$this->jsonHelper = $jsonHelper;
		$this->formKey = $formKey;
		$this->messageManager = $messageManager;
		$this->_url = $url;
        $this->_responseFactory = $responseFactory;

	}

	public function getOrderData()
	{
		$collection = $this->filter->getCollection($this->collectionFactory->create());
		$orderIds = $collection->getAllIds();
		if($orderIds){
			$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
			$orders_data = array();
			$orders_count = 0;
			foreach($orderIds as $order_id){
		    	$order = $objectManager->create('Magento\Sales\Model\Order')->load($order_id);
		    	if($order->getState() == 'new'){
		    		$orders_count++;
			    	$orders_data[$order_id]['order_increment_id'] = $order->getIncrementId();
			    	$first_name = $order->getCustomerFirstname();
			    	if($first_name == ''){
			    		$orders_data[$order_id]['firstname'] = 'Guest';
			    		$orders_data[$order_id]['lastname'] = ' ';
			    	}
			    	else{
			    		$orders_data[$order_id]['firstname'] = $order->getCustomerFirstname();
			    		$orders_data[$order_id]['lastname'] = $order->getCustomerLastname();
			    	}
			    	

				    $shippingaddress = $order->getShippingAddress();        
					$shippingcity = $shippingaddress->getCity();
					$shippingstreet = $shippingaddress->getStreet();      
					$shippingtelephone = $shippingaddress->getTelephone();
					$customer_address = '';
					foreach ($shippingstreet as $address) {
						$customer_address .= $address.' ';
					}
			    	$orders_data[$order_id]['shipping_address'] = $customer_address;
			    	$orders_data[$order_id]['shipping_city'] = $shippingcity;
			    	$orders_data[$order_id]['shipping_phone'] = $shippingtelephone;
			    	$orders_data[$order_id]['amount'] = (int)$order->getGrandTotal();
			    	$orders_data[$order_id]['status'] = $order->getState();

		    	}

			}
			if($orders_count == 0){
				/*$this->messageManager->addErrorMessage('Pending orders not found!');
				$resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
		        $$resultRedirect->setRefererUrl();

		        return $resultRedirect;*/

		        $this->messageManager->addError(__('No order selected!'));
	            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
	            $this->_backendUrl = $objectManager->get('\Magento\Backend\Model\UrlInterface'); 
	            $url = $this->_backendUrl->getUrl("sales/order/index");
	            $CustomRedirectionUrl = $this->_url->getUrl($url);
	            $this->_responseFactory->create()->setRedirect($CustomRedirectionUrl)->sendResponse();
	            exit();

			}
			else{
				return $orders_data;
			}
			
		}
		
	}

	public function getApiKey(){
		$api_key = $this->_scopeConfig->getValue('sonicextension/general/api_key', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
		return $api_key;
	}

	/**
     * @return string
     */
    public function getFormKey()
    {
        return $this->formKey->getFormKey();
    }
	public function getFormAction(){
		return $this->getRequest()->getParams();
	}

	public function getPickupAddress(){
		$SONIC_URL = 'https://sonic.pk/api/';

		try {
            $api_key = $this->getApiKey();
            if($api_key){
            	 	$this->zendClient->reset();
		            $this->zendClient->setUri($SONIC_URL.'pickup_addresses');
		            $this->zendClient->setOptions(array('timeout' => 120));
		            $this->zendClient->setMethod(\Zend\Http\Request::METHOD_GET); 
		            $this->zendClient->setHeaders([
		                'Content-Type' => 'application/json',
		                'Accept' => 'application/json',
		                'Authorization' => $api_key,
		            ]);
		            
		            $this->zendClient->send();
		            $response = $this->zendClient->getResponse();
		            
		            $response_body = $response->getBody();
		            $response_data =  $this->jsonHelper->jsonDecode($response_body);
		            
		            if($response_data['status'] == 0){
		            	return $response_data['pickup_addresses'];
		            }
            }
        } catch (\Exception $e) {
            $this->messageManager->addError(__($e->getMessage()));
        }
	}

	public function getCities(){
		$SONIC_URL = 'https://sonic.pk/api/';
		try {
            $api_key = $this->getApiKey();

            if($api_key){
            	 	$this->zendClient->reset();
		            $this->zendClient->setUri($SONIC_URL.'cities');
		            $this->zendClient->setOptions(array('timeout' => 120));
		            $this->zendClient->setMethod(\Zend\Http\Request::METHOD_GET); 
		            $this->zendClient->setHeaders([
		                'Content-Type' => 'application/json',
		                'Accept' => 'application/json',
		                'Authorization' => $api_key,
		            ]);
		            
		            $this->zendClient->send();
		            $response = $this->zendClient->getResponse();
		            
		            $response_body = $response->getBody();
		            $response_data =  $this->jsonHelper->jsonDecode($response_body);
		            
		            if($response_data['status'] == 0){
		            	return $response_data['cities'];
		            }
            }
        } catch (\Exception $e) {
            $this->messageManager->addError(__($e->getMessage()));
        }
	}
}