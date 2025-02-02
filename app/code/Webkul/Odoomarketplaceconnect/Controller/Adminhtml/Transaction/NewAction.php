<?php

namespace Webkul\Odoomarketplaceconnect\Controller\Adminhtml\Transaction;

/**
 * Webkul Odoomarketplaceconnect NewAction Controller
 * @category  Webkul
 * @package   Webkul_Odoomarketplaceconnect
 * @author    Webkul
 * @copyright Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

class NewAction extends \Webkul\Odoomarketplaceconnect\Controller\Adminhtml\Transaction
{
    /**
     * @return void
     */
    public function execute()
    {
        $helper = $this->_objectManager->create('\Webkul\Odoomagentoconnect\Helper\Connection');
        $helper->getSocketConnect();
        $userId = $helper->getSession()->getUserId();
        if ($userId > 0) {
            $syncCount = 0;
            $failCount = 0;
            $storeManager = $this->_objectManager->get('Magento\Store\Model\StoreRepository');
            $stores = $storeManager->getList();
            if (count($stores) == 0) {
                $this->messageManager->addSuccess(__("No Stores are currently exist at Magento end"));
            } else {
                foreach ($stores as $store) {
                    $storeId = $store->getStoreId();
                    if ($storeId == 0) {
                        continue;
                    }
                    $storeMappingCollection = $this->_objectManager
                                                    ->create('Webkul\Odoomarketplaceconnect\Model\Transaction')
                                                    ->getCollection()
                                                    ->addFieldToFilter('magento_id', ['eq'=>$storeId]);
                    if (count($storeMappingCollection) == 0) {
                        $storeData = $store->getData();
                        $groupId = $store->getGroupId();
                        $groupManager = $this->_objectManager->get('Magento\Store\Model\GroupRepository');
                        $groupObj = $groupManager->get($groupId);
                        $storeData['root_category_id'] = $groupObj->getRootCategoryId();
                        $storeData['default_store_id'] = $groupObj->getDefaultStoreId();
                        $storeData['group_name'] = $groupObj->getName();

                        $websiteId = $store->getWebsiteId();
                        $websiteManager = $this->_objectManager->get('Magento\Store\Model\WebsiteRepository');
                        $websiteObj = $websiteManager->get($websiteId);
                        $storeData['default_group_id'] = $websiteObj->getDefaultGroupId();
                        $storeData['website_code'] = $websiteObj->getCode();
                        $storeData['website_name'] = $websiteObj->getName();

                        $resourceModel = "\Webkul\Odoomarketplaceconnect\Model\ResourceModel\Transaction";
                        $response = $this->_objectManager
                                            ->create($resourceModel)
                                            ->syncStoreAtOdoo($storeData);
                                    
                        if ($response) {
                            $syncCount++;
                        } else {
                            $failCount++;
                        }
                    }
                }
                if ($syncCount) {
                    $this->messageManager
                            ->addSuccess("Total ".$syncCount." Magento Store View successfully Synchronized At Odoo.");
                } elseif ($failCount) {
                    $errorLog = "Synchronization Failed For ".$failCount." Magento Store View Failed, Check Log File!!";
                    $this->messageManager
                            ->addError($errorLog);
                } else {
                    $this->messageManager->addSuccess("All Magento Store View Already Synchronized At Odoo.");
                }
            }
        } else {
            $this->messageManager->addError("Sorry, Your Magento Is Not Connected with Odoo. Error, ".$userId);
        }

        return $this->_forward('index');
    }
    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Webkul_Odoomarketplaceconnect::transaction_new');
    }
}
