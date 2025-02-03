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
 * Course Content
 */
interface CourseContentInterface
{

    const ENTITY_ID = 'entity_id';

    const COURSE_ID = 'course_id';

    const TITLE = 'title';

    const DESCRIPTION = 'description';

    const SECTION = 'section';

    const TYPE = 'type';

    const PREVIEW = 'preview';

    const FILE_NAME = 'file_name';

    const FILE_PATH = 'file_path';

    const DURATION = 'duration';

    /**
     * Set EntityId
     *
     * @param int $entityId
     * @return
     * Webkul\MarketplaceLearningManagementSystem\Api\Data\CourseContentInterface
     */
    public function setEntityId($entityId);
    /**
     * Get EntityId
     *
     * @return int
     */
    public function getEntityId();
    /**
     * Set CourseId
     *
     * @param int $courseId
     * @return
     * Webkul\MarketplaceLearningManagementSystem\Api\Data\CourseContentInterface
     */
    public function setCourseId($courseId);
    /**
     * Get CourseId
     *
     * @return int
     */
    public function getCourseId();
    /**
     * Set Title
     *
     * @param string $title
     * @return
     * Webkul\MarketplaceLearningManagementSystem\Api\Data\CourseContentInterface
     */
    public function setTitle($title);
    /**
     * Get Title
     *
     * @return string
     */
    public function getTitle();
    /**
     * Set Description
     *
     * @param string $description
     * @return
     * Webkul\MarketplaceLearningManagementSystem\Api\Data\CourseContentInterface
     */
    public function setDescription($description);
    /**
     * Get Description
     *
     * @return string
     */
    public function getDescription();
    /**
     * Set Section
     *
     * @param string $section
     * @return
     * Webkul\MarketplaceLearningManagementSystem\Api\Data\CourseContentInterface
     */
    public function setSection($section);
    /**
     * Get Section
     *
     * @return string
     */
    public function getSection();
    /**
     * Set Type
     *
     * @param string $type
     * @return
     * Webkul\MarketplaceLearningManagementSystem\Api\Data\CourseContentInterface
     */
    public function setType($type);
    /**
     * Get Type
     *
     * @return string
     */
    public function getType();
    /**
     * Set Preview
     *
     * @param int $preview
     * @return
     * Webkul\MarketplaceLearningManagementSystem\Api\Data\CourseContentInterface
     */
    public function setPreview($preview);
    /**
     * Get Preview
     *
     * @return int
     */
    public function getPreview();
    /**
     * Set FileName
     *
     * @param string $fileName
     * @return
     * Webkul\MarketplaceLearningManagementSystem\Api\Data\CourseContentInterface
     */
    public function setFileName($fileName);
    /**
     * Get FileName
     *
     * @return string
     */
    public function getFileName();
    /**
     * Set FilePath
     *
     * @param string $filePath
     * @return
     * Webkul\MarketplaceLearningManagementSystem\Api\Data\CourseContentInterface
     */
    public function setFilePath($filePath);
    /**
     * Get FilePath
     *
     * @return string
     */
    public function getFilePath();
    /**
     * Set Duration
     *
     * @param string $duration
     * @return
     * Webkul\MarketplaceLearningManagementSystem\Api\Data\CourseContentInterface
     */
    public function setDuration($duration);
    /**
     * Get Duration
     *
     * @return string
     */
    public function getDuration();
}
