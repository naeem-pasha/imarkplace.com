<?php

namespace Imark\OdooIntegration\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Imark\OdooIntegration\Helper\OdooApiHelper;

class OrderObserver implements ObserverInterface
{
    protected $odooApiHelper;

    public function __construct(OdooApiHelper $odooApiHelper)
    {
        $this->odooApiHelper = $odooApiHelper;
    }

    public function execute(Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();
        foreach ($order->getAllItems() as $item) {
            $productId = $item->getProductId();  // Product ID
            $sku = $item->getSku();              // SKU
            $itemId = $item->getItemId();        // Order Item ID
        }
        $orderData = [
            'customer_name' => $order->getCustomerName(),
            'customer_email' => $order->getCustomerEmail(),
            'course_id' => $sku, // Replace this with the actual course ID, if available in the order data
        ];

        // Call Odoo API to create a user in Odoo
        $this->odooApiHelper->createUserInOdoo($orderData);
    }
}
