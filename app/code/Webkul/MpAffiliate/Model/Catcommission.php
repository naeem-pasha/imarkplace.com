<?php
/**
 * @category  Webkul
 * @package   Webkul_MpAffiliate
 * @author    Webkul
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\MpAffiliate\Model;

use Webkul\MpAffiliate\Api\Data\CatcommissionInterface;
use Magento\Framework\DataObject\IdentityInterface;

class Catcommission extends \Magento\Framework\Model\AbstractModel implements CatcommissionInterface
{
    /**
     * CMS page cache tag.
     */
    const CACHE_TAG = 'mp_affiliate_catcommission';

    /**
     * @var string
     */
    protected $_cacheTag = 'mp_affiliate_catcommission';

    /**
     * Prefix of model events names.
     *
     * @var string
     */
    protected $_eventPrefix = 'mp_affiliate_catcommission';

    /**
     * Initialize resource model.
     */
    protected function _construct()
    {
        $this->_init(\Webkul\MpAffiliate\Model\ResourceModel\Catcommission::class);
    }

    /**
     * Load object data
     *
     * @param int|null $id
     * @param string $field
     * @return $this
     */
    public function load($id, $field = null)
    {
        if ($id === null) {
            return $this->noRoutePayment();
        }
        return parent::load($id, $field);
    }

    /**
     * Load No-Route User
     *
     * @return \Webkul\MpAffiliate\Model\User
     */
    public function noRoutePayment()
    {
        return $this->load(self::NOROUTE_ENTITY_ID, $this->getIdFieldName());
    }

    /**
     * Get identities
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * Get Id.
     *
     * @return int
     */
    public function getEntityId()
    {
        return $this->getData(self::ENTITY_ID);
    }

    /**
     * Set Id.
     */
    public function setEntityId($entityId)
    {
        return $this->setData(self::ENTITY_ID, $entityId);
    }

    /**
     * Get SellerId.
     *
     * @return int
     */
    public function getSellerId()
    {
        return $this->getData(self::SELLER_ID);
    }

    /**
     * Set SellerId.
     */
    public function setSellerId($data)
    {
        return $this->setData(self::SELLER_ID, $data);
    }

    /**
     * Get CategoryId.
     *
     * @return int
     */
    public function getCategoryId()
    {
        return $this->getData(self::CATEGORY_ID);
    }

    /**
     * Set CategoryId.
     */
    public function setCategoryId($data)
    {
        return $this->setData(self::CATEGORY_ID, $data);
    }

    /**
     * Get Commission Type.
     *
     * @return varchar
     */
    public function getCommissionType()
    {
        return $this->getData(self::COMMISSION_TYPE);
    }

    /**
     * Set Commission Type.
     */
    public function setCommissionType($data)
    {
        return $this->setData(self::COMMISSION_TYPE, $data);
    }

    /**
     * Get Commission.
     *
     * @return varchar
     */
    public function getCommission()
    {
        return $this->getData(self::COMMISSION);
    }

    /**
     * Set Commission.
     */
    public function setCommission($data)
    {
        return $this->setData(self::COMMISSION, $data);
    }
}
