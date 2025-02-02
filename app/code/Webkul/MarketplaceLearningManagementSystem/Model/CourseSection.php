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

use Webkul\MarketplaceLearningManagementSystem\Api\Data\CourseSectionInterface;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;

/**
 * Course product Sections
 */
class CourseSection extends AbstractModel implements IdentityInterface, CourseSectionInterface
{
    const NOROUTE_ENTITY_ID = 'no-route';

    const CACHE_TAG = 'webkul_marketplacelearningmanagementsystem_coursesection';

    protected $_cacheTag = 'webkul_marketplacelearningmanagementsystem_coursesection';

    protected $_eventPrefix = 'webkul_marketplacelearningmanagementsystem_coursesection';

    /**
     * set resource model
     */
    public function _construct()
    {
        $this->_init(\Webkul\MarketplaceLearningManagementSystem\Model\ResourceModel\CourseSection::class);
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
     * @return Webkul\MarketplaceLearningManagementSystem\Model\CourseSectionInterface
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
     * @return Webkul\MarketplaceLearningManagementSystem\Model\CourseSectionInterface
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
     * Set Title
     *
     * @param string $title
     * @return Webkul\MarketplaceLearningManagementSystem\Model\CourseSectionInterface
     */
    public function setTitle($title)
    {
        return $this->setData(self::TITLE, $title);
    }

    /**
     * Get Title
     *
     * @return string
     */
    public function getTitle()
    {
        return parent::getData(self::TITLE);
    }

    /**
     * Set SortOrder
     *
     * @param int $sortOrder
     * @return Webkul\MarketplaceLearningManagementSystem\Model\CourseSectionInterface
     */
    public function setSortOrder($sortOrder)
    {
        return $this->setData(self::SORT_ORDER, $sortOrder);
    }

    /**
     * Get SortOrder
     *
     * @return int
     */
    public function getSortOrder()
    {
        return parent::getData(self::SORT_ORDER);
    }
}
