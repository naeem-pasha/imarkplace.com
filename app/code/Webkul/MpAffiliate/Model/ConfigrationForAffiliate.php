<?php

/**
 * MpAffiliate User Model.
 * @category  Webkul
 * @package   Webkul_MpAffiliate
 * @author    Webkul
 * @copyright Copyright (c)  Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\MpAffiliate\Model;

use Webkul\MpAffiliate\Api\Data\ConfigrationInterface;
use Magento\Framework\DataObject\IdentityInterface;

class ConfigrationForAffiliate extends \Magento\Framework\Model\AbstractModel implements ConfigrationInterface
{
    /**
     * CMS page cache tag.
     */
    const CACHE_TAG = 'mp_affiliate_setting';

    /**
     * @var string
     */
    protected $_cacheTag = 'mp_affiliate_setting';

    /**
     * Prefix of model events names.
     *
     * @var string
     */
    protected $_eventPrefix = 'mp_affiliate_setting';

    /**
     * Initialize resource model.
     */
    protected function _construct()
    {
        $this->_init(\Webkul\MpAffiliate\Model\ResourceModel\ConfigrationForAffiliate::class);
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
}
