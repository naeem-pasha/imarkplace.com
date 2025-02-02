<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 *
 * Glory to Ukraine! Glory to the heroes!
 */

namespace AALogics\BlogCategoryWidget\Block;

/**
 * Blog recent posts widget
 */
class BlogsPdpBlock extends \Magefan\Blog\Block\Post\PostList\AbstractList implements \Magento\Widget\Block\BlockInterface
{
    /**
     * @var array
     */
    static $processedIds = [];

    /**
     * @var \Magefan\Blog\Model\CategoryFactory
     */
    protected $_categoryFactory;

    /**
     * @var \Magefan\Blog\Model\Category
     */
    protected $_category;

    protected $_registry;
    
    protected $_productRepository;

    private $categoryRepository;

    private $categorycollectionFactory;


    /**
     * Construct
     *
     * @param \Magento\Framework\View\Element\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Cms\Model\Template\FilterProvider $filterProvider
     * @param \Magefan\Blog\Model\ResourceModel\Post\CollectionFactory $postCollectionFactory
     * @param \Magefan\Blog\Model\Url $url
     * @param \Magefan\Blog\Model\CategoryFactory $categoryFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Cms\Model\Template\FilterProvider $filterProvider,
        \Magefan\Blog\Model\ResourceModel\Post\CollectionFactory $postCollectionFactory,
        \Magefan\Blog\Model\Url $url,
        \Magefan\Blog\Model\CategoryFactory $categoryFactory,
        \Magento\Catalog\Model\ProductRepository $productRepository,
        \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository,   
        \Magefan\Blog\Model\ResourceModel\Category\CollectionFactory $categorycollectionFactory,  
        array $data = []
    ) {
        parent::__construct($context, $coreRegistry, $filterProvider, $postCollectionFactory, $url, $data);
        $this->_productRepository = $productRepository;
        $this->_categoryFactory = $categoryFactory;
        $this->categoryRepository = $categoryRepository;
        $this->categorycollectionFactory = $categorycollectionFactory;
        $this->_registry = $coreRegistry;
    }

    /**
     * Set blog template
     *
     * @return string
     */
    public function _toHtml()
    {
        $this->setTemplate(
            $this->getData('custom_template') ?: 'AALogics_BlogCategoryWidget::widget/recent.phtml'
        );

        $html = parent::_toHtml();
        $arr = [];
        foreach ($this->getPostCollection() as $item) {
            $arr[] = $item->getData();
            self::$processedIds[$item->getId()] = $item->getId();
        }
        return $html;
    }

    /**
     * Retrieve block title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->getData('title') ?: __('Blog Posts');
    }

    /**
     * Prepare posts collection
     *
     * @return void
     */
    protected function _preparePostCollection()
    {
        parent::_preparePostCollection();


        $get_productId = $this->_registry->registry('product');
        if($get_productId){
            $_product_id = $get_productId->getId();
            $_product_data = $this->_productRepository->getById($_product_id);
            $_getCategoryids = $_product_data->getCategoryids();

            $_categoryNames = [];
            if($_getCategoryids){
                foreach ($_getCategoryids as $_categoryid){
                $_getcatbyid = $this->categoryRepository->get($_categoryid, null);
                $_categoryNames[] = $_getcatbyid->getName();
                }
            }       
        }else{
            $get_categoryid = $this->_registry->registry('current_category');
            $_categoryid = $get_categoryid->getId();

            $_categoryNames = [];
            if($_categoryid){
                $_getcatbyid = $this->categoryRepository->get($_categoryid, null);
                $_categoryNames[] = $_getcatbyid->getName();
            }    
        }

        $_blogCategoryCollection = $this->categorycollectionFactory->create()->addFieldToSelect('category_id')
        ->addFieldToFilter('title', array('in' => $_categoryNames));
        $categoryIds = [];
        foreach($_blogCategoryCollection as $row)
        {  
            $categoryIds[] = $row->getCategory_id();
        }          

        if ($categoryIds) {
            $this->_postCollection->addCategoryFilter($categoryIds);
        }

        $enableNoRepeat = $this->getData('no_repeat_posts_enable');
        if ($enableNoRepeat && self::$processedIds){
            $this->_postCollection->addFieldToFilter('post_id', ['nin' => self::$processedIds]);
        }
    }

    /**
     * Retrieve category instance
     *
     * @return \Magefan\Blog\Model\Category
     */
    public function getCategory()
    {
        if ($this->_category === null) {
            if ($categoryId = $this->getData('category_id')) {
                $category = $this->_categoryFactory->create();
                $category->load($categoryId);

                $storeId = $this->_storeManager->getStore()->getId();
                if ($category->isVisibleOnStore($storeId)) {
                    $category->setStoreId($storeId);
                    return $this->_category = $category;
                }
            }

            $this->_category = false;
        }

        return $this->_category;
    }

    /**
     * Retrieve post short content
     * @param  \Magefan\Blog\Model\Post $post
     *
     * @return string
     */
    public function getShorContent($post)
    {
        return $post->getShortFilteredContent();
    }
}
