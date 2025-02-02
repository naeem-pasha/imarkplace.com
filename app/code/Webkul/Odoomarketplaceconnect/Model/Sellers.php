<?php
/**
 * Webkul Odoomarketplaceconnect Sellers
 * @category  Webkul
 * @package   Webkul_Odoomarketplaceconnect
 * @author    Webkul
 * @copyright Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\Odoomarketplaceconnect\Model;

use Webkul\Odoomarketplaceconnect\Api\Data\SellersInterface;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;

class Sellers extends AbstractModel implements SellersInterface, IdentityInterface
{

    protected $_interfaceSellers = [

    SellersInterface::ODOO_ID,
    SellersInterface::MAGENTO_ID,
    SellersInterface::CREATED_BY,
    SellersInterface::MP_SELLER_ID,
    SellersInterface::NEED_SYNC,
    ];


    /**
     * CMS page cache tag
     */
    const CACHE_TAG = 'odoomarketplaceconnect_sellers';

    /**
     * @var string
     */
    protected $_cacheTag = 'odoomarketplaceconnect_sellers';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'odoomarketplaceconnect_sellers';

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Webkul\Odoomarketplaceconnect\Model\ResourceModel\Sellers');
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
     * Get the Need Sync.
     *
     * @api
     * @return string|null
     */

    public function getNeedSync()
    {
        return $this->getData(self::NEED_SYNC);
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

    /**
     * Set the Need Sync.
     *
     * @api
     * @return string|null
     */

    public function setNeedSync($needSync)
    {
        return $this->setData(self::NEED_SYNC, $needSync);
    }
}
