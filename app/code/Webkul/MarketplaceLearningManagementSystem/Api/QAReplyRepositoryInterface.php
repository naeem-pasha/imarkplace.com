<?php
/**
 * Webkul Software.
 *
 * @category Webkul
 * @package Webkul_MarketplaceLearningManagementSystem
 * @author Webkul
 * @copyright Copyright (c) WebkulSoftware Private Limited (https://webkul.com)
 * @license https://store.webkul.com/license.html
 *
 */
namespace Webkul\MarketplaceLearningManagementSystem\Api;

/**
 * QA Reply Repository
 */
interface QAReplyRepositoryInterface
{
    /**
     * Retrieve QA Reply data by Id.
     *
     * @api
     * @param string $id
     * @return \Webkul\MarketplaceLearningManagementSystem\Api\Data\QAReplyInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($id);

    /**
     * Retrieve all QA Reply
     *
     * @api
     * @return \Webkul\MarketplaceLearningManagementSystem\Api\Data\QAReplyInterface
     */
    public function getList();

    /**
     * Delete QA Reply
     * @param \Webkul\MarketplaceLearningManagementSystem\Api\Data\QAReplyInterface $qaReply
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Webkul\MarketplaceLearningManagementSystem\Api\Data\QAReplyInterface $qaReply
    );

    /**
     * Delete QA Reply by ID
     * @param string $id
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($id);
}
