<?php
/**
 * Webkul MpAffiliate Configuration controller.
 * @category  Webkul
 * @package   Webkul_MpAffiliate
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\MpAffiliate\Controller\Marketplace;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Webkul\MpAffiliate\Model\UserFactory;
use Webkul\MpAffiliate\Model\UserBalanceFactory;
use Webkul\MpAffiliate\Helper\Data as AffDataHelper;

class Affiliateconfiguration extends \Magento\Customer\Controller\AbstractAccount
{
    /**
     * @var PageFactory
     */
    private $resultPageFactory;

    /**
     * @var Session
     */
    private $customerSession;

    /**
     * @var Webkul\Affiliate\Helper\Data
     */
    private $affDataHelper;

    /**
     * @var UserFactory
     */
    private $userFactory;

    /**
     * @var UserBalanceFactory
     */
    private $userBalance;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        Session $customerSession,
        AffDataHelper $affDataHelper,
        UserFactory $userFactory,
        UserBalanceFactory $userBalance,
        \Webkul\Marketplace\Helper\Data $marketplaceHelper
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->customerSession = $customerSession;
        $this->affDataHelper = $affDataHelper;
        $this->userFactory = $userFactory;
        $this->userBalance = $userBalance;
        $this->marketplaceHelper = $marketplaceHelper;
        parent::__construct($context);
    }

    /**
     * Manage Affiliate Configuration page
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $configData = $this->affDataHelper->getAffiliateConfig();
        if (!$configData['enable']) {
            $resultRedirect = $this->resultRedirectFactory->create();
            $defaultUrl = $this->_url->getUrl('*/*/', ['_secure' => true]);
            $resultRedirect->setUrl($defaultUrl);
            return $resultRedirect;
        }
        if ($this->marketplaceHelper->isSeller()) {
            $resultPage = $this->resultPageFactory->create();
            if ($this->marketplaceHelper->getIsSeparatePanel()) {
                $resultPage->addHandle('mpaffiliate_marketplace_affiliateconfiguration_layout2');
            }
            $resultPage->getConfig()->getTitle()->set(__('Affiliate Configuration'));
            return $resultPage;
        } else {
            return $this->resultRedirectFactory->create()->setPath(
                'marketplace/account/becomeseller',
                ['_secure' => $this->getRequest()->isSecure()]
            );
        }
    }
}
