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
namespace Webkul\MarketplaceLearningManagementSystem\Block\Product;

use Webkul\MarketplaceLearningManagementSystem\Model\Config\Source\CourseLevelOptions;

/**
 * Manage Product Options
 */
class ManageConfig extends \Magento\Framework\View\Element\Template
{
    /**
     * @var Webkul\MarketplaceLearningManagementSystem\Helper\Data
     */
    protected $mpLmsHelper;

    /**
     * @var Magento\Framework\Locale\ListsInterface
     */
    protected $listsInterface;

    /**
     * @var CourseLevelOptions
     */
    protected $courseLevelOptions;

    /**
     * @var Webkul\Marketplace\Helper\Data
     */
    protected $mpHelper;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Webkul\Marketplace\Helper\Data $mpHelper
     * @param \Webkul\MarketplaceLearningManagementSystem\Helper\Data $mpLmsHelper
     * @param CourseLevelOptions $courseLevelOptions
     * @param \Magento\Framework\Locale\ListsInterface $listsInterface
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Webkul\Marketplace\Helper\Data $mpHelper,
        \Webkul\MarketplaceLearningManagementSystem\Helper\Data $mpLmsHelper,
        CourseLevelOptions $courseLevelOptions,
        \Magento\Framework\Locale\ListsInterface $listsInterface,
        array $data = []
    ) {
        $this->mpHelper = $mpHelper;
        $this->courseLevelOptions = $courseLevelOptions;
        $this->listsInterface = $listsInterface;
        $this->mpLmsHelper = $mpLmsHelper;
        parent::__construct($context, $data);
    }

    /**
     * Get MpLms Helper
     *
     * @return object
     */
    public function getMpLmsHelper()
    {
        return $this->mpLmsHelper;
    }

    /**
     * Get Mp Helper
     *
     * @return object
     */
    public function getMpHelper()
    {
        return $this->mpHelper;
    }

    /**
     * Get Course Level Options
     *
     * @return array
     */
    public function getCourseLevelOptions()
    {
        return $this->courseLevelOptions->getAllOptions();
    }
    
    public function getLanguageOptions()
    {
        return $this->listsInterface->getOptionLocales();
    }
}
