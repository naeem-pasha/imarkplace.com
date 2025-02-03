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
use Webkul\MarketplaceLearningManagementSystem\Api\Data\QARecordInterface;
use Magento\Framework\DataObject\IdentityInterface;

/**
 * Questions Record
 */
class QARecord extends AbstractModel implements IdentityInterface, QARecordInterface
{

    const NOROUTE_ENTITY_ID = 'no-route';

    const CACHE_TAG = 'webkul_marketplacelearningmanagementsystem_qarecord';

    protected $_cacheTag = 'webkul_marketplacelearningmanagementsystem_qarecord';

    protected $_eventPrefix = 'webkul_marketplacelearningmanagementsystem_qarecord';

    /**
     * set resource model
     */
    public function _construct()
    {
        $this->_init(\Webkul\MarketplaceLearningManagementSystem\Model\ResourceModel\QARecord::class);
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
     * @return Webkul\MarketplaceLearningManagementSystem\Model\QARecordInterface
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
     * Set StudentId
     *
     * @param int $studentId
     * @return Webkul\MarketplaceLearningManagementSystem\Model\QARecordInterface
     */
    public function setStudentId($studentId)
    {
        return $this->setData(self::STUDENT_ID, $studentId);
    }

    /**
     * Get StudentId
     *
     * @return int
     */
    public function getStudentId()
    {
        return parent::getData(self::STUDENT_ID);
    }

    /**
     * Set StudentName
     *
     * @param string $studentName
     * @return Webkul\MarketplaceLearningManagementSystem\Model\QARecordInterface
     */
    public function setStudentName($studentName)
    {
        return $this->setData(self::STUDENT_NAME, $studentName);
    }

    /**
     * Get StudentName
     *
     * @return string
     */
    public function getStudentName()
    {
        return parent::getData(self::STUDENT_NAME);
    }

    /**
     * Set Title
     *
     * @param string $title
     * @return Webkul\MarketplaceLearningManagementSystem\Model\QARecordInterface
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
     * Set Detail
     *
     * @param string $detail
     * @return Webkul\MarketplaceLearningManagementSystem\Model\QARecordInterface
     */
    public function setDetail($detail)
    {
        return $this->setData(self::DETAIL, $detail);
    }

    /**
     * Get Detail
     *
     * @return string
     */
    public function getDetail()
    {
        return parent::getData(self::DETAIL);
    }

    /**
     * Set Chapter
     *
     * @param int $chapter
     * @return Webkul\MarketplaceLearningManagementSystem\Model\QARecordInterface
     */
    public function setChapter($chapter)
    {
        return $this->setData(self::CHAPTER, $chapter);
    }

    /**
     * Get Chapter
     *
     * @return int
     */
    public function getChapter()
    {
        return parent::getData(self::CHAPTER);
    }

    /**
     * Set Replies
     *
     * @param int $replies
     * @return Webkul\MarketplaceLearningManagementSystem\Model\QARecordInterface
     */
    public function setReplies($replies)
    {
        return $this->setData(self::REPLIES, $replies);
    }

    /**
     * Get Replies
     *
     * @return int
     */
    public function getReplies()
    {
        return parent::getData(self::REPLIES);
    }

    /**
     * Set CreatedDate
     *
     * @param string $createdDate
     * @return Webkul\MarketplaceLearningManagementSystem\Model\QARecordInterface
     */
    public function setCreatedDate($createdDate)
    {
        return $this->setData(self::CREATED_DATE, $createdDate);
    }

    /**
     * Get CreatedDate
     *
     * @return string
     */
    public function getCreatedDate()
    {
        return parent::getData(self::CREATED_DATE);
    }

    /**
     * Set Status
     *
     * @param string $status
     * @return Webkul\MarketplaceLearningManagementSystem\Model\QARecordInterface
     */
    public function setStatus($status)
    {
        return $this->setData(self::STATUS, $status);
    }

    /**
     * Get Status
     *
     * @return string
     */
    public function getStatus()
    {
        return parent::getData(self::STATUS);
    }

    /**
     * Set CourseId
     *
     * @param int $courseId
     * @return Webkul\MarketplaceLearningManagementSystem\Model\QARecordInterface
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
}
