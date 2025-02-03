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
 * CourseStatus
 */
interface CourseStatusInterface
{

    const ENTITY_ID = 'entity_id';

    const COURSE_ID = 'course_id';

    const CUSTOMER_ID = 'customer_id';

    const CONTENT_ID = 'content_id';

    /**
     * Set EntityId
     *
     * @param int $entityId
     * @return
     * Webkul\MarketplaceLearningManagementSystem\Api\Data\CourseStatusInterface
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
     * Webkul\MarketplaceLearningManagementSystem\Api\Data\CourseStatusInterface
     */
    public function setCourseId($courseId);
    /**
     * Get CourseId
     *
     * @return int
     */
    public function getCourseId();
    /**
     * Set CustomerId
     *
     * @param int $customerId
     * @return
     * Webkul\MarketplaceLearningManagementSystem\Api\Data\CourseStatusInterface
     */
    public function setCustomerId($customerId);
    /**
     * Get CustomerId
     *
     * @return int
     */
    public function getCustomerId();
    /**
     * Set ContentId
     *
     * @param int $contentId
     * @return
     * Webkul\MarketplaceLearningManagementSystem\Api\Data\CourseStatusInterface
     */
    public function setContentId($contentId);
    /**
     * Get ContentId
     *
     * @return int
     */
    public function getContentId();
}
