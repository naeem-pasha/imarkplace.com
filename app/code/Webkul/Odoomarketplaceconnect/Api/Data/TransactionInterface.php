<?php
/**
 * Webkul Odoomarketplaceconnect Transaction Interface
 * @category  Webkul
 * @package   Webkul_Odoomarketplaceconnect
 * @author    Webkul
 * @copyright Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\Odoomarketplaceconnect\Api\Data;

interface TransactionInterface
{
    /**
    * Constants for keys of data array. Identical to the name of the getter in snake case
    */
    const ODOO_ID    = 'odoo_id';
    const MAGENTO_ID    = 'magento_id';
    const ORDER_ID    = 'order_id';
    const CREATED_BY    = 'created_by';
    const SELLER_ID    = 'seller_id';
 
    /**
     * Get odooId
     *
     * @return int|null
     */
    public function getOdooId();
    
    /**
     * Get the magento_id.
     *
     * @api
     * @return int|null
     */
    public function getMagentoId();

    /**
     * Get orderId
     *
     * @api
     * @param int $orderId
     * @return $this
     */
    public function getOrderId();

    /**
     * Get sellerId
     *
     * @api
     * @param int $sellerId
     * @return $this
     */
    public function getSellerId();

    /**
     * Get Created By
     *
     * @return string|null
     */
    public function getCreatedBy();


    /**
     * Set odooId
     *
     * @api
     * @param int $odooId
     * @return $this
     */
    public function setOdooId($odooId);

    /**
     * Set magentoId
     *
     * @api
     * @param int $magentoId
     * @return $this
     */
    public function setMagentoId($magentoId);

    /**
     * Get orderId
     *
     * @api
     * @param int $orderId
     * @return $this
     */
    public function setOrderId($orderId);

    /**
     * Get sellerId
     *
     * @api
     * @param int $sellerId
     * @return $this
     */
    public function setSellerId($sellerId);

    /**
     * Set created_by
     *
     * @api
     * @param string $created_by
     * @return $this
     */
    public function setCreatedBy($createdBy);
}
