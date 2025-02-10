<?php
/**
 * Webkul Software.
 *
 * @category Webkul
 * @package Webkul_MarketplaceLearningManagementSystem
 * @author Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license https://store.webkul.com/license.html
 */


namespace Webkul\MarketplaceLearningManagementSystem\Model;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\DataObject\IdentityInterface;
use Webkul\MarketplaceLearningManagementSystem\Api\Data\CourseStatusInterface;

/**
 * Course Product Status
 */
class CourseStatus extends AbstractModel implements IdentityInterface, CourseStatusInterface
{

    const NOROUTE_ENTITY_ID = 'no-route';

    const CACHE_TAG = 'webkul_marketplacelearningmanagementsystem_coursestatus';

    protected $_cacheTag = 'webkul_marketplacelearningmanagementsystem_coursestatus';

    protected $_eventPrefix = 'webkul_marketplacelearningmanagementsystem_coursestatus';

    /**
     * set resource model
     */
    public function _construct()
    {
        $this->_init(\Webkul\MarketplaceLearningManagementSystem\Model\ResourceModel\CourseStatus::class);
    }

    /**
     * Load No-Route Indexer.
     *
     * @return $this
     */
    public function noRouteReasons()
    {
        return $this->load(self::NOROUTE_ENTITY_ID, $this->getIdFieldName());
    }

    /**
     * Get identities.
     *
     * @return []
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG.'_'.$this->getId()];
    }

    /**
     * Set EntityId
     *
     * @param int $entityId
     * @return Webkul\MarketplaceLearningManagementSystem\Model\CourseStatusInterface
     */
    public function setEntityId($entityId)
    {
        return $this->setData(self::ENTITY_ID, $entityId);
    }

    /**
     * Get EntityId
     *
     * @return int
     */
    public function getEntityId()
    {
        return parent::getData(self::ENTITY_ID);
    }

    /**
     * Set CourseId
     *
     * @param int $courseId
     * @return Webkul\MarketplaceLearningManagementSystem\Model\CourseStatusInterface
     */
    public function setCourseId($courseId)
    {
        return $this->setData(self::COURSE_ID, $courseId);
    }

    /**
     * Get CourseId
     *
     * @return int
     */
    public function getCourseId()
    {
        return parent::getData(self::COURSE_ID);
    }

    /**
     * Set CustomerId
     *
     * @param int $customerId
     * @return Webkul\MarketplaceLearningManagementSystem\Model\CourseStatusInterface
     */
    public function setCustomerId($customerId)
    {
        return $this->setData(self::CUSTOMER_ID, $customerId);
    }

    /**
     * Get CustomerId
     *
     * @return int
     */
    public function getCustomerId()
    {
        return parent::getData(self::CUSTOMER_ID);
    }

    /**
     * Set ContentId
     *
     * @param int $contentId
     * @return Webkul\MarketplaceLearningManagementSystem\Model\CourseStatusInterface
     */
    public function setContentId($contentId)
    {
        return $this->setData(self::CONTENT_ID, $contentId);
    }

    /**
     * Get ContentId
     *
     * @return int
     */
    public function getContentId()
    {
        return parent::getData(self::CONTENT_ID);
    }
}
