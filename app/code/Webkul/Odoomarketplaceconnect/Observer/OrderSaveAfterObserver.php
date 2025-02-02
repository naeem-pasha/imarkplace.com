<?php
/**
 * Webkul Odoomarketplaceconnect SalesOrderPlaceAfterObserver Model
 *
 * @category  Webkul
 * @package   Webkul_Odoomarketplaceconnect
 * @author    Webkul
 * @copyright Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 *
 */

namespace Webkul\Odoomarketplaceconnect\Observer;

use Magento\Framework\Event\Manager;
use Webkul\Odoomagentoconnect\Observer\SalesOrderAfterObserver;

class OrderSaveAfterObserver extends SalesOrderAfterObserver
{

    public $_messageManager;

    public function __construct(
        \Webkul\Marketplace\Model\Saleslist $salesList,
        \Webkul\Odoomagentoconnect\Model\Order $orderMapping,
        \Magento\Framework\App\RequestInterface $requestInterface,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Webkul\Odoomagentoconnect\Model\ResourceModel\Order $orderModel,
        \Webkul\Odoomarketplaceconnect\Model\ResourceModel\Order $mpOrderModel
    ) {
        $this->_salesList = $salesList;
        $this->_orderModel = $orderModel;
        $this->_scopeConfig = $scopeConfig;
        $this->_orderMapping = $orderMapping;
        $this->_mpOrderModel = $mpOrderModel;
        $this->_requestInterface = $requestInterface;
        $this->_messageManager = $messageManager;
    }

    /**
     * Sale Order Save After event handler
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $lastOrderObj = $observer->getEvent()->getData('order');
        $route = $this->_requestInterface->getControllerName();
        if ($route == "order" || $route == "order_shipment" || $route == "order_invoice") {
            return true;
        }

        $saleOrderId = $lastOrderObj->getEntityId();
        $mapping = $this->_orderMapping->getCollection()->addFieldToFilter('magento_id', $saleOrderId);

        /** @var $orderInstance Order */
        if (count($mapping) == 0) {
            if (!$lastOrderObj) return;

            $autoOrder = $this->_scopeConfig->getValue('odoomagentoconnect/order_settings/auto_order');
            if ($autoOrder == 1) {
                $odooOrderId = $this->_orderModel->exportOrder($lastOrderObj);
                if ($odooOrderId) {
                    $autoSellerOrder = $this->_scopeConfig->getValue('odoomagentoconnect/odoomarketplaceconnect/auto_order');
                    if ($autoSellerOrder == 1) {
                        $collection = $this->_salesList->getCollection()->addFieldToFilter('order_id', $saleOrderId);
                        foreach ($collection as $salesList) {
                            $mpOrderId = $salesList->getEntityId();
                            $response = $this->_mpOrderModel->exportSpecificOrder($mpOrderId);
                        }
                    }
                    $this->_messageManager->addSuccess(__("Magento Sales Order ".$lastOrderObj->getIncrementId()." successfully created at Odoo."));
                }
            }
        }
    }
}
