<?php
namespace AALogics\SupplierCategoryBlock\Block;

use Webkul\Marketplace\Helper\Data as MpHelper;
use Webkul\Marketplace\Model\ResourceModel\Seller\CollectionFactory;

class SupplierCategoryBlock extends \Magento\Framework\View\Element\Template
{
	protected $categoryFactory;
	protected $_request;
	protected $SellerCollection;

	/**
     * @var \Webkul\Marketplace\Model\ResourceModel\Seller\CollectionFactory
     */
    protected $_sellerlistCollectionFactory;

	/**
     * @var MpHelper
     */
    protected $mpHelper;
	protected $_storeManager;
	protected $_customerRepositoryInterface;
	
	public function __construct(
		\Magento\Framework\View\Element\Template\Context $context,
		\Magento\Catalog\Model\CategoryRepository $categoryFactory,
		\Magento\Framework\App\RequestInterface $request,
		\Webkul\Marketplace\Model\ResourceModel\Seller\CollectionFactory $sellerlistCollectionFactory,
		MpHelper $mpHelper,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		\Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface		
	)
	{
		parent::__construct($context);
		$this->_categoryFactory = $categoryFactory;
		$this->_request = $request;
		$this->_sellerlistCollectionFactory = $sellerlistCollectionFactory;
		$this->mpHelper = $mpHelper;
		$this->_storeManager = $storeManager;
		$this->_customerRepositoryInterface = $customerRepositoryInterface;

	}

	public function getsubCategories($category_id)
	{
		
		$categoryId = $category_id;
		$category = $this->_categoryFactory->get($categoryId);
    	$subCategories = $category->getChildrenCategories()->addAttributeToSelect('*');;
		return $subCategories;
	}

    public function getSellerbyCategory($category_id)
    {
		$helper = $this->mpHelper;
		$storeCollection = $this->_sellerlistCollectionFactory
            ->create()
            ->addFieldToSelect(
                ['allowed_categories','is_seller','seller_id','shop_title','logo_pic','shop_url']
            )->addFieldToFilter(array('allowed_categories'),array(
				array('like' => '%'.$category_id.'%'),
				)
			)
            ->addFieldToFilter(
                'is_seller',
                ['eq' => 1]
            )->addFieldToFilter(
                'store_id',
                ['eq' => 0]
            )->setOrder(
                'entity_id',
                'desc'
            );

		$sellerlist = array();
		foreach ($storeCollection as $key => $items) {
			$sellerlist[$key] = $items->getData();
		}
		
        return $sellerlist;
    }

	public function getCustomerDataById($customerId)
	{
		$getCustomerData = $this->_customerRepositoryInterface->getById($customerId); 
		$getMappingUrl = $getCustomerData->getCustomAttribute('mapping_url');
		$mappingUrl = "";
		if($getMappingUrl){
			$mappingUrl = $getCustomerData->getCustomAttribute('mapping_url')->getValue();
		}
		return $mappingUrl;
	}

	public function getBaseUrl(){
		return $this->_storeManager->getStore()->getBaseUrl();
	}
}