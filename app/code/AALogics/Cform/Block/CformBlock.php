<?php
namespace AALogics\Cform\Block;
class CformBlock extends \Magento\Framework\View\Element\Template
{
	protected $categoryFactory;
	protected $_storeManager;
	protected $_cityOption;
	public function __construct(
		\Magento\Framework\View\Element\Template\Context $context,
		\Magento\Catalog\Model\CategoryRepository $categoryFactory,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		\AALogics\Cform\Model\Source\CityOptions $cityOption
		
	)
	{
		parent::__construct($context);
		$this->_categoryFactory = $categoryFactory;
		$this->_storeManager = $storeManager;
		$this->_cityOption = $cityOption;
	}

	public function getsubCategories($category_id)
	{
		
		$categoryId = $category_id;
		$category = $this->_categoryFactory->get($categoryId);
    	$subCategories = $category->getChildrenCategories()->addAttributeToSelect('*');;
		return $subCategories;
	}

	public function getBaseUrl(){
		return $this->_storeManager->getStore()->getBaseUrl();
	}

	public function _getCitiesOption(){
        $cities = $this->_cityOption->toOptionArray();
        return $cities;
    }
}