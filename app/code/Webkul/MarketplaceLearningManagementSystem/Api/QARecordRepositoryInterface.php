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
 * QA Record Repository
 * @api
 */
interface QARecordRepositoryInterface
{
    /**
     * Retrieve QA Record by Id.
     *
     * @api
     * @param string $id
     * @return \Webkul\MarketplaceLearningManagementSystem\Api\Data\QARecordInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($id);

    /**
     * Retrieve all QA Record
     *
     * @api
     * @return \Webkul\MarketplaceLearningManagementSystem\Api\Data\QARecordInterface
     */
    public function getList();

    /**
     * Delete QA Record
     * @param \Webkul\MarketplaceLearningManagementSystem\Api\Data\QARecordInterface $qaRecord
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Webkul\MarketplaceLearningManagementSystem\Api\Data\QARecordInterface $qaRecord
    );

    /**
     * Delete QA Record by ID
     * @param string $id
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($id);
}
