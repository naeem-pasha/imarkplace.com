<?php
/**
 * Affiliate User Interface.
 * @category  Webkul
 * @package   Webkul_Affiliate
 * @author    Webkul
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\MpAffiliate\Api\Data;

interface CatcommissionInterface
{
    /**
     * Constants for keys of data array.
     * Identical to the name of the getter in snake case.
     */
    const ENTITY_ID = 'entity_id';
    const SELLER_ID = 'seller_id';
    const CATEGORY_ID = 'category_id';
    const COMMISSION_TYPE = 'commission_type';
    const COMMISSION = 'commission';

    /**
     * Get ID
     *
     * @return int|null
     */
    public function getEntityId();

    /**
     * set ID
     *
     * @return $this
     */
    public function setEntityId($entityId);

    /**
     * Get CommissionType
     * @return varchar
     */
    public function getCommissionType();

    /**
     * set CommissionType
     * @return $this
     */
    public function setCommissionType($commissionType);

    /**
     * Get Commission
     * @return varchar
     */
    public function getCommission();

    /**
     * set Commission
     * @return $this
     */
    public function setCommission($commission);
}
