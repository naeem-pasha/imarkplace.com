<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_Odoomarketplaceconnect
 * @author    Webkul
 * @copyright Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\Odoomarketplaceconnect\Observer;

use Magento\Framework\Event\ObserverInterface;

/**
 * Webkul Odoomarketplaceconnect ProductSaveAfterObserver Observer.
 */
class ProductSaveAfterObserver implements ObserverInterface
{
    /**
     * @param \Magento\Framework\Message\ManagerInterface $messageManager,
     * @param CollectionFactory                           $collectionFactory
     */
    public function __construct(
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Webkul\Odoomarketplaceconnect\Model\Product $productMapping,
        \Webkul\Marketplace\Model\Product $productModel,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Webkul\Odoomarketplaceconnect\Model\ResourceModel\Product $mpProductModel
    ) {
        $this->_productMapping = $productMapping;
        $this->_productModel = $productModel;
        $this->_scopeConfig = $scopeConfig;
        $this->_mpProductModel = $mpProductModel;
        $this->_messageManager = $messageManager;
    }

    /**
     * admin customer save after event handler.
     *
     * #param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $autoSync = $this->_scopeConfig->getValue('odoomagentoconnect/odoomarketplaceconnect/auto_product');
        if ($autoSync) {
            $data = $observer->getEvent()->getData();
            if (array_key_exists("product_id", $data[0]) || array_key_exists("id", $data[0])) {
                $productId = $this->getProductId($data[0]);
                if ($productId) {
                    $collection = $this->_productModel->getCollection()
                        ->addFieldToFilter('mageproduct_id', $productId);
                    foreach ($collection as $value) {
                        $mpProductId = $value->getEntityId();
                        $mappingCollection = $this->_productMapping->getCollection()->addFieldToFilter('mp_product_id', $mpProductId);
                        if (!count($mappingCollection)) {
                            $response = $this->_mpProductModel->createSpecificProduct($mpProductId);
                            if ($response['odoo_id'] > 0) {
                                $message = "The Seller Product have been successfully Exported to Odoo.";
                                $this->_messageManager->addSuccess(__($message));
                            } else {
                                $message = 'The Seller Product have not been Exported to Odoo for more details check Synchronization Logs or 
                                                    check Product(s) Seller synced to Odoo or not!!';
                                $this->_messageManager->addError(__($message));
                            }
                        }
                    }
                }
            }
        }
    }

    public function getProductId($data)
    {
        if (array_key_exists("product_id", $data)) {
            $proId = $data['product_id'];
        } else {
            $proId = $data['id'];
        }
        return $proId;
    }
}
