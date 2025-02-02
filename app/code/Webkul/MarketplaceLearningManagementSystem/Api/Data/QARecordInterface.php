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


namespace Webkul\MarketplaceLearningManagementSystem\Api\Data;

/**
 * QARecord
 */
interface QARecordInterface
{

    const ENTITY_ID = 'entity_id';

    const STUDENT_ID = 'student_id';

    const STUDENT_NAME = 'student_name';

    const TITLE = 'title';

    const DETAIL = 'detail';

    const CHAPTER = 'chapter';

    const REPLIES = 'replies';

    const CREATED_DATE = 'created_date';

    const STATUS = 'status';

    const COURSE_ID = 'course_id';

    /**
     * Set EntityId
     *
     * @param int $entityId
     * @return Webkul\MarketplaceLearningManagementSystem\Api\Data\QARecordInterface
     */
    public function setEntityId($entityId);
    /**
     * Get EntityId
     *
     * @return int
     */
    public function getEntityId();
    /**
     * Set StudentId
     *
     * @param int $studentId
     * @return Webkul\MarketplaceLearningManagementSystem\Api\Data\QARecordInterface
     */
    public function setStudentId($studentId);
    /**
     * Get StudentId
     *
     * @return int
     */
    public function getStudentId();
    /**
     * Set StudentName
     *
     * @param string $studentName
     * @return Webkul\MarketplaceLearningManagementSystem\Api\Data\QARecordInterface
     */
    public function setStudentName($studentName);
    /**
     * Get StudentName
     *
     * @return string
     */
    public function getStudentName();
    /**
     * Set Title
     *
     * @param string $title
     * @return Webkul\MarketplaceLearningManagementSystem\Api\Data\QARecordInterface
     */
    public function setTitle($title);
    /**
     * Get Title
     *
     * @return string
     */
    public function getTitle();
    /**
     * Set Detail
     *
     * @param string $detail
     * @return Webkul\MarketplaceLearningManagementSystem\Api\Data\QARecordInterface
     */
    public function setDetail($detail);
    /**
     * Get Detail
     *
     * @return string
     */
    public function getDetail();
    /**
     * Set Chapter
     *
     * @param int $chapter
     * @return Webkul\MarketplaceLearningManagementSystem\Api\Data\QARecordInterface
     */
    public function setChapter($chapter);
    /**
     * Get Chapter
     *
     * @return int
     */
    public function getChapter();
    /**
     * Set Replies
     *
     * @param int $replies
     * @return Webkul\MarketplaceLearningManagementSystem\Api\Data\QARecordInterface
     */
    public function setReplies($replies);
    /**
     * Get Replies
     *
     * @return int
     */
    public function getReplies();
    /**
     * Set CreatedDate
     *
     * @param string $createdDate
     * @return Webkul\MarketplaceLearningManagementSystem\Api\Data\QARecordInterface
     */
    public function setCreatedDate($createdDate);
    /**
     * Get CreatedDate
     *
     * @return string
     */
    public function getCreatedDate();
    /**
     * Set Status
     *
     * @param string $status
     * @return Webkul\MarketplaceLearningManagementSystem\Api\Data\QARecordInterface
     */
    public function setStatus($status);
    /**
     * Get Status
     *
     * @return string
     */
    public function getStatus();
    /**
     * Set CourseId
     *
     * @param int $courseId
     * @return Webkul\MarketplaceLearningManagementSystem\Api\Data\QARecordInterface
     */
    public function setCourseId($courseId);
    /**
     * Get CourseId
     *
     * @return int
     */
    public function getCourseId();
}
