<?php

namespace Webkul\Odoomarketplaceconnect\Model;

/**
 * Webkul Odoomarketplaceconnect Transaction
 * @category  Webkul
 * @package   Webkul_Odoomarketplaceconnect
 * @author    Webkul
 * @copyright Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

use Webkul\Odoomarketplaceconnect\Api\Data\TransactionInterface;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;

class Transaction extends AbstractModel implements TransactionInterface, IdentityInterface
{

    protected $_interfaceTransaction = [
   
    TransactionInterface::ODOO_ID,
    TransactionInterface::MAGENTO_ID,
    TransactionInterface::CREATED_BY,
    TransactionInterface::SELLER_ID,
    TransactionInterface::ORDER_ID,
    ];


    /**#@+
     * Post's Statuses
     */
    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 0;
    /**#@-*/

    /**
     * CMS page cache tag
     */
    const CACHE_TAG = 'odoomarketplaceconnect_transaction';

    /**
     * @var string
     */
    protected $_cacheTag = 'odoomarketplaceconnect_transaction';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'odoomarketplaceconnect_transaction';

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Webkul\Odoomarketplaceconnect\Model\ResourceModel\Transaction');
    }


    /**
     * Prepare post's statuses.
     * Available event to customize statuses.
     *
     * @return array
     */
    public function getAvailableStatuses()
    {
        return [self::STATUS_ENABLED => __('Enabled'), self::STATUS_DISABLED => __('Disabled')];
    }
    /**
     * Return unique ID(s) for each object in system
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * Get the odoo id.
     *
     * @api
     * @return int|null
     */

    public function getOdooId()
    {
        return $this->getData(self::ODOO_ID);
    }

    /**
     * Get the magentoId.
     *
     * @api
     * @return int|null
     */

    public function getMagentoId()
    {
        return $this->getData(self::MAGENTO_ID);
    }

    /**
     * Get the createdBy.
     *
     * @api
     * @return string|null
     */

    public function getCreatedBy()
    {
        return $this->getData(self::CREATED_BY);
    }

    /**
     * Get the sellerId.
     *
     * @api
     * @return int|null
     */

    public function getSellerId()
    {
        return $this->getData(self::SELLER_ID);
    }

    /**
     * Get the orderId.
     *
     * @api
     * @return int|null
     */

    public function getOrderId()
    {
        return $this->getData(self::ORDER_ID);
    }

    /**
     * Set odooId
     *
     * @api
     * @param int $odooId
     * @return $this
     */
    public function setOdooId($odooId)
    {
        return $this->setData(self::ODOO_ID, $odooId);
    }

    /**
     * Set magentoId
     *
     * @api
     * @param int $magentoId
     * @return $this
     */

    public function setMagentoId($magentoId)
    {
        return $this->setData(self::MAGENTO_ID, $magentoId);
    }

    /**
     * Set the sellerId.
     *
     * @api
     * @return int|null
     */

    public function setSellerId($sellerId)
    {
        return $this->setData(self::SELLER_ID, $sellerId);
    }

    /**
     * Set the orderId.
     *
     * @api
     * @return int|null
     */

    public function setOrderId($orderId)
    {
        return $this->getData(self::ORDER_ID, $orderId);
    }

    /**
     * Set createdBy
     *
     * @api
     * @param string $createdBy
     * @return $this
     */
    public function setCreatedBy($createdBy)
    {
        return $this->setData(self::CREATED_BY, $createdBy);
    }
}
