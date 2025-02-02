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

class CourseInfo extends \Magento\Framework\View\Element\Template
{
    /**
     * @var Data
     */
    protected $helper;

    /**
     * @param Context $context
     * @param Data $helper
     * @param array $data
     */
    public function __construct(
        Context $context,
        Data $helper,
        array $data = []
    ) {
        $this->helper = $helper;
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
     * Get Video Icon Url
     *
     * @return string
     */
    public function getVideoIconUrl()
    {
        return $this->getViewFileUrl('Webkul_MarketplaceLearningManagementSystem::images/video_icon.png');
    }

    /**
     * Get Chapter Icon Url
     *
     * @return string
     */
    public function getChapterIconUrl()
    {
        return $this->getViewFileUrl('Webkul_MarketplaceLearningManagementSystem::images/chapter.png');
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
     * Get Publish Course Date Icon Url
     *
     * @return string
     */
    public function getPublishCourseDateIconUrl()
    {
        return $this->getViewFileUrl('Webkul_MarketplaceLearningManagementSystem::images/calendar_1.png');
    }

    /**
     * Get Update Course Date Icon Url
     *
     * @return string
     */
    public function getUpdateCourseDateIconUrl()
    {
        return $this->getViewFileUrl('Webkul_MarketplaceLearningManagementSystem::images/calendar_2.png');
    }

    /**
     * Get Language Icon Url
     *
     * @return string
     */
    public function getLanguageIconUrl()
    {
        return $this->getViewFileUrl('Webkul_MarketplaceLearningManagementSystem::images/msg.png');
    }
}
