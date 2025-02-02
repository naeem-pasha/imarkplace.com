<?php
/**
 * Webkul MpAffiliate Summary controller.
 * @category  Webkul
 * @package   Webkul_MpAffiliate
 * @author    Webkul
 * @copyright Copyright (c)  Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\MpAffiliate\Controller\User;

class Summary extends \Webkul\MpAffiliate\Controller\User\AbstractUser
{
    /**
     * Affiliate Summary
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        /** @var \Magento\Framework\View\Result\Page $resultPage */
        
        $resultPage = $this->getResultPageFactory()->create();
        $resultPage->getConfig()->getTitle()->set(__('Summary'));
        return $resultPage;
    }
}
