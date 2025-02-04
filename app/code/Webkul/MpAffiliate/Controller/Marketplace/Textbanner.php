<?php
/**
 * Webkul MpAffiliate Text Banner controller.
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

class Textbanner extends \Magento\Customer\Controller\AbstractAccount
{
    /**
     * @var PageFactory
     */
    private $resultPageFactory;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        \Webkul\Marketplace\Helper\Data $marketplaceHelper,
        \Webkul\MpAffiliate\Helper\Data $helper
    ) {
        $this->helper = $helper;
        $this->resultPageFactory = $resultPageFactory;
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
        if ($this->marketplaceHelper->isSeller()) {
            $resultPage = $this->resultPageFactory->create();
            if ($this->marketplaceHelper->getIsSeparatePanel()) {
                $resultPage->addHandle('mpaffiliate_marketplace_textbanner_layout2');
            }
            $resultPage->getConfig()->getTitle()->set(__('Create Text Banner'));
            return $resultPage;
        } else {
            return $this->resultRedirectFactory->create()->setPath(
                'marketplace/account/becomeseller',
                ['_secure' => $this->getRequest()->isSecure()]
            );
        }
    }
}
