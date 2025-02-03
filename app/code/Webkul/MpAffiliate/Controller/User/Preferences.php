<?php
/**
 * Webkul MpAffiliate Preferences controller.
 * @category  Webkul
 * @package   Webkul_MpAffiliate
 * @author    Webkul
 * @copyright Copyright (c)  Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\MpAffiliate\Controller\User;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Customer\Model\Session;
use Webkul\MpAffiliate\Model\UserFactory;
use Webkul\MpAffiliate\Helper\Email as HelperEmail;

class Preferences extends \Webkul\MpAffiliate\Controller\User\AbstractUser
{
    /**
     * MpAffiliate Preferences
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        if ($this->getRequest()->isPost()) {
            try {
                $customerId = $this->customerSession->getCustomerId();
                $data = $this->getRequest()->getPostValue();
                $userColl = $this->userFactory->create()->load($customerId, 'customer_id');
                unset($data['form_key']);
                $userColl->setPaymentMethod(json_encode($data));
                $this->saveObject($userColl);
            } catch (\Exception $e) {
                $this->logger->info($e->getMessage());
            }
            $this->messageManager->addSuccess(__('Payment method saved successfully.'));
            $resultRedirect = $this->resultRedirectFactory->create();
            return $resultRedirect->setUrl($this->_url->getUrl('mpaffiliate/user/preferences'));
        } else {
            /** @var \Magento\Framework\View\Result\Page $resultPage */
            $resultPage = $this->getResultPageFactory()->create();
            $resultPage->getConfig()->getTitle()->set(__('Payment Preference'));
            return $resultPage;
        }
    }
}
