<?php
/**
 * Webkul MpAffiliate User Status controller.
 * @category  Webkul
 * @package   Webkul_MpAffiliate
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\MpAffiliate\Controller\Marketplace;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Webkul\MpAffiliate\Helper\Email as HelperEmail;
use Magento\Customer\Model\Session;
use Webkul\MpAffiliate\Model\RequesttosellerFactory as RequesttosellerFactory;

class Mailtoaffiliate extends \Magento\Customer\Controller\AbstractAccount
{
    /**
     * @var PageFactory
     */
    public $resultPageFactory;

    public $helperEmail;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        Session $customerSession,
        HelperEmail $helperEmail,
        PageFactory $resultPageFactory,
        RequesttosellerFactory $requesttosellerFactory,
        \Webkul\MpAffiliate\Logger\Logger $logger,
        \Webkul\Marketplace\Helper\Data $marketplaceHelper,
        \Webkul\MpAffiliate\Helper\Data $helper,
        array $data = []
    ) {
        $this->helper = $helper;
        $this->customerSession = $customerSession;
        $this->resultPageFactory = $resultPageFactory;
        $this->helperEmail = $helperEmail;
        $this->requesttosellerFactory = $requesttosellerFactory;
        $this->logger = $logger;
        $this->marketplaceHelper = $marketplaceHelper;
        parent::__construct($context);
    }

    /**
     * Add Auction on product page
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $configData = $this->helper->getAffiliateConfig();
        if (!$configData['enable']) {
            $resultRedirect = $this->resultRedirectFactory->create();
            $defaultUrl = $this->_url->getUrl('*/*/', ['_secure' => true]);
            $resultRedirect->setUrl($defaultUrl);
            return $resultRedirect;
        }
        if ($this->getRequest()->isPost()) {
            try {
                $data = $this->getRequest()->getParams();
                $mailCount = 0;
                $sellerId = $this->customerSession->getCustomer()->getId();
                if ($data['aff_user']=="all") {
                    $collection = $this->requesttosellerFactory->create()->getCollection()
                                    ->addFieldToFilter("seller_id", ["eq"=>$sellerId])
                                    ->addFieldToFilter("status", ["eq"=>1]);
                    foreach ($collection as $affUser) {
                        $this->helperEmail->sendMailToAffUserForNotify($affUser->getAffiliateId(), $sellerId, $data);
                        $mailCount++;
                    }
                } else {
                    $this->helperEmail->sendMailToAffUserForNotify($data['aff_user'], $sellerId, $data);
                    $mailCount++;
                }
                $this->messageManager->addSuccess(
                    __('Total of %1 e-mail(s) have been sent successfully.', $mailCount)
                );
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setUrl($this->_url->getUrl('mpaffiliate/marketplace/mailtoaffiliate/'));
            } catch (\Exception $e) {
                $this->logger->info($e->getMessage());
                $this->messageManager->addError(__($e->getMessage()));
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setUrl($this->_url->getUrl('mpaffiliate/marketplace/mailtoaffiliate/'));
            }
        } else {
            if ($this->marketplaceHelper->isSeller()) {
                $resultPage = $this->resultPageFactory->create();
                if ($this->marketplaceHelper->getIsSeparatePanel()) {
                    $resultPage->addHandle('mpaffiliate_marketplace_mailtoaffiliate_layout2');
                }
                $resultPage->getConfig()->getTitle()->set(__('Send mail to affiliate'));
                return $resultPage;
            } else {
                return $this->resultRedirectFactory->create()->setPath(
                    'marketplace/account/becomeseller',
                    ['_secure' => $this->getRequest()->isSecure()]
                );
            }
        }
    }
}
