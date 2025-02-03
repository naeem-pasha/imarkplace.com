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

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Webkul\MarketplaceLearningManagementSystem\Model\CourseContentFactory;
use Webkul\MarketplaceLearningManagementSystem\Helper\Data;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;
use Webkul\MarketplaceLearningManagementSystem\Model\CourseStatusFactory;
use Webkul\MarketplaceLearningManagementSystem\Model\Config\Source\CourseSection;
use Webkul\MarketplaceLearningManagementSystem\Model\QARecordFactory;
use Webkul\MarketplaceLearningManagementSystem\Model\QAReplyFactory;

/**
 * Course info data provider
 */
class CourseDataProvider implements ArgumentInterface
{
    /**
     * CourseContentFactory
     */
    protected $courseContentFactory;

    /**
     * CourseSection
     */
    protected $courseSection;

    /**
     * OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * QARecordFactory
     */
    protected $qaRecordFactory;

    /**
     * QAReplyFactory
     */
    protected $qaReplyFactory;

    /**
     * CollectionFactory
     */
    protected $orderCollectionFactory;

    /**
     * @param Data $helper
     * @param CourseSection $courseSection
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
     * @param CourseStatusFactory $courseStatusFactory
     * @param CollectionFactory $orderCollectionFactory
     * @param QARecordFactory $qaRecordFactory
     * @param QAReplyFactory $qaReplyFactory
     * @param CourseContentFactory $courseContentFactory
     */
    public function __construct(
        Data $helper,
        CourseSection $courseSection,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        CourseStatusFactory $courseStatusFactory,
        CollectionFactory $orderCollectionFactory,
        QARecordFactory $qaRecordFactory,
        QAReplyFactory $qaReplyFactory,
        CourseContentFactory $courseContentFactory
    ) {
        $this->helper = $helper;
        $this->orderRepository = $orderRepository;
        $this->courseSection = $courseSection;
        $this->courseStatusFactory = $courseStatusFactory;
        $this->qaRecordFactory = $qaRecordFactory;
        $this->qaReplyFactory = $qaReplyFactory;
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->courseContentFactory = $courseContentFactory;
    }

    /**
     * Get Total Duration Of course
     *
     * @return int
     */
    public function getTotalDurationOfCourse($courseId)
    {
        $collection = $this->courseContentFactory->create()
                            ->getCollection()
                            ->addFieldToFilter('course_id', $courseId);
        $duration = 0;
        foreach ($collection as $data) {
            if (!empty($data->getDuration())) {
                $duration += $data->getDuration();
            }
        }
            
        return $this->helper->convertDuration($duration);
    }

    /**
     * Get QA Reply
     *
     * @param int $qaId
     * @return array
     */
    public function getQaReply($qaId)
    {
        $modalCollection = $this->qaReplyFactory->create()
                            ->getCollection()
                            ->addFieldToFilter('qa_id', $qaId)
                            ->setOrder('created_at', 'desc');
                            
        return $modalCollection->getData();
    }

    /**
     * Get Total Chapter of course
     *
     * @return int
     */
    public function getTotalChapter($courseId)
    {
        $collection = $this->courseContentFactory->create()
                            ->getCollection()
                            ->addFieldToFilter('course_id', $courseId);
        return $collection->getSize();
    }

    /**
     * Get QA Record
     *
     * @param int $productId
     * @return array
     */
    public function getQaRecord($productId)
    {
        $modalCollection = $this->qaRecordFactory->create()
                            ->getCollection()
                            ->addFieldToFilter('course_id', $productId)
                            ->setOrder('created_date', 'desc');
                            
        return $modalCollection->getData();
    }

    /**
     * Get Course Content Data
     *
     * @return int
     */
    public function getCourseContent($courseId)
    {
        $collection = $this->courseContentFactory->create()
                            ->getCollection()
                            ->addFieldToFilter('course_id', $courseId);
        return $collection;
    }

    /**
     * Get Course content data
     *
     * @return array
     */
    public function courseContentData($courseId, $contentId)
    {
        $customerCourseIds = $this->getCurrentCustomerCourseIds();
        $collection = $this->courseContentFactory->create()
                            ->getCollection();
        $contentCollection = $collection->addFieldToFilter('course_id', $courseId)
                            ->addFieldToSelect('section')
                            ->distinct(true);
        if (empty($contentCollection->getSize())) {
            return;
        }

        $isPreview = (in_array($courseId, $customerCourseIds) && !empty($contentId))? true : false;
        
        $sectionIds = [];
        $content = [];
        foreach ($contentCollection as $contentData) {
            array_push($sectionIds, $contentData->getSection());
        }
        foreach ($sectionIds as $sectionId) {
            $sectionDuration = 0;
            $contentData = [];

            $sectionData = $this->courseContentFactory->create()
            ->getCollection()->addFieldToFilter('section', $sectionId);
            
            foreach ($sectionData as $data) {
                if (!empty($data->getDuration())) {
                    $sectionDuration += $data->getDuration();
                }
                
                array_push($contentData, [
                    'content_id' => $data->getEntityId(),
                    'content-title' => $data->getTitle(),
                    'preview' => ($isPreview)? true : $data->getPreview(),
                    'duration' => $this->helper->convertDuration($data->getDuration()),
                    'description' => $data->getDescription(),
                    'assignment' => ($data->getType() == 2) ? true : false,
                    'file' => $data->getFilePath()
                ]);
            }
            array_push($content, [
                'title' => $this->getSectionTitle($sectionId),
                'content' => $contentData,
                'duration' => $this->helper->convertDuration($sectionDuration)
            ]);
        }
        return $content;
    }

    /**
     * Get Percentage Of Course
     *
     * @param int $courseId
     * @return int
     */
    public function getPercentageOfCourse($courseId)
    {
        $customerId = $this->helper->getCurrentCustomerId();
        $courseStatusData = $this->courseStatusFactory->create()
                            ->getCollection()
                            ->addFieldToFilter('course_id', $courseId)
                            ->addFieldToFilter('customer_id', $customerId)
                            ->getData();
        $contentIds = [];
        foreach ($courseStatusData as $data) {
            array_push($contentIds, $data['content_id']);
        }

        $totalCourseDuration = 0;
        $completeedDuration = 0;

        $contentCollection = $this->courseContentFactory->create()
                            ->getCollection()
                            ->addFieldToFilter('course_id', $courseId);
        foreach ($contentCollection->getData() as $data) {
            $totalCourseDuration += (int)$data['duration'];
            if (in_array($data['entity_id'], $contentIds)) {
                $completeedDuration += $data['duration'];
            }
        }
        
        if ($completeedDuration && $totalCourseDuration) {
            $percentage =  round(($completeedDuration * 100)/ $totalCourseDuration);
            if ($percentage > 100) {
                return 100;
            } else {
                return $percentage;
            }
        }
        
        return 0;
    }

    /**
     * Get Section Title
     *
     * @param int $sectionId
     * @return string
     */
    public function getSectionTitle($sectionId)
    {
        return $this->courseSection->getOptionText($sectionId);
    }

    /**
     * Get Current Customer Course Ids
     *
     * @return array
     */
    public function getCurrentCustomerCourseIds()
    {
        $customerId = $this->helper->getCurrentCustomerId();
        $orders = $this->getCustomerOrderCollection($customerId);

        $myCourses = [];
        foreach ($orders->getData() as $order) {
            if ($order['status'] == 'complete') {
                $data = $this->orderRepository->get($order['entity_id']);
                foreach ($data->getAllItems() as $item) {
                    if ($item->getProductType() == 'course'
                        && !in_array($item->getProductId(), $myCourses)
                    ) {
                        array_push($myCourses, $item->getProductId());
                    }
                }
            }
        }

        return $myCourses;
    }

    /**
     * Get Current Customer Order Collection
     *
     * @param int $customerId
     * @return object
     */
    public function getCustomerOrderCollection($customerId)
    {
        return $this->orderCollectionFactory->create()->addFieldToFilter('customer_id', $customerId);
    }
}
