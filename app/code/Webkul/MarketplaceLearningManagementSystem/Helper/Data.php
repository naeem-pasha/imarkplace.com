<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_MarketplaceLearningManagementSystem
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\MarketplaceLearningManagementSystem\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Customer\Model\SessionFactory;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;
use Webkul\MarketplaceLearningManagementSystem\Model\CourseStatusFactory;
use Webkul\MarketplaceLearningManagementSystem\Model\CourseDataProviderFactory;
use Webkul\MarketplaceLearningManagementSystem\Model\Config\Source\CourseLevelOptions;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;

/**
 * Helper Data
 */
class Data extends AbstractHelper implements ArgumentInterface
{
    /**
     * RequestInterface
     */
    protected $request;

    /**
     * ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * UrlInterface
     */
    protected $urlInterface;

    /**
     * StoreManagerInterface
     */
    protected $storeManagerInterface;

    /**
     * CourseLevelOptions
     */
    protected $courseLevelOptions;

    /**
     * @var SessionFactory
     */
    protected $customerSessionFactory;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var Magento\Framework\Locale\ListsInterface
     */
    protected $language;
    
    /**
     * CollectionFactory
     */
    protected $orderCollectionFactory;

    /**
     * OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * @param Context $context
     * @param \Magento\Framework\UrlInterface $urlInterface
     * @param CollectionFactory $orderCollectionFactory
     * @param RequestInterface $request
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
     * @param CourseLevelOptions $courseLevelOptions
     * @param SessionFactory $customerSessionFactory
     * @param CourseDataProviderFactory $courseDataProvider
     * @param ProductRepositoryInterface $productRepository
     * @param CourseStatusFactory $courseStatusFactory
     * @param CustomerRepositoryInterface $customerRepositoryInterface
     * @param ScopeConfigInterface $scopeConfig
     * @param StoreManagerInterface $storeManagerInterface
     * @param \Magento\Framework\Locale\ListsInterface $language
     */
    public function __construct(
        Context $context,
        \Magento\Framework\UrlInterface $urlInterface,
        RequestInterface $request,
        CourseLevelOptions $courseLevelOptions,
        SessionFactory $customerSessionFactory,
        CourseDataProviderFactory $courseDataProvider,
        ProductRepositoryInterface $productRepository,
        CourseStatusFactory $courseStatusFactory,
        CustomerRepositoryInterface $customerRepositoryInterface,
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManagerInterface,
        \Magento\Framework\Locale\ListsInterface $language,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        CollectionFactory $orderCollectionFactory
    ) {
        $this->courseDataProvider = $courseDataProvider;
        $this->request = $request;
        $this->urlInterface = $urlInterface;
        $this->courseLevelOptions = $courseLevelOptions;
        $this->customerSessionFactory = $customerSessionFactory;
        $this->storeManagerInterface = $storeManagerInterface;
        $this->customerRepositoryInterface = $customerRepositoryInterface;
        $this->courseStatusFactory = $courseStatusFactory;
        $this->productRepository = $productRepository;
        $this->language = $language;
        $this->scopeConfig = $scopeConfig;
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->orderRepository = $orderRepository;
        parent::__construct($context);
    }

    /**
     * Get Product by Id
     *
     * @param int $productId
     * @return object
     */
    public function getProductById($productId)
    {
        return $this->productRepository->getById($productId);
    }

    /**
     * Get Product Name
     *
     * @return string
     */
    public function getProdutName()
    {
        $courseId = $this->request->getParam('id');
        $productName = $this->productRepository->getById($courseId)->getName();
        return $productName;
    }

    /**
     * Get Current User Sort Name
     *
     * @param string $name
     * @return string
     */
    public function getCurrentUserSortName()
    {
        $customerId = $this->getCurrentCustomerId();
        $customer = $this->customerRepositoryInterface->getById($customerId);
        $name = $customer->getFirstName()." ".$customer->getLastName();
        $name = explode(' ', $name);
        return strtoupper($name[0][0].$name[1][0]);
    }

    /**
     * Get User Sort Name
     *
     * @param string $name
     * @return string
     */
    public function getUserSortName($name)
    {
        $name = explode(' ', $name);
        return $name[0][0].$name[1][0];
    }

    /**
     * Get Enroll Students
     *
     * @param int $productId
     * @return int
     */
    public function getEnrollStudents()
    {
        $productId = $this->request->getParam('id');
        $collection = $this->courseStatusFactory->create()
                        ->getCollection()
                        ->addFieldToFilter('course_id', $productId); 
        return $collection->getSize();
    }

    /**
     * Get Configuration Attribute value
     *
     * @param string $groupId
     * @param string $fieldId
     * @return string
     */
    public function getFieldValue($groupId, $fieldId)
    {
        $path = 'wk_mplms/'.$groupId.'/'.$fieldId;

        return $this->scopeConfig->getValue($path);
    }

    /**
     * Count Section's Lecture
     *
     * @param array $section
     * @return int
     */
    public function countSectionLecture($section)
    {
        $count = 0;
        foreach ($section as $data) {
            if (!$data['assignment']) {
                $count++;
            }
        }
        return  $count;
    }

    /**
     * Count Section's Assignment
     *
     * @param array $section
     * @return int
     */
    public function countSectionAssignment($section)
    {
        $count = 0;
        foreach ($section as $data) {
            if ($data['assignment']) {
                $count++;
            }
        }
        return  $count;
    }

    /**
     * Get Product Type
     *
     * @return bool
     */
    public function getProductType()
    {
        $productId = $this->request->getParam('id');
        $productType = $this->productRepository->getById($productId)->getTypeId();

        return ($productType == 'course')? true : false;
    }

    /**
     * Convert Duration
     *
     * @param int $durationInSec
     * @return string
     */
    public function convertDuration($durationInSec)
    {
        if (empty($durationInSec)) {
            return ;
        }
        $sec = $durationInSec % 60;
        $hour = $durationInSec / 60;
        $min = $hour % 60;
        $hour = (int)($hour / 60);

        if ($sec <= 9) {
            $sec = "0".$sec;
        }
        if ($min <= 9) {
            $min = "0".$min;
        }
        if ($hour <= 9) {
            $hour = "0".$hour;
        }
        if ($hour == "00" && $min == "00") {
            return "00:".$sec;
        } elseif ($hour == "00" && $sec == "00") {
            return $min.":00";
        } elseif ($hour != "00" && $min == "00" && $sec == "00") {
            return $hour.":00:00";
        } elseif ($hour != "00" && $min != "00" && $sec == "00") {
            return $hour.":".$min.":00";
        } elseif ($hour == "00" && $min != "00" && $sec != "00") {
            return $min.":".$sec;
        } else {
            return $hour.":".$min.":".$sec;
        }
    }

    /**
     * Get Total Duration Of course
     *
     * @return int
     */
    public function getTotalDurationOfCourse()
    {
        $courseId = $this->request->getParam('id');
        $duration = $this->courseDataProvider->create()->getTotalDurationOfCourse($courseId);
            
        return $duration;
    }

    /**
     * Get Share Button Url
     *
     * @param str $site
     * @return string
     */
    public function getShareButtonUrl($site)
    {
        $courseId = $this->request->getParam('id');
        $productUrl = $this->productRepository->getById($courseId)->getProductUrl();
        
        $fb = 'https://www.facebook.com/sharer.php?u='.$productUrl;
        $twitter = 'https://twitter.com/share?url='.$productUrl;
        $mail = 'mailto:?subject='.
            __('I wanted you to see this site').' &amp;body= '.
            __('I saw this and thought of you!').'  '.$productUrl;

        switch ($site) {
            case 'fb':
                return $fb;
            case 'tw':
                return $twitter;
            case 'mail':
                return $mail;
            default:
                return '';
        }
    }

    /**
     * Get File Url
     *
     * @param int $contentId
     * @return string
     */
    public function getFileUrl($contentId)
    {
        $contentData = $this->courseContentData();
        foreach ($contentData as $sectionData) {
            foreach ($sectionData['content'] as $data) {
                if ($data['content_id'] == $contentId) {
                    $file = $data['file'];
                }
            }
        }

        $fileUrl = $this->storeManagerInterface
                    ->getStore()
                    ->getBaseUrl(
                        \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
                    ).'MarketplaceLearningManagementSystem'.$file;
        return $fileUrl;
    }

     /**
     * Get Base Url
     *
     * @return string
     */
    public function getBaseUrl()
    {
       
        $baseUrl = $this->storeManagerInterface
                    ->getStore()
                    ->getBaseUrl(
                        \Magento\Framework\UrlInterface::URL_TYPE_WEB
                    ); 
        return $baseUrl;
    }

    /**
     * Get Percentage Of course
     *
     * @return string
     */
    public function getPercentageOfCourse($courseId = null)
    {
        if (!$courseId) {
            $courseId = $this->request->getParam('id');
        }

        return $this->courseDataProvider->create()->getPercentageOfCourse($courseId);
    }
    
    /**
     * Get Current Customer Id
     *
     * @return int
     */
    public function getCurrentCustomerId()
    {
        return $this->customerSessionFactory->create()->getCustomerId();
    }

    /**
     * Get Total Chapter of course
     *
     * @return int
     */
    public function getTotalChapterOfCourse()
    {
        $courseId = $this->request->getParam('id');
        return $this->courseDataProvider->create()->getTotalChapter($courseId);
    }

    /**
     * Get total no of assignment of course
     *
     * @return int
     */
    public function getTotalAssignmentOfCourse()
    {
        $courseId = $this->request->getParam('id');
        $courseData = $this->courseDataProvider->create()
                        ->getCourseContent($courseId);
        $assignmentCount = 0;
        foreach ($courseData as $data) {
            if ($data->getType() == '2') {
                $assignmentCount++;
            }
        }
        return $assignmentCount;
    }

    /**
     * Get Total Hour Of course
     *
     * @return string
     */
    public function getTotalHoursOfCourse()
    {
        $time = explode(":", $this->getTotalDurationOfCourse());
        if (count($time) == 2) {
            return $time[0].__(" Minutes");
        } else {
            return $time[0].__(" Hours");
        }
    }

    /**
     * Get course content data
     *
     * @return array
     */
    public function courseContentData()
    {
        $courseId  = $this->request->getParam('id');
        $contentId = $this->request->getParam('cid');

        return  $this->courseDataProvider->create()->courseContentData($courseId, $contentId);
    }

    /**
     * Get total no of lectures of course
     *
     * @return int
     */
    public function getTotalLecturesOfCourse()
    {
        $courseId = $this->request->getParam('id');
        $courseData = $this->courseDataProvider->create()
                        ->getCourseContent($courseId);
        $lecturesCount = 0;
        foreach ($courseData as $data) {
            if ($data->getType() == '1') {
                $lecturesCount++;
            }
        }
        return $lecturesCount;
    }

    /**
     * Get Content Url
     *
     * @param int $contentId
     * @return string
     */
    public function getContentUrl($contentId)
    {
        $courseId = $this->request->getParam('id');
        return $this->urlInterface
                ->getUrl('mplms/course/view/')."id/".$courseId."/cid"."/".$contentId;
    }

    /**
     * Get Published Course Date
     *
     * @param int $productId
     * @return string
     */
    public function getPublishCourseDate()
    {
        $courseId = $this->request->getParam('id');
        return date(
            'Y-m-d',
            strtotime($this->productRepository->getById($courseId)->getCreatedAt())
        );
    }

    /**
     * Get Last Update Date
     *
     * @param int $productId
     * @return string
     */
    public function getUpdateCourseDate()
    {
        $courseId = $this->request->getParam('id');
        return date(
            'Y-m-d',
            strtotime($this->productRepository->getById($courseId)->getUpdatedAt())
        );
    }

    /**
     * Get Course Description
     *
     * @return string
     */
    public function getCourseDescription()
    {
        $courseId = $this->request->getParam('id');
        return $this->productRepository->getById($courseId)->getDescription();
    }

     /**
     * Get Course Short Description
     *
     * @return string
     */
    public function getCourseShortDescription()
    {
        $courseId = $this->request->getParam('id'); 
        return $this->productRepository->getById($courseId)->getShortDescription(); 
    }

    /**
     * Get Course Language
     *
     * @return string
     */
    public function getCourseLanguage()
    {
        $courseId = $this->request->getParam('id');
        $languageOptions = $this->language->getOptionLocales();
        $language = $this->productRepository->getById($courseId)
                ->getCustomAttribute('course_language')
                ->getValue();
        foreach ($languageOptions as $lang) {
            if ($lang['value'] == $language) {
                return [
                    'id' => $language,
                    'text' => $lang['label']
                ];
            }
        }
        return '';
    }

    /**
     * Get Course Level
     *
     * @return string
     */
    public function getCourseLevel()
    {
        $courseId = $this->request->getParam('id');
        $level = $this->productRepository->getById($courseId)
                ->getCustomAttribute('course_level')
                ->getValue();
        return ($level)? [
            'id' => $level,
            'text' => $this->courseLevelOptions->getOptionText($level)
            ] : '';
    }

    /**
     * Check valid  Course for current user
     *
     * @return bool
     */

    public function isValidCourse()
    {

        $courseId = $this->request->getParam('id');
        $contentId = $this->request->getParam('cid');

        $customerCourseIds = $this->getCurrentCustomerCourseIds();
        $isPurchased = false;
        if (in_array($courseId, $customerCourseIds)) {
            $isPurchased = true;
        }
        return $isPurchased;
    }

     /**
      * Get Current Customer Course Ids
      *
      * @return array
      */
    public function getCurrentCustomerCourseIds()
    {
        $customerId = $this->getCurrentCustomerId();
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
