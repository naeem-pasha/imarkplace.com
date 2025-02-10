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

use Webkul\MarketplaceLearningManagementSystem\Api\Data\QAReplyInterface;
use Magento\Framework\DataObject\IdentityInterface;

/**
 * QuestionAnswer Reply Class
 */
class QAReply extends \Magento\Framework\Model\AbstractModel implements IdentityInterface, QAReplyInterface
{

    const NOROUTE_ENTITY_ID = 'no-route';

    const CACHE_TAG = 'webkul_marketplacelearningmanagementsystem_qareply';

    protected $_cacheTag = 'webkul_marketplacelearningmanagementsystem_qareply';

    protected $_eventPrefix = 'webkul_marketplacelearningmanagementsystem_qareply';

    /**
     * set resource model
     */
    public function _construct()
    {
        $this->_init(\Webkul\MarketplaceLearningManagementSystem\Model\ResourceModel\QAReply::class);
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
     * @return Webkul\MarketplaceLearningManagementSystem\Model\QAReplyInterface
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
     * Set QaId
     *
     * @param int $qaId
     * @return Webkul\MarketplaceLearningManagementSystem\Model\QAReplyInterface
     */
    public function setQaId($qaId)
    {
        return $this->setData(self::QA_ID, $qaId);
    }

    /**
     * Get QaId
     *
     * @return int
     */
    public function getQaId()
    {
        return parent::getData(self::QA_ID);
    }

    /**
     * Set RepliedBy
     *
     * @param string $repliedBy
     * @return Webkul\MarketplaceLearningManagementSystem\Model\QAReplyInterface
     */
    public function setRepliedBy($repliedBy)
    {
        return $this->setData(self::REPLIED_BY, $repliedBy);
    }

    /**
     * Get RepliedBy
     *
     * @return string
     */
    public function getRepliedBy()
    {
        return parent::getData(self::REPLIED_BY);
    }

    /**
     * Set QnaThread
     *
     * @param string $qnaThread
     * @return Webkul\MarketplaceLearningManagementSystem\Model\QAReplyInterface
     */
    public function setQnaThread($qnaThread)
    {
        return $this->setData(self::QNA_THREAD, $qnaThread);
    }

    /**
     * Get QnaThread
     *
     * @return string
     */
    public function getQnaThread()
    {
        return parent::getData(self::QNA_THREAD);
    }

    /**
     * Set CreatedAt
     *
     * @param string $createdAt
     * @return Webkul\MarketplaceLearningManagementSystem\Model\QAReplyInterface
     */
    public function setCreatedAt($createdAt)
    {
        return $this->setData(self::CREATED_AT, $createdAt);
    }

    /**
     * Get CreatedAt
     *
     * @return string
     */
    public function getCreatedAt()
    {
        return parent::getData(self::CREATED_AT);
    }
}
