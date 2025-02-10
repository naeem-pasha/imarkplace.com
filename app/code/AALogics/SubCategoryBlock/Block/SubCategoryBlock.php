<?php
namespace AALogics\SubCategoryBlock\Block;
class SubCategoryBlock extends \Magento\Framework\View\Element\Template
{
	protected $categoryFactory;

	protected $categoryResource;
	protected $_storeManager;
	public function __construct(
		\Magento\Framework\View\Element\Template\Context $context,
		\Magento\Catalog\Model\CategoryRepository $categoryFactory,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		\Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryResource
		
	)
	{
		parent::__construct($context);
		$this->_categoryFactory = $categoryFactory;
		$this->_storeManager = $storeManager;
		$this->_categoryResource = $categoryResource;
	}

	public function getsubCategories($category_id)
	{
		
		$categoryIds =[];
		$categoryId = $category_id;
		$category = $this->_categoryFactory->get($categoryId);
		$subCategories = $category->getChildrenCategories()->addAttributeToSelect(['entity_id']);
		foreach($subCategories as $data)
		{
			array_push($categoryIds,$data->getId());
		}
		$categories = $this->_categoryResource->create()->addAttributeToSelect(['smartmenu_cat_imgicon','name'])->addAttributeToFilter('entity_id', $categoryIds);;
		return $categories;
	}

	public function getBaseUrl(){
		return $this->_storeManager->getStore()->getBaseUrl();
	}
}