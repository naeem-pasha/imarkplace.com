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
 * Course Content Repository
 */
interface ContentRepositoryInterface
{
    /**
     * Retrieve content data by Id.
     *
     * @api
     * @param string $id
     * @return \Webkul\MarketplaceLearningManagementSystem\Api\Data\ContentInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($id);

    /**
     * Retrieve all content
     *
     * @api
     * @return \Webkul\MarketplaceLearningManagementSystem\Api\Data\ContentInterface
     */
    public function getList();

    /**
     * Delete Content
     * @param \Webkul\MarketplaceLearningManagementSystem\Api\Data\ContentInterface $content
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Webkul\MarketplaceLearningManagementSystem\Api\Data\ContentInterface $content
    );

    /**
     * Delete Content by ID
     * @param string $id
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($id);
}
