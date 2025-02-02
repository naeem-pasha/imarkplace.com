<?php
/**
 * Webkul MpAffiliate User Create.
 *
 * @category  Webkul
 * @package   Webkul_MpAffiliate
 * @author    Webkul
 * @copyright Copyright (c)  Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\MpAffiliate\Block\User;

use Magento\Framework\View\Element\Template\Context;
use Magento\Customer\Model\Session;
use Webkul\MpAffiliate\Helper\Data as AffDataHelper;

class Register extends \Magento\Framework\View\Element\Template
{
    /**
     * isAffilateRegistration
     * @return bool
     */
    private $affDataHelper;

    private $customerSession;

    public function __construct(
        Context $context,
        Session $customerSession,
        AffDataHelper $affDataHelper,
        \Magento\Cms\Model\Template\FilterProvider $filterProvider,
        array $data = []
    ) {
        $this->filterProvider = $filterProvider;
        $this->customerSession = $customerSession;
        $this->affDataHelper = $affDataHelper;
        parent::__construct($context, $data);
    }

    //Check Request for affiliate registration
    public function isAffilateRegistration()
    {
        $affiliateConfigData = $this->affDataHelper->getAffiliateConfig();
        $affValue = $this->getRequest()->getParam('aff');
        return ($affValue == 1 && $affiliateConfigData['registration']) ? true : false;
    }

    public function _prepareLayout()
    {
        //set page title
        $configDataArr = $this->affDataHelper->getAffiliateConfig();
        if ($this->getRequest()->getParam('aff') && $configDataArr['enable'] && $configDataArr['registration']) {
            $this->pageConfig->getTitle()->set(__('Create Affiliate Customer Account'));
        }
        return parent::_prepareLayout();
    }

    /**
     * isAffilateRegistration
     * @return bool
     */
    public function isAffilateRegistrationTerms()
    {
        $affiliateConfigData = $this->affDataHelper->getAffiliateConfig();
        return $this->filterProvider->getPageFilter()->filter($affiliateConfigData['editor_textarea']);
    }

    public function getErrorUrl()
    {
        return $this->getUrl('*/*/*', ['_current' => true, '_use_rewrite' => true]);
    }

    public function getConfigBlogLinkValue()
    {
        $affiliateConfigData = $this->affDataHelper->getAffiliateConfig();
        return $affiliateConfigData['blog_url_active'];
    }

    public function getBlogUrlHintonregistration()
    {
        $affiliateConfigData = $this->affDataHelper->getAffiliateConfig();
        return $affiliateConfigData['blog_url_hint'];
    }
}
