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

use Webkul\MarketplaceLearningManagementSystem\Api\Data\CourseContentInterface;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;

/**
 * Course Content Model
 */
class CourseContent extends AbstractModel implements IdentityInterface, CourseContentInterface
{

    const NOROUTE_ENTITY_ID = 'no-route';

    const CACHE_TAG = 'webkul_marketplacelearningmanagementsystem_coursecontent';

    protected $_cacheTag = 'webkul_marketplacelearningmanagementsystem_coursecontent';

    protected $_eventPrefix = 'webkul_marketplacelearningmanagementsystem_coursecontent';

    /**
     * set resource model
     */
    public function _construct()
    {
        $this->_init(\Webkul\MarketplaceLearningManagementSystem\Model\ResourceModel\CourseContent::class);
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
     * @return Webkul\MarketplaceLearningManagementSystem\Model\CourseContentInterface
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
     * @return Webkul\MarketplaceLearningManagementSystem\Model\CourseContentInterface
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
     * @return Webkul\MarketplaceLearningManagementSystem\Model\CourseContentInterface
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
     * Set Description
     *
     * @param string $description
     * @return Webkul\MarketplaceLearningManagementSystem\Model\CourseContentInterface
     */
    public function setDescription($description)
    {
        return $this->setData(self::DESCRIPTION, $description);
    }

    /**
     * Get Description
     *
     * @return string
     */
    public function getDescription()
    {
        return parent::getData(self::DESCRIPTION);
    }

    /**
     * Set Section
     *
     * @param string $section
     * @return Webkul\MarketplaceLearningManagementSystem\Model\CourseContentInterface
     */
    public function setSection($section)
    {
        return $this->setData(self::SECTION, $section);
    }

    /**
     * Get Section
     *
     * @return string
     */
    public function getSection()
    {
        return parent::getData(self::SECTION);
    }

    /**
     * Set Type
     *
     * @param string $type
     * @return Webkul\MarketplaceLearningManagementSystem\Model\CourseContentInterface
     */
    public function setType($type)
    {
        return $this->setData(self::TYPE, $type);
    }

    /**
     * Get Type
     *
     * @return string
     */
    public function getType()
    {
        return parent::getData(self::TYPE);
    }

    /**
     * Set Preview
     *
     * @param int $preview
     * @return Webkul\MarketplaceLearningManagementSystem\Model\CourseContentInterface
     */
    public function setPreview($preview)
    {
        return $this->setData(self::PREVIEW, $preview);
    }

    /**
     * Get Preview
     *
     * @return int
     */
    public function getPreview()
    {
        return parent::getData(self::PREVIEW);
    }

    /**
     * Set FileName
     *
     * @param string $fileName
     * @return Webkul\MarketplaceLearningManagementSystem\Model\CourseContentInterface
     */
    public function setFileName($fileName)
    {
        return $this->setData(self::FILE_NAME, $fileName);
    }

    /**
     * Get FileName
     *
     * @return string
     */
    public function getFileName()
    {
        return parent::getData(self::FILE_NAME);
    }

    /**
     * Set FilePath
     *
     * @param string $filePath
     * @return Webkul\MarketplaceLearningManagementSystem\Model\CourseContentInterface
     */
    public function setFilePath($filePath)
    {
        return $this->setData(self::FILE_PATH, $filePath);
    }

    /**
     * Get FilePath
     *
     * @return string
     */
    public function getFilePath()
    {
        return parent::getData(self::FILE_PATH);
    }

    /**
     * Set Duration
     *
     * @param string $duration
     * @return Webkul\MarketplaceLearningManagementSystem\Model\CourseContentInterface
     */
    public function setDuration($duration)
    {
        return $this->setData(self::DURATION, $duration);
    }

    /**
     * Get Duration
     *
     * @return string
     */
    public function getDuration()
    {
        return parent::getData(self::DURATION);
    }
}
