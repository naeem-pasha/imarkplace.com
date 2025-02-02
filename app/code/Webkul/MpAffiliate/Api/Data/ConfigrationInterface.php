<?php
/**
 * Affiliate User Interface.
 * @category  Webkul
 * @package   Webkul_Affiliate
 * @author    Webkul
 * @copyright Copyright (c)  Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\MpAffiliate\Api\Data;

interface ConfigrationInterface
{
    /**
     * Constants for keys of data array.
     * Identical to the name of the getter in snake case.
     */
    const ENTITY_ID = 'entity_id';

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
}
