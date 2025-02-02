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
namespace Webkul\MarketplaceLearningManagementSystem\Model;

use Webkul\MarketplaceLearningManagementSystem\Api\CourseSectionRepositoryInterface;
use Webkul\MarketplaceLearningManagementSystem\Model\ResourceModel\CourseSection\CollectionFactory;
use Webkul\MarketplaceLearningManagementSystem\Model\ResourceModel\CourseSection;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotDeleteException;

/**
 * Course Section
 */
class CourseSectionRepository implements CourseSectionRepositoryInterface
{
    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var CourseSectionFactory
     */
    protected $courseSectionFactory;

    /**
     * @var CourseSection
     */
    protected $courseSection;

    /**
     * @var Sections[]
     */
    protected $instancesById = [];

    /**
     * @param CourseSection $courseSection
     * @param CourseSectionFactory $courseSectionFactory
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        CourseSection $courseSection,
        CourseSectionFactory $courseSectionFactory,
        CollectionFactory $collectionFactory
    ) {
        $this->courseSection = $courseSection;
        $this->courseSectionFactory = $courseSectionFactory;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getById($id)
    {
        $courseSectionData = $this->courseSectionFactory->create();
        $courseSectionData->load($id);
        if (!$courseSectionData->getId()) {
            $this->instancesById[$id] = $courseSectionData;
        }
        $this->instancesById[$id] = $courseSectionData;

        return $this->instancesById[$id];
    }

    /**
     * {@inheritdoc}
     */
    public function getList()
    {
        $collection = $this->collectionFactory->create();
        $collection->load();

        return $collection;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(
        \Webkul\MarketplaceLearningManagementSystem\Api\Data\CourseSectionInterface $section
    ) {
        try {
            $this->courseSection->delete($section);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the section: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($id)
    {
        return $this->delete($this->getById($id));
    }
}
