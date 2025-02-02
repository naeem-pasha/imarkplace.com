<?php
/**
 * Webkul Odoomarketplaceconnect DisapproveProductAfterObserver
 *
 * @category  Webkul
 * @package   Webkul_Odoomarketplaceconnect
 * @author    Webkul Software Pvt. Ltd <support@webkul.com>
 * @copyright Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\Odoomarketplaceconnect\Observer;

use Magento\Framework\Event\ObserverInterface;

/**
 * Webkul Odoomarketplaceconnect DisapproveProductAfterObserver Observer.
 */
class DisapproveProductAfterObserver implements ObserverInterface
{
    public function __construct(
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Webkul\Odoomarketplaceconnect\Model\Product $productMapping,
        \Webkul\Odoomagentoconnect\Helper\Connection $connection,
        \Magento\Catalog\Model\Product $catalogModel
    ) {
        $this->_productMapping = $productMapping;
        $this->_catalogModel = $catalogModel;
        $this->_connection = $connection;
        $this->_messageManager = $messageManager;
    }

    /**
     * admin customer save after event handler.
     *
     * #param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $productObj = $observer->getEvent()->getProduct();
        if ($productObj) {
            $helper = $this->_connection;

            $productId = $productObj->getMageproductId();
            $collection = $this->_productMapping->getCollection()->addFieldToFilter('mp_product_id', $productObj->getId());
            foreach ($collection as $value) {
                $odooId = $value->getOdooId();
                $proType = $this->_catalogModel->load($productId)->getTypeId();
                $model = ($proType == 'configurable') ? "product.template" : "product.product";

                $response = $helper->callOdooMethod($model, "write", [(int)$odooId, ['status'=> 'rejected']]);
                if ($response && $response[0]) {
                    $message = "The Seller Product have been successfully disapproved at Odoo.";
                    $this->_messageManager->addSuccess(__($message));
                } else {
                    $message = 'The Seller Product have not been disapproved at Odoo for more details check Synchronization Logs !!';
                    $this->_messageManager->addError(__($message));
                }
            }
        }
    }
}
