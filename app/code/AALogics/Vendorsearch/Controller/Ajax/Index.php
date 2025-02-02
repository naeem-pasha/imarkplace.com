<?php

namespace AALogics\Vendorsearch\Controller\Ajax;

use \MageBig\AjaxSearch\Helper\Data as HelperData;
use \Magento\Framework\App\Action\Context;
use \Magento\Framework\Controller\ResultFactory;
use \Magento\Search\Model\QueryFactory;
use \Magento\Store\Model\StoreManagerInterface;
use \MageBig\AjaxSearch\Model\Search as SearchModel;
use Magento\Framework\App\RequestInterface;

/**
 * AjaxSearch ajax controller
 */
class Index extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \MageBig\AjaxSearch\Helper\Data
     */
    protected $helperData;

    /**
     * @var \Magento\Search\Model\QueryFactory
     */
    private $queryFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \MageBig\AjaxSearch\Model\Search
     */
    private $searchModel;

    /**
     * Request instance
     *
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * @var \Webkul\Marketplace\Model\ResourceModel\Seller\CollectionFactory
     */
    protected $_sellerlistCollectionFactory;
    protected $customerRepositoryInterface;
    

    /**
     * Index constructor.
     *
     * @param HelperData $helperData
     * @param Context $context
     * @param QueryFactory $queryFactory
     * @param StoreManagerInterface $storeManager
     * @param SearchModel $searchModel
     * @param RequestInterface $request
     */
    public function __construct(
        HelperData $helperData,
        Context $context,
        QueryFactory $queryFactory,
        StoreManagerInterface $storeManager,
        SearchModel $searchModel,
        RequestInterface $request,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface,
        \Webkul\Marketplace\Model\ResourceModel\Seller\CollectionFactory $sellerlistCollectionFactory
    ) {
        $this->helperData   = $helperData;
        $this->storeManager = $storeManager;
        $this->queryFactory = $queryFactory;
        $this->searchModel  = $searchModel;
        $this->request = $request;
        $this->customerRepositoryInterface = $customerRepositoryInterface;
        $this->_sellerlistCollectionFactory = $sellerlistCollectionFactory;
        parent::__construct($context);
    }

    /**
     * Retrieve json of result data
     *
     * @return array|\Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        // die('new controller run');
        $params = $this->request->getParams();
        $name = $params['q'];
        // echo "<pre>"; print_r($name);

        if($params['st'] == 1){
            if($params['q'] =! ""){
                $resultingCustomers = $this->_sellerlistCollectionFactory
                ->create()
                ->addFieldToSelect(
                    ['is_seller','seller_id','shop_title','shop_url','logo_pic','contact_number','company_description']
                )->addFieldToFilter(
                    'shop_title',['like'=>('%'.$name .'%')]
                )->addFieldToFilter(
                    'is_seller',
                    ['eq' => 1]
                )->addFieldToFilter(
                    'store_id',
                    ['eq' => 0]
                )->setOrder(
                    'entity_id',
                    'desc'
                );
          
                // echo "<pre>"; print_r($resultingCustomers->getSelect()->__toString());
                // echo "Search by Vendor : ".$name;
                // echo "<pre>"; print_r($resultingCustomers->getData());
                // die('---end---');

                $data = [];
                if($resultingCustomers->getData()){
                    $_count = 0;
                    $data_count = 0;
                    foreach($resultingCustomers->getData() as $customer){
                        if($_count < 5)
                        {
                            // Get Mapping Url
                            $getCustomerData = $this->customerRepositoryInterface->getById($customer['seller_id']); 
                            $getMappingUrl = $getCustomerData->getCustomAttribute('mapping_url');
                            $url = "";
                            if($getMappingUrl){
                                $url = $getCustomerData->getCustomAttribute('mapping_url')->getValue();
                            }else{
                                $url = $this->storeManager->getStore()->getBaseUrl().'/marketplace/seller/profile/shop/'.$customer['shop_url'];
                            }

                            // is Image available
                            $img = "";
                            if($customer['logo_pic']){
                                $img = $this->storeManager->getStore()->getBaseUrl().'/media/avatar/'.$customer['logo_pic'];
                            }else{
                                $img = $this->storeManager->getStore()->getBaseUrl().'/media/placeholder/vendor-placeholder-img.png';
                            }

                            $data[] = [
                                "name"=> $customer['shop_title'],
                                "sku"=> "",
                                "image"=> $img,
                                "reviews_rating"=> "",
                                "url"=> $url
                            ];
                        }
                        $_count = $_count + 1;
                        $data_count = $data_count + 1;
                    }
                }

                // echo "<pre>"; print_r($data);
                // die('---end---');

                $responseData['vendor'] = 'yes';
                $responseData['result'] = [[
                    "code"=> "product",
                    "data"=>  $data ,
                    "size"=> $data_count,
                    "url"=> $this->storeManager->getStore()->getBaseUrl()."/searchby/vendor/index/?q=".$name
                ]];
                $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
                $resultJson->setData($responseData);
                return $resultJson;
            }
            // die('---end---');
        }else{
            $query = $this->queryFactory->get();
            $query->setStoreId($this->storeManager->getStore()->getId());
        
            $responseData = [];

            if ($query->getQueryText() != '') {
                
                $query->setId(0)->setIsActive(1)->setIsProcessed(1);

                $responseData['result'] = $this->searchModel->getData();
            }
            
            $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
            $resultJson->setData($responseData);

            return $resultJson;
        }
    }
}
