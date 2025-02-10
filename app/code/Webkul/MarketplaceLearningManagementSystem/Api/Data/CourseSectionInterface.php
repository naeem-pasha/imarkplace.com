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
 * CourseSection
 */
interface CourseSectionInterface
{

    const ENTITY_ID = 'entity_id';

    const COURSE_ID = 'course_id';

    const TITLE = 'title';

    const SORT_ORDER = 'sort_order';

    /**
     * Set EntityId
     *
     * @param int $entityId
     * @return
     * Webkul\MarketplaceLearningManagementSystem\Api\Data\CourseSectionInterface
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
     * Webkul\MarketplaceLearningManagementSystem\Api\Data\CourseSectionInterface
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
     * Webkul\MarketplaceLearningManagementSystem\Api\Data\CourseSectionInterface
     */
    public function setTitle($title);
    /**
     * Get Title
     *
     * @return string
     */
    public function getTitle();
    /**
     * Set SortOrder
     *
     * @param int $sortOrder
     * @return
     * Webkul\MarketplaceLearningManagementSystem\Api\Data\CourseSectionInterface
     */
    public function setSortOrder($sortOrder);
    /**
     * Get SortOrder
     *
     * @return int
     */
    public function getSortOrder();
}
