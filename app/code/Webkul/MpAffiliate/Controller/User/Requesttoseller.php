<?php
/**
 * Webkul MpAffiliate User Status controller.
 * @category  Webkul
 * @package   Webkul_MpAffiliate
 * @author    Webkul
 * @copyright Copyright (c)  Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\MpAffiliate\Controller\User;

class Requesttoseller extends \Webkul\MpAffiliate\Controller\User\AbstractUser
{

    /**
     * Add Auction on product page
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->set(__('Request sent to seller'));
        return $resultPage;
    }
}
