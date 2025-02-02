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
namespace Webkul\MarketplaceLearningManagementSystem\Block\Course\MyCourse;

use Webkul\MarketplaceLearningManagementSystem\Helper\Data;
use Magento\Framework\View\Element\Template\Context;
use Magento\Catalog\Model\ProductFactory;
use Magento\Store\Model\StoreManagerInterface;
use Webkul\MarketplaceLearningManagementSystem\Model\CourseContentFactory;
use Webkul\MarketplaceLearningManagementSystem\Model\CourseDataProviderFactory;
use Magento\Framework\UrlInterface;

class CourseList extends \Magento\Framework\View\Element\Template
{
    /**
     * @var ProductFactory
     */
    protected $productFactory;

    /**
     * @var Data
     */
    protected $helper;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManagerInterface;

    /**
     * @var CourseContentFactory
     */
    protected $courseContentFactory;

    /**
     * @var CourseDataProviderFactory
     */
    protected $courseDataProviderFactory;

    /**
     * @var UrlInterface
     */
    protected $urlInterface;

    /**
     * @param Context $context
     * @param Data $helper
     * @param StoreManagerInterface $storeManagerInterface
     * @param ProductFactory $productFactory
     * @param CourseContentFactory $courseContentFactory
     * @param UrlInterface $urlInterface
     * @param CourseDataProviderFactory $courseDataProviderFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        Data $helper,
        StoreManagerInterface $storeManagerInterface,
        ProductFactory $productFactory,
        CourseContentFactory $courseContentFactory,
        UrlInterface $urlInterface,
        CourseDataProviderFactory $courseDataProviderFactory,
        array $data = []
    ) {
        $this->helper = $helper;
        $this->productFactory = $productFactory;
        $this->urlInterface = $urlInterface;
        $this->courseContentFactory = $courseContentFactory;
        $this->storeManagerInterface = $storeManagerInterface;
        $this->courseDataProviderFactory = $courseDataProviderFactory;
        parent::__construct($context, $data);
    }

    /**
     * Get Course Product Data
     *
     * @return array
     */
    public function getCourseProductData()
    {
        $myCourses = $this->courseDataProviderFactory->create()->getCurrentCustomerCourseIds();

        $coursesData = [];
        foreach ($myCourses as $courseId) {
            $details = $this->getCourseProductDetails($courseId);
            array_push($coursesData, $details);
        }
        return $coursesData;
    }

    /**
     * Get course product details
     *
     * @param int $courseId
     * @return string
     */
    public function getCourseProductDetails($courseId)
    {
        $details = [
            'id' => $courseId
        ];
        $store = $this->storeManagerInterface->getStore();
        $product = $this->productFactory->create()->load($courseId);

        $baseUrl = $store->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . 'catalog/product';
        if (empty($product->getImage())) {
            $courseImageUrl = $this->getViewFileUrl('Magento_Catalog::images/product/placeholder/small_image.jpg');
        } else {
            $courseImageUrl = $baseUrl . $product->getImage();
        }

        $conentId = $this->courseContentFactory->create()
                        ->getCollection()
                        ->addFieldToFilter('course_id', $courseId)
                        ->addFieldToFilter('type', '1')
                        ->getFirstItem()
                        ->getEntityId();

        return [
            'id' => $courseId,
            'name' => $product->getName(),
            'image' => $courseImageUrl,
            'description' => $product->getData('short_description'),
            'url' => $this->getCourseViewPageUrl()."id/".$courseId."/cid"."/".$conentId
        ];
    }

    /**
     * Get Course View Page Url
     *
     * @return string
     */
    public function getCourseViewPageUrl()
    {
        return $this->urlInterface->getUrl('mplms/course/view');
    }

    /**
     * Get Helper
     *
     * @return object
     */
    public function getHelper()
    {
        return $this->helper;
    }
}
