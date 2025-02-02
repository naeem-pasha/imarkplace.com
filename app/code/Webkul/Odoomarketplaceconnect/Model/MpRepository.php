<?php
/**
 * Webkul Odoomarketplaceconnect MpRepository Model
 * @category  Webkul
 * @package   Webkul_Odoomarketplaceconnect
 * @author    Webkul
 * @copyright Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */


namespace Webkul\Odoomarketplaceconnect\Model;

use Webkul\Odoomarketplaceconnect\Api\MpRepositoryInterface;
use Webkul\Odoomagentoconnect\Helper\Connection;

/**
 * Defines the implementation class of the mp repository service contract.
 */
class MpRepository implements MpRepositoryInterface
{
    /**
     * Constructor.
     *
     */
    public function __construct(
        Connection $connection,
        \Magento\Framework\Stdlib\DateTime\DateTime $date
    ) {
        $this->_date = $date;
        $this->_connection = $connection;
    }

    /**
     * Set seller product
     *
     * @param int $odooId
     * @param mixed $itemData
     * @return int
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\StateException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function setVendorProduct($odooId, $itemData = [])
    {
        $mpProductId = 0;
        $helper = $this->_connection;
        $mpProductModel = $helper->getModel(\Webkul\Marketplace\Model\Product::class);
        $productCollection = $mpProductModel->getCollection()->addFieldToFilter('mageproduct_id', $itemData['mageproduct_id']);
        if (count($productCollection) == 0) {
            $itemData['created_at'] = $this->_date->gmtDate();
            $mpProductModel->setData($itemData)->save();

            $mpProductId = $mpProductModel->getId();
            $productModel = $helper->getModel(\Webkul\Odoomarketplaceconnect\Model\Product::class);
            $data = [
                'odoo_id'=>$odooId,
                'mp_product_id'=>$mpProductId,
                'mp_seller_id'=>$itemData['seller_id'],
                'magento_id'=>$itemData['mageproduct_id'],
                'created_by'=>'Odoo',
                'need_sync'=>'no'
            ];
            $productModel->setData($data)->save();
        } else {
            foreach ($productCollection as $product) {
                $product->setUpdatedAt($this->_date->gmtDate())->save();
            }
        }
        return $mpProductId;
    }

    /**
     * Set seller status
     *
     * @param int $sellerId
     * @param int $status
     * @return int
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\StateException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function setVendorStatus($sellerId, $status)
    {
        $resp = false;
        $mpSeller = $this->_connection->getModel(\Webkul\Marketplace\Model\Seller::class)->load($sellerId);
        if ($mpSeller) {
            $mpSeller->setIsSeller($status)->save();
            $mpSeller->save();
            $resp = true;
        }
        return $resp;
    }
}
