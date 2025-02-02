<?php
/**
 * Webkul Odoomarketplaceconnect Order
 * @category  Webkul
 * @package   Webkul_Odoomarketplaceconnect
 * @author    Webkul
 * @copyright Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\Odoomarketplaceconnect\Model;

use Webkul\Odoomarketplaceconnect\Api\Data\OrderInterface;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;

class Order extends AbstractModel implements OrderInterface, IdentityInterface
{

    protected $_interfaceOrder = [
   
    OrderInterface::ODOO_ID,
    OrderInterface::MAGENTO_ID,
    OrderInterface::CREATED_BY,
    OrderInterface::ODOO_LINE_ID,
    OrderInterface::MAGENTO_LINE_ID,
    OrderInterface::MP_SELLER_ID,
    OrderInterface::MP_SALE_ID,
    ];

    /**
     * CMS page cache tag
     */
    const CACHE_TAG = 'odoomarketplaceconnect_order';

    /**
     * @var string
     */
    protected $_cacheTag = 'odoomarketplaceconnect_order';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'odoomarketplaceconnect_order';

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Webkul\Odoomarketplaceconnect\Model\ResourceModel\Order');
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
     * Get the odoo line id.
     *
     * @api
     * @return int|null
     */

    public function getOdooLineId()
    {
        return $this->getData(self::ODOO_LINE_ID);
    }

    /**
     * Get the magento line Id.
     *
     * @api
     * @return int|null
     */

    public function getMagentoLineId()
    {
        return $this->getData(self::MAGENTO_LINE_ID);
    }

    /**
     * Get the mpSellerId.
     *
     * @api
     * @return int|null
     */

    public function getMpSellerId()
    {
        return $this->getData(self::MP_SELLER_ID);
    }

    /**
     * Get the MP SALE ID.
     *
     * @api
     * @return int|null
     */

    public function getMpSaleId()
    {
        return $this->getData(self::MP_SALE_ID);
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
     * Set the odoo line id.
     *
     * @api
     * @return int|null
     */

    public function setOdooLineId($odooLineId)
    {
        return $this->setData(self::ODOO_LINE_ID, $odooLineId);
    }

    /**
     * Set the magento line Id.
     *
     * @api
     * @return int|null
     */

    public function setMagentoLineId($magentoLineId)
    {
        return $this->setData(self::MAGENTO_LINE_ID, $magentoLineId);
    }

    /**
     * Set the mpSellerId.
     *
     * @api
     * @return int|null
     */

    public function setMpSellerId($mpSellerId)
    {
        return $this->setData(self::MP_SELLER_ID, $mpSellerId);
    }

    /**
     * Set the MP SALE ID.
     *
     * @api
     * @return int|null
     */

    public function setMpSaleId($mpSaleId)
    {
        return $this->setData(self::MP_SALE_ID, $mpSaleId);
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
