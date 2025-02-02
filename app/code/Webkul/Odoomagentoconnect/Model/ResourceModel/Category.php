<?php
/**
 * Webkul Odoomagentoconnect Category ResourceModel
 *
 * @category  Webkul
 * @package   Webkul_Odoomagentoconnect
 * @author    Webkul
 * @copyright Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\Odoomagentoconnect\Model\ResourceModel;

/**
 * Webkul Odoomagentoconnect Category ResourceModel Class
 */
class Category extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * @var eventManager
     */
    protected $_eventManager;

    /**
     * Construct
     *
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param string|null                                       $resourcePrefix
     */
    public function __construct(
        \Magento\Framework\Event\Manager $eventManager,
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Magento\Catalog\Model\Category $categoryModel,
        \Webkul\Odoomagentoconnect\Helper\Connection $connection,
        $resourcePrefix = null
    ) {
        $this->_eventManager = $eventManager;
        $this->_connection = $connection;
        $this->_categoryModel = $categoryModel;
        parent::__construct($context, $resourcePrefix);
    }

    /**
     * Get Magento product categories during runtime for manual mapping options.
     *
     * @return categoryArray
     */
    public function getMageCategoryArray()
    {
        $categoryArray[''] = "--Select Magento Category--";
        $collection = $this->_categoryModel->getCollection()
            ->addAttributeToFilter('level', ['neq' => 0])
            ->getAllIds();
        foreach ($collection as $category) {
            $categoryObj = $this->_categoryModel->load($category);  
            $categoryArray[$categoryObj->getId()] = $categoryObj->getName();
        }
        return $categoryArray;
    }

    /**
     * Get Odoo product categories during runtime for manual mapping options.
     *
     * @return categoryArray
    */
    public function getOdooCategoryArray()
    {
        $categoryArray = [];
        $resp = $this->_connection->callOdooMethod('product.category', 'search_read', [[],['id', 'display_name']]);
        if ($resp && $resp[0]) {
            $categoryArray[''] ='--Select Odoo Category--';
            $odooCategories = $resp[1];
            foreach ($odooCategories as $odooCategory) {
                $categoryArray[$odooCategory['id']] = $odooCategory['display_name'];
            }
        } else {
            $categoryArray['error'] = $resp[1];
        }
        return $categoryArray;
    }

   /**
     * Create or update Magento product categories to Odoo during ajax requests.
     * 
     * @param int $magentoId 
     * @param string $method (create or write)
     * @param int|null $odooId
     * @param \Webkul\Odoomagentoconnect\Model\Category $mappingObj
     *
     * @return response
     */
    public function createSpecificCategory($magentoId, $method, $odooId = 0, $mappingObj = Null)
    {
        $response = [];
        $helper = $this->_connection;
        if ($magentoId) {
            $context = $helper->getOdooContext();
            $categoryArray = $this->getCategoryArray($magentoId);
            $categoryArray['method'] = $method;
            if ($odooId) $categoryArray['category_id'] = $odooId;
            $context['created_by'] = 'Magento';
            $resp = $helper->callOdooMethod('connector.category.mapping', 'create_category', [$categoryArray], $context);

            if ($resp && $resp[0]) {
                if (!$odooId) $odooId = $resp[1];
                if ($odooId > 0) {
                    $mappingData = [
                        'odoo_id'=>$odooId,
                        'magento_id'=>$magentoId,
                        'created_by'=>$helper::$mageUser
                    ];                
                    if ($mappingObj) {
                        $mappingObj->setNeedSync('no')->save();
                    } else {
                        $helper->createMapping(\Webkul\Odoomagentoconnect\Model\Category::class, $mappingData);
                    }
                    $this->_eventManager->dispatch('catalog_category_sync_after', ['mage_id' => $magentoId, 'odoo_id' => $odooId]);
                    $response['odoo_id'] = $odooId;
                }
            } else {
                $respMessage = $resp[1];
                $operation = $method == 'create' ? 'Export' : 'Update';
                $error = "Category ".$operation." Error, Category Id ".$magentoId." >> ".$respMessage;
                $helper->addError($error);
                $response['odoo_id'] = 0;
                $response['error'] = $respMessage;
            }
        }
        return $response;
    }

     /**
     * Get Magenton product category data on the basis of category ID.
     * 
     * @param int $categoryId 
     * @return categArray
     */
    public function getCategoryArray($categoryId)
    {
        $categoryObj = $this->_categoryModel->load($categoryId);
        $name = urlencode($categoryObj->getName());
        $categArray = ['name'=>$name, 'ecomm_id'=>$categoryId];
        $parentId = $categoryObj->getParentId();
        if ($parentId) {
            $categoryMappingModel = $this->_connection->getModel(\Webkul\Odoomagentoconnect\Model\Category::class);
            $mappingCategoryData = $categoryMappingModel->getCollection()
                ->addFieldToFilter('magento_id', $parentId)
                ->getFirstItem()
                ->getData();
            if (!empty($mappingCategoryData)) {
                $odooParentId = $mappingCategoryData['odoo_id'];
                $categArray['parent_id'] = (int)$odooParentId;
            }
        }
        return $categArray;
    }
    
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('odoomagentoconnect_category', 'entity_id');
    }
}
