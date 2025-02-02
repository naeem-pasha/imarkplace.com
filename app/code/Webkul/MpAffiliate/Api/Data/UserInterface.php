<?php
/**
 * @category  Webkul
 * @package   Webkul_MpAffiliate
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\MpAffiliate\Api\Data;

interface UserInterface
{
    /**
    * Constants for keys of data array.
    * Identical to the name of the getter in snake case.
    */
    const ENTITY_ID = 'entity_id';
    const CUSTOMER_ID = 'customer_id';
    const STATUS = 'status';
    const CREATED_AT = 'created_at';

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
     * Get CustomerId
     * @return string
     */
    public function getCustomerId();

    /**
     * set CustomerId
     * @return $this
     */
    public function setCustomerId($customerId);

    /**
     * Get Status
     * @return string
     */
    public function getStatus();

    /**
     * set Enable
     * @return $this
     */
    public function setStatus($status);

    /**
     * Get CreatedAt.
     * @return string
     */
    public function getCreatedAt();

    /**
     * set CreatedAt.
     * @return $this
     */
    public function setCreatedAt($createdAt);
}
