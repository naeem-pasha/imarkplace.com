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
 * Course Section Repository
 * @api
 */
interface CourseSectionRepositoryInterface
{
    /**
     * Retrieve course section data by Id.
     *
     * @api
     * @param string $id
     * @return \Webkul\MarketplaceLearningManagementSystem\Api\Data\CourseSectionInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($id);

    /**
     * Retrieve all course sections
     *
     * @api
     * @return \Webkul\MarketplaceLearningManagementSystem\Api\Data\CourseSectionInterface
     */
    public function getList();

    /**
     * Delete Course Section
     * @param \Webkul\MarketplaceLearningManagementSystem\Api\Data\CourseSectionInterface $section
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Webkul\MarketplaceLearningManagementSystem\Api\Data\CourseSectionInterface $section
    );

    /**
     * Delete Course Section by ID
     * @param string $id
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($id);
}
