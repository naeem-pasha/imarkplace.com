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
namespace Webkul\MarketplaceLearningManagementSystem\Block\Course;

use Webkul\MarketplaceLearningManagementSystem\Helper\Data;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\View\Element\Template;
use Webkul\MarketplaceLearningManagementSystem\Model\CourseDataProvider;

class CourseView extends Template
{
    /**
     * @var Data
     */
    protected $helper;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManagerInterface;

    /**
     * @var CourseDataProvider
     */
    protected $courseDataProvider;

    /**
     * @param Context $context
     * @param Data $helper
     * @param RequestInterface $request
     * @param StoreManagerInterface $storeManagerInterface
     * @param CourseDataProvider $courseDataProvider
     * @param array $data
     */
    public function __construct(
        Context $context,
        Data $helper,
        RequestInterface $request,
        StoreManagerInterface $storeManagerInterface,
        CourseDataProvider $courseDataProvider,
        array $data = []
    ) {
        $this->helper = $helper;
        $this->request = $request;
        $this->courseDataProvider = $courseDataProvider;
        $this->storeManagerInterface = $storeManagerInterface;
        parent::__construct($context, $data);
    }

    /**
     * Get Helper Object
     *
     * @return object
     */
    public function getHelper()
    {
        return $this->helper;
    }

    /**
     * Get Course Data Provider
     *
     * @return void
     */
    public function getCourseDataProvider()
    {
        return $this->courseDataProvider;
    }

    /**
     * Get Video Icon Url
     *
     * @return string
     */
    public function getVideoIconUrl()
    {
        return $this->getViewFileUrl('Webkul_MarketplaceLearningManagementSystem::images/video_icon.png');
    }

    /**
     * Get Assignment Icon Url
     *
     * @return string
     */
    public function getDocumentIconUrl()
    {
        return $this->getViewFileUrl('Webkul_MarketplaceLearningManagementSystem::images/document.png');
    }

    /**
     * Get Video File Url
     *
     * @return string
     */
    public function getVideoFileUrl()
    {
        $contentData = $this->helper->courseContentData();
        $sectionId = $this->request->getParam('cid');
        foreach ($contentData as $sectionData) {
            foreach ($sectionData['content'] as $data) {
                if ($data['content_id'] == $sectionId) {
                    $file = $data['file'];
                }
            }
        }

        $fileUrl = $this->storeManagerInterface
                ->getStore()
                ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA).
                'MarketplaceLearningManagementSystem'.$file;
        return $fileUrl;
    }
}
