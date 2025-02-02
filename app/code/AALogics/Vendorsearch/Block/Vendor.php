<?php
namespace AALogics\Vendorsearch\Block;
use Magento\Framework\App\RequestInterface;
use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory;

class Vendor extends \Magento\Framework\View\Element\Template
{

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
    
    private $customerFactory;
    protected $_storeManager;
    protected $customerRepositoryInterface;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        array $data=[],
        RequestInterface $request,
        CollectionFactory $customerFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface,
        \Webkul\Marketplace\Model\ResourceModel\Seller\CollectionFactory $sellerlistCollectionFactory
    )
    {
        $this->customerFactory = $customerFactory;
        $this->request = $request;
        $this->_storeManager = $storeManager;
        $this->customerRepositoryInterface = $customerRepositoryInterface;
        $this->_sellerlistCollectionFactory = $sellerlistCollectionFactory;
        parent::__construct($context, $data);
    }

    public function view_all()
    {
        $params = $this->request->getParams();
        $name = $params['q'];
        $data = [];

        if($params['q'] =! "")
        {
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

            if($resultingCustomers->getData()){
                foreach($resultingCustomers->getData() as $customer)
                {
                    // Get Mapping Url
                    $getCustomerData = $this->customerRepositoryInterface->getById($customer['seller_id']); 
                    $getMappingUrl = $getCustomerData->getCustomAttribute('mapping_url');
                    $url = "";
                    if($getMappingUrl){
                        $url = $getCustomerData->getCustomAttribute('mapping_url')->getValue();
                    }else{
                        $url = $this->_storeManager->getStore()->getBaseUrl().'/marketplace/seller/profile/shop/'.$customer['shop_url'];
                    }

                    // is Image available
                    $img = "";
                    if($customer['logo_pic']){
                        $img = $this->_storeManager->getStore()->getBaseUrl().'/media/avatar/'.$customer['logo_pic'];
                    }else{
                        $img = $this->_storeManager->getStore()->getBaseUrl().'/media/placeholder/vendor-placeholder-img.png';
                    }

                    $data[] = [
                        "seller_id"=> $customer['seller_id'],
                        "name"=> $customer['shop_title'],
                        "image"=> $img,
                        "url"=> $url
                    ];
                }
            }
        }
        
        return $data;
    }
}