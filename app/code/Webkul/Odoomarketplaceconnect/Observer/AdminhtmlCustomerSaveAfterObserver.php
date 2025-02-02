<?php
/**
 * Webkul Odoomarketplaceconnect AdminhtmlCustomerSaveAfterObserver
 *
 * @category  Webkul
 * @package   Webkul_Odoomarketplaceconnect
 * @author    Webkul
 * @copyright Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\Odoomarketplaceconnect\Observer;

use Magento\Framework\Event\ObserverInterface;

class AdminhtmlCustomerSaveAfterObserver implements ObserverInterface
{
    public function __construct(
        \Webkul\Marketplace\Model\Seller $sellerModel,
        \Webkul\Odoomarketplaceconnect\Model\Sellers $sellersMapping,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Webkul\Odoomarketplaceconnect\Model\ResourceModel\Sellers $sellersModel
    ) {
        $this->_sellerModel = $sellerModel;
        $this->_scopeConfig = $scopeConfig;
        $this->_sellersModel = $sellersModel;
        $this->_sellersMapping = $sellersMapping;
        $this->_messageManager = $messageManager;
    }

    /**
     * Customer save after event handler
     *
     * @param  \Magento\Framework\Event\Observer $observer
     * @return void
    */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $autoSync = $this->_scopeConfig->getValue('odoomagentoconnect/odoomarketplaceconnect/auto_seller');
        if ($autoSync) {
            $customerId = $observer->getCustomer()->getId();
            $collection = $this->_sellerModel
                ->getCollection()
                ->addFieldToFilter('seller_id', $customerId)
                ->getData();
            if (!empty($collection)) {
                $sellersId = $collection[0]['entity_id'];
                $mappingCollection = $this->_sellersMapping->getCollection()
                    ->addFieldToFilter('mp_seller_id', $sellersId);
                if (count($mappingCollection) == 0) {
                    $response = $this->_sellersModel->exportSpecificSeller($sellersId);
                    if ($response['odoo_id'] > 0) {
                        $message = "The Marketplace Seller have been successfully Exported to Odoo.";
                        $this->_messageManager->addSuccess(__($message));
                    } else {
                        $message = "The Marketplace Seller have not been Exported to Odoo for more details check Synchronization Logs !!";
                        $this->_messageManager->addError(__($message));
                    }
                }
            }
        }
    }
}
