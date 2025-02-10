<?php
namespace AALogics\LoginSignupButtonHeader\Block;

class GetCategories extends \Magento\Framework\View\Element\Template
{
	protected $categoryRepository;
	protected $categoryFactory;
	protected $layerResolver;
	public function __construct(
		\Magento\Framework\View\Element\Template\Context $context, 
		\Magento\Catalog\Model\CategoryRepository $categoryRepository, 
	        \Magento\Catalog\Model\Category $categoryFactory, 
                \Magento\Catalog\Model\Layer\Resolver $layerResolver)
	{
		$this->categoryRepository = $categoryRepository;
		$this->categoryFactory = $categoryFactory;
		$this->layerResolver = $layerResolver;
		parent::__construct($context);
	}

	public function getSubCategories()
	{
		$writer = new \Zend_Log_Writer_Stream(BP . '/var/log/custom4.log');
		$logger = new \Zend_Log();
		$logger->addWriter($writer);
		$currentCategory = $this->layerResolver->get()->getCurrentCategory();
		$category = $this->categoryRepository->get($currentCategory->getId());
//		$categoryDesc = $this->categoryRepository->get($currentCategory->getDescription());


		$subCategories = $category->getChildrenCategories();
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$logger->info($subCategories);
		foreach($subCategories as $subCategory) {
			$category= $this->categoryFactory->load($subCategory->getId());
//			echo "subCtg::" . $subCategory->getId();
			$subCtg = $objectManager->get(\Magento\Catalog\Api\CategoryRepositoryInterface::class)->get($subCategory->getId());
			$categoryDescription = $subCtg->getDescription();
			$categorydata[]=array(
		                'name' => $category->getName(),
				'image' => $category->getImageUrl(),
				'url' => $subCategory->getUrl(),
				'description' => $categoryDescription
			);
			$logger->info($subCategory->getUrl());
		
		}

		return $categorydata;
	}
}
