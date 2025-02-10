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

use Webkul\MarketplaceLearningManagementSystem\Api\ContentRepositoryInterface;
use Webkul\MarketplaceLearningManagementSystem\Model\ResourceModel\CourseContent\CollectionFactory;
use Webkul\MarketplaceLearningManagementSystem\Model\ResourceModel\CourseContent;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotDeleteException;

/**
 * Course Content
 */
class CourseContentRepository implements ContentRepositoryInterface
{
    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var CourseContentFactory
     */
    protected $courseContentFactory;

    /**
     * @var CourseContent
     */
    protected $courseContent;

    /**
     * @var Contents[]
     */
    protected $instancesById = [];

    /**
     * @param CourseContent $courseContent
     * @param CourseContentFactory $courseContentFactory
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        CourseContent $courseContent,
        CourseContentFactory $courseContentFactory,
        CollectionFactory $collectionFactory
    ) {
        $this->courseContent = $courseContent;
        $this->courseContentFactory = $courseContentFactory;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getById($id)
    {
        $contentData = $this->courseContentFactory->create();
        $contentData->load($id);
        if (!$contentData->getId()) {
            $this->instancesById[$id] = $contentData;
        }
        $this->instancesById[$id] = $contentData;

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
        \Webkul\MarketplaceLearningManagementSystem\Api\Data\ContentInterface $content
    ) {
        try {
            $this->courseContent->delete($content);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the content: %1',
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
