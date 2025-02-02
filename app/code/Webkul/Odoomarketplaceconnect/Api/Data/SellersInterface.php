<?php
/**
 * Webkul Odoomarketplaceconnect Sellers Interface
 * @category  Webkul
 * @package   Webkul_Odoomarketplaceconnect
 * @author    Webkul
 * @copyright Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\Odoomarketplaceconnect\Api\Data;

interface SellersInterface
{
    /**
    * Constants for keys of data array. Identical to the name of the getter in snake case
    */
    const ODOO_ID    = 'odoo_id';
    const MAGENTO_ID    = 'magento_id';
    const MP_SELLER_ID    = 'mp_seller_id';
    const CREATED_BY    = 'created_by';
    const NEED_SYNC    = 'need_sync';

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
     * Get mpSellerId
     *
     * @api
     * @param int $mpSellerId
     * @return $this
     */
    public function getMpSellerId();
    
    /**
     * Get the need_sync.
     *
     * @api
     * @return string|null
     */
    public function getNeedSync();

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
     * Set mpSellerId
     *
     * @api
     * @param int $mpSellerId
     * @return $this
     */
    public function setMpSellerId($mpSellerId);

    /**
     * Set created_by
     *
     * @api
     * @param string $created_by
     * @return $this
     */
    public function setCreatedBy($createdBy);

    /**
     * Set need_sync
     *
     * @api
     * @param string $need_sync
     * @return $this
     */
    public function setNeedSync($needSync);
}
