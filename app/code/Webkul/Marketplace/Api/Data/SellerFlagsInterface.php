<?php
/**
 * Webkul Software
 *
 * @category Webkul
 * @package Webkul_Marketplace
 * @author Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license https://store.webkul.com/license.html
 */

namespace Webkul\Marketplace\Api\Data;

interface SellerFlagsInterface
{
    const ENTITY_ID  = 'entity_id';
    const SELLER_ID  = 'seller_id';
    const REASON     = 'reason';
    const NAME       = 'name';
    const EMAIL      = 'email';
    const CREATED_AT = 'created_at';

    /**
     * Gets the entity ID.
     *
     * @return int Entity ID.
     */
    public function getEntityId();

    /**
     * Sets entity ID.
     *
     * @param int $entityId
     * @return $this
     */
    public function setEntityId($entityId);

    /**
     * Gets the Seller ID.
     *
     * @return int Seller ID.
     */
    public function getSellerId();

    /**
     * Sets Seller ID.
     *
     * @param int $sellerId
     * @return $this
     */
    public function setSellerId($sellerId);

    /**
     * Gets the Reason.
     *
     * @return string Reason
     */
    public function getReason();

    /**
     * Sets the Reason.
     *
     * @param string $reason
     * @return $this
     */
    public function setReason($reason);

    /**
     * Gets the Name.
     *
     * @return string Name
     */
    public function getName();

    /**
     * Sets the Name.
     *
     * @param string $name
     * @return $this
     */
    public function setName($name);

    /**
     * Gets the Email.
     *
     * @return string Email
     */
    public function getEmail();

    /**
     * Sets the Email.
     *
     * @param string $email
     * @return $this
     */
    public function setEmail($email);

    /**
     * Gets creation timestamp.
     *
     * @return string
     */
    public function getCreatedAt();

    /**
     * Sets creation timestamp.
     *
     * @param string $timestamp
     * @return $this
     */
    public function setCreatedAt($timestamp);
}
