<?php
/**
 * Webkul Odoomagentoconnect CategorySaveAfterObserver Model
 *
 * @category  Webkul
 * @package   Webkul_Odoomagentoconnect
 * @author    Webkul
 * @copyright Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\Odoomagentoconnect\Observer;

use Magento\Framework\Event\ObserverInterface;

/**
 * Webkul Odoomagentoconnect CategorySaveAfterObserver Class
 */
class CategorySaveAfterObserver implements ObserverInterface
{
    public function __construct(
        \Magento\Framework\App\RequestInterface $requestInterface,
        \Webkul\Odoomagentoconnect\Helper\Connection $connection,
        \Webkul\Odoomagentoconnect\Model\Category $categoryMapping,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Webkul\Odoomagentoconnect\Model\ResourceModel\Category $categoryModel
    ) {
        $this->_connection = $connection;
        $this->_scopeConfig = $scopeConfig;
        $this->_categoryModel = $categoryModel;
        $this->_requestInterface = $requestInterface;
        $this->_categoryMapping = $categoryMapping;
    }

    /**
     * Category save after event handler
     *
     * @param  \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $route = $this->_requestInterface->getControllerName();
        $categoryObj = $observer->getEvent()->getCategory();
        $mappingObj = null;
        $categoryId = $categoryObj->getId();
        $categoryMappingModel= $this->_categoryMapping;
        if ($categoryId && $route == 'category') {
            $helper = $this->_connection;
            $helper->getSocketConnect();
            $userId = $helper->getSession()->getUserId();
            $autoSync = $this->_scopeConfig->getValue('odoomagentoconnect/automatization_settings/auto_category');
            $mappingObj = $categoryMappingModel->getCollection()->addFieldToFilter('magento_id', $categoryId)->getFirstItem();
    
            if (!$autoSync && $mappingObj) $mappingObj->setNeedSync('yes')->save();
            
            $userId = $helper->getSession()->getUserId();
            if ($userId > 0 && $autoSync) {
                $categoryObj->setName($categoryObj->getName());
                $categoryObj->setParentId($categoryObj->getParentId());
                $categoryObj->save();
                if ($mappingObj) {
                    $magentoId = $mappingObj->getMagentoId();
                    $odooId = (int)$mappingObj->getOdooId();
                    $this->_categoryModel->createSpecificCategory($magentoId, 'write', $odooId, $mappingObj);
                } else {
                    $response = $this->_categoryModel->createSpecificCategory($categoryId, 'create');
                }
            }
        }
    }
}
