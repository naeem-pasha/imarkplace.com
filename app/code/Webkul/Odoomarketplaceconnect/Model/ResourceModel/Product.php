<?php
/**
 * Webkul Odoomarketplaceconnect Product
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
class Product extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Construct
     *
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param string|null $resourcePrefix
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Webkul\Marketplace\Model\Product $catalogModel,
        \Webkul\Odoomarketplaceconnect\Model\Sellers $sellerMapping,
        \Webkul\Odoomagentoconnect\Model\Template $templateMapping,
        \Webkul\Odoomagentoconnect\Model\Product $productMapping,
        \Webkul\Odoomagentoconnect\Model\ResourceModel\Template $templateModel,
        \Webkul\Odoomagentoconnect\Model\ResourceModel\Product $productModel,
        \Magento\Catalog\Model\Product $productManager,
        Connection $connection,
        $resourcePrefix = null
    ) {
        $this->_catalogModel = $catalogModel;
        $this->_templateMapping = $templateMapping;
        $this->_productMapping = $productMapping;
        $this->_productManager = $productManager;
        $this->_sellerMapping = $sellerMapping;
        $this->_templateModel = $templateModel;
        $this->_productModel = $productModel;
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
        $this->_init('odoomarketplaceconnect_product', 'entity_id');
    }

    public function createSpecificProduct($mpProductId)
    {
        $response = [];
        $odooId = 0;
        $this->_connection->updateMpSetting();
        $sellerProductObj = $this->_catalogModel->load($mpProductId);
        $productId = $sellerProductObj->getMageproductId();
        $mpSellerId = $sellerProductObj->getSellerId();
        $collection = $this->_sellerMapping
            ->getCollection()
            ->addFieldToFilter('magento_id', $mpSellerId);

        $odooSellerId = (count($collection) == 0) ? 0 : $collection->getFirstItem()->getOdooId();
        if ($odooSellerId) {
            $product = $this->_productManager->load($productId);
            if ($product->getTypeID() == 'configurable') {
                $model = "product.template";
                $collection = $this->_templateMapping->getCollection()->addFieldToFilter('magento_id', $productId);
                if (count($collection) > 0) {
                    $odooId = $collection->getFirstItem()->getOdooId();
                } else {
                    $response = $this->_templateModel->exportSpecificConfigurable($productId);
                    if ($response['odoo_id'] > 0) {
                        $odooId = $response['odoo_id'];
                        $this->_templateModel->syncConfigChildProducts($productId, $odooId);
                    }
                }
            } else {
                $model = "product.product";
                $collection = $this->_productMapping->getCollection()->addFieldToFilter('magento_id', $productId);
                if (count($collection) > 0) {
                    $odooId = $collection->getFirstItem()->getOdooId();
                } else {
                    $response = $this->_productModel->createSpecificProduct($productId);
                    $odooId = $response['odoo_id'];
                }
            }
            if ($odooId > 0) {
                $this->updateProductMapping($mpProductId, $mpSellerId, $productId, $odooId, $odooSellerId, $model);
            }
        }
        $response['odoo_id'] = $odooId;
        return $response;
    }

    public function updateProductMapping($mpProductId, $mpSellerId, $productId, $odooId, $odooSellerId, $model)
    {
        $helper = $this->_connection;
        $mappingData = [
                'odoo_id'=>$odooId,
                'magento_id'=>$productId,
                'mp_product_id'=>$mpProductId,
                'mp_seller_id'=>$mpSellerId,
                'need_sync'=>'no',
                'created_by'=>$helper::$mageUser
            ];

        $helper->createMapping(\Webkul\Odoomarketplaceconnect\Model\Product::class, $mappingData);
        $data = [
            'marketplace_seller_id'=> (int)$odooSellerId,
            'status'=> 'pending'
        ];
       
        $helper->callOdooMethod($model, "write", [(int)$odooId, $data], true);
        return true;
    }

    public function updateSpecificProduct($mappingId)
    {
        $odooId = 0;
        $response = [];
        $helper = $this->_connection;
        $helper->updateMpSetting();
        $mappingObj = $helper->getModel(\Webkul\Odoomarketplaceconnect\Model\Product::class)->load($mappingId);
        $odooId = $mappingObj->getOdooId();
        $productId = $mappingObj->getMagentoId();
        $product = $this->_productManager->load($productId);
        $status = $product->getStatus();
    
        if ($product->getTypeID() == 'configurable') {
            $model = "product.template";
        } else {
            $model = "product.product";
        }
        
        $mpSellerId = $mappingObj->getMpSellerId();  // seller Id in the current marketplace product mappingObj.
        $collection = $this->_sellerMapping
            ->getCollection()
            ->addFieldToFilter('magento_id', $mpSellerId);
            
        $odooSellerId = (count($collection) == 0) ? 0 : $collection->getFirstItem()->getOdooId();
        $data = ['marketplace_seller_id'=> (int)$odooSellerId];
        $data['status'] = ($status == 2) ? 'rejected' : 'approved';
        $helper->callOdooMethod($model, "write", [(int)$odooId, $data], true);
        $mappingObj->setNeedSync('no')->save();
        $response['odoo_id'] = $odooId;
        return $response;
    }
}
