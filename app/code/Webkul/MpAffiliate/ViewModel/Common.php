<?php
/**
 * Webkul_MpAffiliate
 * @category  Webkul
 * @package   Webkul_MpAffiliate
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\MpAffiliate\ViewModel;

class Common implements \Magento\Framework\View\Element\Block\ArgumentInterface
{
    public $helper;
    public $mpHelper;
    public $jsonHelper;

    public function __construct(
        \Webkul\Marketplace\Helper\Data $mpHelper,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magento\Framework\Pricing\Helper\Data $pricingHelper,
        \Webkul\MpAffiliate\Helper\Data $helper
    ) {
        $this->mpHelper = $mpHelper;
        $this->jsonHelper = $jsonHelper;
        $this->pricingHelper = $pricingHelper;
        $this->helper = $helper;
    }

    public function getPricingHelper()
    {
        return $this->pricingHelper;
    }

    public function getMpHelper()
    {
        return $this->mpHelper;
    }

    public function getHelper()
    {
        return $this->helper;
    }
}
