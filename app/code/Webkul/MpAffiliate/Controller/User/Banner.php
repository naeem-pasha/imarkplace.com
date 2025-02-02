<?php
/**
 * Webkul MpAffiliate Banner controller.
 * @category  Webkul
 * @package   Webkul_MpAffiliate
 * @author    Webkul
 * @copyright Copyright (c)  Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\MpAffiliate\Controller\User;

class Banner extends \Webkul\MpAffiliate\Controller\User\AbstractUser
{
    /**
     * Affiliate Commission
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $resultPage = $this->getResultPageFactory()->create();
        $resultPage->getConfig()->getTitle()->set(__('Banners & Ads'));
        return $resultPage;
    }
}
