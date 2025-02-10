<?php
/**
 * Webkul Odoomarketplaceconnect Category Model
 * @category  Webkul
 * @package   Webkul_Odoomarketplaceconnect
 * @author    Webkul
 * @copyright Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\Odoomarketplaceconnect\Model\ResourceModel;

use Webkul\Odoomagentoconnect\Helper\Connection;

/**
 * Blog post mysql resource
 */
class Category extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Construct
     *
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param string|null $resourcePrefix
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Magento\Catalog\Model\Category $categoryModel,
        \Webkul\Odoomagentoconnect\Model\Category $categoryMapping,
        \Webkul\Odoomagentoconnect\Model\ResourceModel\Category $categoryResModel,
        \Magento\Framework\Event\Manager $eventManager,
        Connection $connection,
        $resourcePrefix = null
    ) {
        $this->_categoryMapping = $categoryMapping;
        $this->_categoryResModel = $categoryResModel;
        $this->_eventManager = $eventManager;
        $this->_categoryModel = $categoryModel;
        $this->_connection = $connection;
        parent::__construct($context, $resourcePrefix);
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('odoomarketplaceconnect_category', 'entity_id');
    }

    public function getCategoryArray($categoryId)
    {  
        $category = $this->_categoryModel->load($categoryId);
        $parentId = $category->getParentId();
        $categoryData = ['name'=>$category->getName()];
        if ($parentId) {
            $categoryModel = $this->_connection->getModel(\Webkul\Odoomarketplaceconnect\Model\Category::class);
            $mappingCollection = $categoryModel->getCollection()->addFieldToFilter('magento_id', $parentId);
    
            if (count($mappingCollection)) {
                $odooId = $mappingCollection->getFirstItem()->getOdooId();
                $categoryData['parent_id'] = (int)$odooId;
            }
        }
        return $categoryData;
    }

    public function checkWebsiteCategorySetting()
    {
        $resp = $this->_connection->callOdooMethod('connector.instance', 'checkWebsiteCategorySetting', [], true);
        $websiteSync = ($resp[0] && $resp[1] == 1) ? $resp[1]: 0;
        return $websiteSync;
    }

    public function createWebsiteCategory($mageId)
    {
        $response = [];
        if ($mageId) {
            $helper = $this->_connection;
            $collection = $this->_categoryMapping->getCollection()->addFieldToFilter('magento_id', $mageId);
            if (!count($collection)) {
                $this->_categoryResModel->createSpecificCategory($mageId, 'create');
            }
            $categoryArray = $this->getCategoryArray($mageId);
            $resp = $helper->callOdooMethod('product.public.category', 'create', [$categoryArray], true);
            if ($resp && $resp[0]) {
                $odooId = (int)$resp[1];
                $response['odoo_id'] = $odooId;
                $mappingData = [
                    'odoo_id'=>$odooId,
                    'magento_id'=>$mageId,
                    'need_sync'=>'no',
                    'created_by'=>$helper::$mageUser
                ];
                $helper->createMapping(\Webkul\Odoomarketplaceconnect\Model\Category::class, $mappingData);

                $data = [
                    'website_category_id'=>$odooId,
                    'website_category_name'=>$odooId,
                    'ecomm_id'=>(int)$mageId,
                ];
                $helper->callOdooMethod('connector.category.mapping', 'updateCategoryMapping', [$data], true);
                $this->_eventManager->dispatch('website_category_sync_after', ['mage_id' => $mageId, 'odoo_id' => $odooId]);       
            } else {
                $response['odoo_id'] = 0;
            }
        }
        return $response;
    }

    public function updateSpecificCategory($mappingId)
    {
        $response = [];
        $helper = $this->_connection;
        if ($mappingId) {
            $categoryModel = $helper->getModel(\Webkul\Odoomarketplaceconnect\Model\Category::class);
            $mappingObj =  $categoryModel->load($mappingId);
            $odooId = (int)$mappingObj->getOdooId();
            $magentoId = $mappingObj->getMagentoId();
            $categoryArray = $this->getCategoryArray($magentoId);
            $resp = $helper->callOdooMethod("product.public.category", "write", [$odooId, $categoryArray], true);
            if ($resp && $resp[0]) {
                $mappingObj->setNeedSync('no')->save();
                $this->_eventManager->dispatch('website_category_sync_after', ['mage_id' => $magentoId, 'odoo_id' => $odooId,]);
                $response['odoo_id'] = $odooId;
            } else {
                $response['odoo_id'] = 0;
            }
        }
        return $response;
    }

    public function getErpCategoryArray()
    {
        $categoryArray = [];
        $response = $this->checkWebsiteCategorySetting();
        if ($response) {
            $helper = $this->_connection;
            $category[''] ='--Select Odoo Website Category--';
            $resp = $helper->callOdooMethod("product.public.category", 'search_read', [[],['id', 'display_name']]);
            if ($resp && $resp[0]) {
                $categoryArray[''] ='--Select Odoo Website Category--';
                $odooCategories = $resp[1];
                foreach ($odooCategories as $odooCategory) {
                    $categoryArray[$odooCategory['id']] = $odooCategory['display_name'];
                }
            } else {
                $categoryArray['error'] = $resp[1];
            }   
        } else {
            $categoryArray[''] ='--Website Category setting disabled at Odoo.--';
        }
        return $categoryArray;
    }
    
    public function getMageCategoryArray()
    {
        $category = [];
        $category[''] ='--Select Magento Website Category--';
        $collection = $this->_categoryModel->getCollection()->addAttributeToFilter('level', ['neq' => 0])->getAllIds();
        foreach ($collection as $value) {
            $categoryObj = $this->_categoryModel->load($value);
            $categoryId = $categoryObj->getId();
            $categoryName = $categoryObj->getName();
            $category[$categoryId] = $categoryName;
        }
        return $category;
    }
}
