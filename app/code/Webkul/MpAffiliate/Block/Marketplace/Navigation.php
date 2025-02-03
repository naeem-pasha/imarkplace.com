<?php
/**
 * Webkul MpAffiliate Account Navigation.
 *
 * @category  Webkul
 * @package   Webkul_MpAffiliate
 * @author    Webkul
 * @copyright Copyright (c)  Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\MpAffiliate\Block\Marketplace;

/**
 * Affiliate Navigation link
 */
class Navigation extends \Magento\Framework\View\Element\Template
{

    /**
     * getNavSelectClass for select navgation link if current page url match
     * @param string $navUrl
     * @return string
     */
    public function getNavSelectClass($navUrl)
    {
        return strpos($this->_urlBuilder->getCurrentUrl(), $navUrl) ? 'current' : '';
    }

    /**
     * getNavUrl for get url for navigation links
     * @param string $navUrl
     * @return string
     */
    public function getNavUrl($navUrl)
    {
        return $this->getUrl($navUrl, ['_secure' => $this->getRequest()->isSecure()]);
    }
}
