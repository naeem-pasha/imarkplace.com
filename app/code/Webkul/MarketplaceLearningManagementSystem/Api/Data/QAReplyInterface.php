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
 * QA Reply
 */
interface QAReplyInterface
{

    const ENTITY_ID = 'entity_id';

    const QA_ID = 'qa_id';

    const REPLIED_BY = 'replied_by';

    const QNA_THREAD = 'qna_thread';

    const CREATED_AT = 'created_at';

    /**
     * Set EntityId
     *
     * @param int $entityId
     * @return Webkul\MarketplaceLearningManagementSystem\Api\Data\QAReplyInterface
     */
    public function setEntityId($entityId);
    /**
     * Get EntityId
     *
     * @return int
     */
    public function getEntityId();
    /**
     * Set QaId
     *
     * @param int $qaId
     * @return Webkul\MarketplaceLearningManagementSystem\Api\Data\QAReplyInterface
     */
    public function setQaId($qaId);
    /**
     * Get QaId
     *
     * @return int
     */
    public function getQaId();
    /**
     * Set RepliedBy
     *
     * @param string $repliedBy
     * @return Webkul\MarketplaceLearningManagementSystem\Api\Data\QAReplyInterface
     */
    public function setRepliedBy($repliedBy);
    /**
     * Get RepliedBy
     *
     * @return string
     */
    public function getRepliedBy();
    /**
     * Set QnaThread
     *
     * @param string $qnaThread
     * @return Webkul\MarketplaceLearningManagementSystem\Api\Data\QAReplyInterface
     */
    public function setQnaThread($qnaThread);
    /**
     * Get QnaThread
     *
     * @return string
     */
    public function getQnaThread();
    /**
     * Set CreatedAt
     *
     * @param string $createdAt
     * @return Webkul\MarketplaceLearningManagementSystem\Api\Data\QAReplyInterface
     */
    public function setCreatedAt($createdAt);
    /**
     * Get CreatedAt
     *
     * @return string
     */
    public function getCreatedAt();
}
