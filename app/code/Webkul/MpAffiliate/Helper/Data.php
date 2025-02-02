<?php
/**
 * Webkul_MpAffiliate data helper
 * @category  Webkul
 * @package   Webkul_MpAffiliate
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\MpAffiliate\Helper;

use Webkul\MpAffiliate\Model\UserFactory;
use Magento\Store\Model\StoreManagerInterface;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var Webkul\MpAffiliate\Model\UserFactory
     */
    private $userFactory;

    /**
     * @var Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @param \Magento\Framework\App\Helper\Context  $context
     * @param UserFactory $userFactory
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        StoreManagerInterface $storeManager,
        UserFactory $userFactory,
        \Magento\Customer\Model\CustomerFactory $customerFactory
    ) {
        $this->userFactory = $userFactory;
        $this->storeManager  = $storeManager;
        $this->customerFactory  = $customerFactory;
        parent::__construct($context);
    }

    /**
     * Get Configuration Detail of Affiliate
     * @return array of Affiliate Configuration Detail
     */
    public function getAffiliateConfig()
    {
        $affiliateConfig = [
            'enable' => $this->scopeConfig->getValue('Marketplaceaffiliate/general/enable'),
            'referUrlCompare' => $this->scopeConfig->getValue('Marketplaceaffiliate/general/referUrlCompare'),
            'registration' => $this->scopeConfig->getValue('Marketplaceaffiliate/general/registration'),
            'auto_approve' => $this->scopeConfig->getValue('Marketplaceaffiliate/general/auto_approve'),
            'min_pay_bal' => $this->scopeConfig->getValue('Marketplaceaffiliate/general/min_pay_bal'),
            'pay_date' => $this->scopeConfig->getValue('Marketplaceaffiliate/general/pay_date'),
            'blog_url_hint' => $this->scopeConfig->getValue('Marketplaceaffiliate/general/blog_url_hint'),
            'blog_url_active' => $this->scopeConfig->getValue('Marketplaceaffiliate/general/blog'),
            'aff_email_campaign' => $this->scopeConfig
                                        ->getValue('Marketplaceaffiliate/general/email_campaign_template'),
            'manager_email' => $this->scopeConfig->getValue('Marketplaceaffiliate/general/manager_email'),
            'manager_email_template' => $this->scopeConfig
                                            ->getValue('Marketplaceaffiliate/general/manager_email_template'),
            'payment_credit_template' => $this->scopeConfig
                                ->getValue('Marketplaceaffiliate/general/payment_credit_notify_email_template'),
            'user_notify_by_seller' => $this->scopeConfig
                                ->getValue('Marketplaceaffiliate/general/aff_user_notify_by_seller_email_template'),
            'manager_first_name' => $this->scopeConfig->getValue('Marketplaceaffiliate/payment/manager_first_name'),
            'manager_last_name' => $this->scopeConfig->getValue('Marketplaceaffiliate/payment/manager_last_name'),
            'sandbox' => $this->scopeConfig->getValue('Marketplaceaffiliate/payment/sandbox'),
            'payment_methods' => $this->scopeConfig->getValue('Marketplaceaffiliate/payment/payment_methods'),
            'per_click' => $this->scopeConfig->getValue('Marketplaceaffiliate/commission/per_click'),
            'unique_click' => $this->scopeConfig->getValue('Marketplaceaffiliate/commission/unique_click'),
            'type_on_sale' => $this->scopeConfig->getValue('Marketplaceaffiliate/commission/type_on_sale'),
            'rate' => $this->scopeConfig->getValue('Marketplaceaffiliate/commission/rate'),
            'editor_textarea' => $this->scopeConfig->getValue('Marketplaceaffiliate/terms/editor_textarea')
        ];
        return $affiliateConfig;
    }
    
    /**
     * isAffiliateUser check user is affiliate or not
     * @param int $affiliateUserId
     * @return false|array $response
     */
    public function isAffiliateUser($affiliateUserId)
    {
        $affiliateColl = $this->userFactory->create()->getCollection()
                                ->addFieldToFilter('customer_id', $affiliateUserId);
        $response = false;
        if ($affiliateColl->getSize()) {
            foreach ($affiliateColl as $affiliateUser) {
                $response = [
                    'user' => true,
                    'status' => $affiliateUser->getStatus(),
                    'data' => $affiliateUser->getData()
                ];
            }
        }
        return($response);
    }

    /**
     * getCurrentCurrencyCode
     * @return string current currency code
     */
    public function getCurrentCurrencyCode()
    {
        return $this->storeManager->getStore()->getCurrentCurrencyCode();
    }

    //get Customer Data by customer id
    public function getCustomerData($customerId)
    {
        return $this->customerFactory->create()->load($customerId);
    }
}
