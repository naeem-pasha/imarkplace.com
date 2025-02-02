<?php
/**
 * Webkul Odoomarketplaceconnect Order
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
class Order extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    public function __construct(
        \Webkul\Marketplace\Model\Saleslist $salesList,
        \Webkul\Odoomagentoconnect\Model\Order $orderMapping,
        \Webkul\Odoomagentoconnect\Model\Product $productMapping,
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        Connection $connection,
        $resourcePrefix = null
    ) {
        $this->_salesList = $salesList;
        $this->_connection = $connection;
        $this->_orderMapping = $orderMapping;
        $this->_productMapping = $productMapping;
        parent::__construct($context, $resourcePrefix);
      
       
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('odoomarketplaceconnect_order', 'entity_id');
    }

    public function exportSpecificOrder($mpOrderId)
    {
        // Custom Logs M2 Odoo
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/m2odoo.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info("exportSpecificOrder -> orderId : ".$mpOrderId);

        $response = [];
        $odooOrderId = 0;
        $this->_connection->updateMpSetting();
        $sellerOrderObj = $this->_salesList->load($mpOrderId);
        $mageOrderId = $sellerOrderObj->getOrderId();

        // Custom Logs M2 Odoo
        $logger->info("( exportSpecificOrder) -> mageOrderId : ".$mageOrderId);
        
        $orderData = [
            "magento_id"=>$mageOrderId,
            "magento_line_id"=>$sellerOrderObj->getOrderItemId(),
            "mp_sale_id"=>$mpOrderId,
            "mp_seller_id"=>$sellerOrderObj->getSellerId()
        ];
        $logger->info(" -> orderData : ".print_r($orderData,true));

        
        $mageProductId = $sellerOrderObj->getMageproductId();
        $logger->info("(getMageproductId : ".$mageProductId);
        
        $orderCollection = $this->_orderMapping->getCollection()->addFieldToFilter('magento_id', $mageOrderId);

        // Custom Logs M2 Odoo
          
        $logger->info(" query : ".$orderCollection->getSelect()->__toString());
  
        $logger->info(" -> orderCollection : ".print_r($orderCollection->getData(),true));

        if ($orderCollection->getSize() > 0){
            foreach ($orderCollection as $mapOrder) {
                $odooOrderId = $mapOrder->getOdooId();
                // Custom Logs M2 Odoo
                $logger->info(" -> Foreach odooOrderId : ".$odooOrderId);
            }

        } else {
            $thisOrder = $this->_connection->getModel(\Magento\Sales\Model\Order::class)->load($mageOrderId);
            $odooOrderId = $this->_connection->getModel(\Webkul\Odoomagentoconnect\Model\ResourceModel\Order::class)->exportOrder($thisOrder);

            // Custom Logs M2 Odoo
            $logger->info(" -> Else thisOrder : ".$thisOrder);
            $logger->info(" -> Else odooOrderId : ".$odooOrderId);
        }

        if ($odooOrderId) {
            $orderData['odoo_id'] = $odooOrderId;
            $productCollection = $this->_productMapping->getCollection()->addFieldToFilter('magento_id', $mageProductId);
            
            // Custom Logs M2 Odoo
            $logger->info(" -> 2nd If productCollection : ".print_r($productCollection->getData(),true));

            foreach ($productCollection as $mapProduct) {
                $odooProductId = (int)$mapProduct->getOdooId();
                $this->syncMarketplaceOrder($odooProductId, $orderData);
                // Custom Logs M2 Odoo
                $logger->info(" -> 2nd Froeach odooProductId : ".$odooProductId);
            }
        }
        $response['odoo_id'] = $odooOrderId;

           // Custom Logs M2 Odoo
           $logger->info(" (End exportSpecificOrder)  response : ".print_r($response,true));

        return $response;
    }

    public function syncMarketplaceOrder($odooProductId, $orderData)
    {
        $helper =  $this->_connection;
        $odooLineId = $this->getOdooOrderLine($odooProductId, $orderData);
        if ($odooLineId) {
            $orderData['odoo_line_id'] = $odooLineId;
            $orderData['created_by'] = $helper::$mageUser;
            $helper->createMapping(\Webkul\Odoomarketplaceconnect\Model\Order::class, $orderData);
        }
    }

    public function getOdooOrderLine($odooProductId, $orderData)
    {
        $odooLineId = 0;
        $helper = $this->_connection;
        $data = [
            'name'=>(int)$orderData["odoo_id"],
            'odoo_product_id'=>$odooProductId,
            'odoo_id'=>(int)$orderData["odoo_id"],
            'ecomm_id'=>(int)$orderData["magento_id"],
            'ecomm_seller_id'=>(int)$orderData["mp_seller_id"],
            'ecomm_mp_order_id'=>(int)$orderData["mp_sale_id"],
            'ecomm_order_line_id'=>(int)$orderData["magento_line_id"],
        ];
        $resp = $helper->callOdooMethod("connector.mp.order.mapping", "createMpOrderMapping", [$data], true);
        if ($resp && $resp[0]) {
            $odooLineId = $resp[1];
        } else {
            $helper->addError('Seller order mapping error : '.$resp[1]);
        }
        return $odooLineId;
    }
}
