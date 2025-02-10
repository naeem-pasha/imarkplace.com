<?php
/**
 * Webkul Odoomarketplaceconnect MpRepositoryInterface
 * @category  Webkul
 * @package   Webkul_Odoomarketplaceconnect
 * @author    Webkul
 * @copyright Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\Odoomarketplaceconnect\Api;

/**
 * @api
 */
interface MpRepositoryInterface
{
    /**
     * Set seller's product
     *
     * @param int $odooId
     * @param mixed $itemData
     * @return int
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\StateException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function setVendorProduct($odooId, $itemData = []);

    /**
     * Sets seller's status
     *
     * @param int $sellerId
     * @param int $status
     * @return int
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\StateException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function setVendorStatus($sellerId, $status);
}
