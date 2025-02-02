<?php
/**
 * Webkul MpAffiliate Commission controller.
 * @category  Webkul
 * @package   Webkul_MpAffiliate
 * @author    Webkul
 * @copyright Copyright (c)  Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\MpAffiliate\Controller\User;

class Commission extends \Webkul\MpAffiliate\Controller\User\AbstractUser
{
    /**
     * Affiliate Commission
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $resultPage = $this->getResultPageFactory()->create();
        $resultPage->getConfig()->getTitle()->set(__('Commission'));
        return $resultPage;
    }
}
