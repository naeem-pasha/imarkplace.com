<?php
/**
 * Webkul Odoomarketplaceconnect Order Interface
 * @category  Webkul
 * @package   Webkul_Odoomarketplaceconnect
 * @author    Webkul
 * @copyright Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\Odoomarketplaceconnect\Api\Data;

interface OrderInterface
{
    /**
    * Constants for keys of data array. Identical to the name of the getter in snake case
    */
    const ODOO_ID    = 'odoo_id';
    const MAGENTO_ID    = 'magento_id';
    const ODOO_LINE_ID    = 'odoo_line_id';
    const MAGENTO_LINE_ID    = 'magento_line_id';
    const CREATED_BY    = 'created_by';
    const MP_SELLER_ID    = 'mp_seller_id';
    const MP_SALE_ID    = 'mp_sale_id';

    /**
     * Get odooId
     *
     * @return int|null
     */
    public function getOdooId();
    
    /**
     * Get magentoId.
     *
     * @api
     * @return int|null
     */
    public function getMagentoId();

    /**
     * Get odooLineId
     *
     * @api
     * @param int $odooLineId
     * @return $this
     */
    public function getOdooLineId();

    /**
     * Get magentoLineId
     *
     * @api
     * @param int $magentoLineId
     * @return $this
     */
    public function getMagentoLineId();

    /**
     * Get mpSellerId
     *
     * @api
     * @param int $mpSellerId
     * @return $this
     */
    public function getMpSellerId();

    /**
     * Get mpSaleId
     *
     * @api
     * @param int $mpSaleId
     * @return $this
     */
    public function getMpSaleId();

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
     * Get odooLineId
     *
     * @api
     * @param int $odooLineId
     * @return $this
     */
    public function setOdooLineId($odooLineId);

    /**
     * Get magentoLineId
     *
     * @api
     * @param int $magentoLineId
     * @return $this
     */
    public function setMagentoLineId($magentoLineId);

    /**
     * Get mpSellerId
     *
     * @api
     * @param int $mpSellerId
     * @return $this
     */
    public function setMpSellerId($mpSellerId);

    /**
     * Get mpSaleId
     *
     * @api
     * @param int $mpSaleId
     * @return $this
     */
    public function setMpSaleId($mpSaleId);

    /**
     * Set created_by
     *
     * @api
     * @param string $created_by
     * @return $this
     */
    public function setCreatedBy($createdBy);
}
