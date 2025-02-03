<?php
/**
 * Webkul MpAffiliate Campaign controller.
 * @category  Webkul
 * @package   Webkul_MpAffiliate
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\MpAffiliate\Controller\User;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Customer\Model\Session;
use Webkul\MpAffiliate\Model\UserFactory;
use Webkul\MpAffiliate\Helper\Email as HelperEmail;

class Campaign extends \Webkul\MpAffiliate\Controller\User\AbstractUser
{
    /**
     * @var Webkul\MpAffiliate\Helper\Email
     */
    private $helperEmail;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param Session $customerSession
     * @param UserFactory $userFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        Session $customerSession,
        UserFactory $userFactory,
        HelperEmail $helperEmail,
        \Webkul\MpAffiliate\Helper\Data $mpaffHelper,
        \Webkul\MpAffiliate\Logger\Logger $logger
    ) {
        $this->logger = $logger;
        $this->helperEmail = $helperEmail;
        $this->mpaffHelper = $mpaffHelper;
        parent::__construct($context, $resultPageFactory, $customerSession, $userFactory, $mpaffHelper, $logger);
    }

    /**
     * Affiliate Email Campaign
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        if ($this->getRequest()->isPost()) {
            try {
                  $data = $this->getRequest()->getParams();
                  $emailList = explode(',', $data['email']);
                  $affUserId = $this->getCustomerSession()->getCustomerId();
                  $mailCount = 0;
                foreach ($emailList as $email) {
                    if ($email!='') {
                        /** send account approve mail notification to Affiliate User*/
                        $this->helperEmail->emailCampaignMail(
                            $affUserId,
                            $email,
                            $data['subject'],
                            nl2br($data['message'])
                        );
                        $mailCount++;
                    }
                }
                  $this->messageManager->addSuccess(
                      __('Total of %1 e-mail(s) have been sent successfully.', $mailCount)
                  );
                  $resultRedirect = $this->resultRedirectFactory->create();
                   return $resultRedirect->setUrl($this->_url->getUrl('mpaffiliate/user/campaign'));
            } catch (\Exception $e) {
                $this->logger->info($e->getMessage());
                $this->messageManager->addError(__($e->getMessage()));
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setUrl($this->_url->getUrl('mpaffiliate/user/campaign'));
            }
        } else {
            /** @var \Magento\Framework\View\Result\Page $resultPage */
            $resultPage = $this->getResultPageFactory()->create();
            $resultPage->getConfig()->getTitle()->set(__('E-mail Campaign'));
            return $resultPage;
        }
    }
}
