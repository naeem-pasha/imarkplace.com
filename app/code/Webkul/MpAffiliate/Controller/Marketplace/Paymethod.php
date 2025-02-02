<?php
/**
 * Webkul MpAffiliate Payment controller.
 * @category  Webkul
 * @package   Webkul_MpAffiliate
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\MpAffiliate\Controller\Marketplace;

use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Customer\Model\Session;
use Magento\Framework\Controller\Result\JsonFactory;
use Webkul\MpAffiliate\Model\UserBalanceFactory;
use Webkul\MpAffiliate\Model\PaymentFactory;
use Webkul\MpAffiliate\Model\ConfigrationForAffiliateFactory;
use Webkul\MpAffiliate\Helper\Email as HelperEmail;

class Paymethod extends \Magento\Customer\Controller\AbstractAccount
{
    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    private $resultJsonFactory;

    /**
     * @var \Webkul\MpAffiliate\Model\PaymentFactory
     */
    private $paymentFactory;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var \Webkul\MpAffiliate\Model\UserBalanceFactory
     */
    private $userBalance;

    /**
     * @var Webkul\MpAffiliate\Helper\Email
     */
    private $helperEmail;

    /**
     * @param Context              $context
     * @param JsonFactory          $resultJsonFactory
     * @param ScopeConfigInterface $scopeConfig,
     * @param PaymentFactory       $paymentFactory,
     * @param UserBalanceFactory   $userBalanceFactory,
     * @param HelperEmail          $helperEmail
     */
    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        ScopeConfigInterface $scopeConfig,
        Session $customerSession,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        PaymentFactory $paymentFactory,
        UserBalanceFactory $userBalance,
        ConfigrationForAffiliateFactory $configrationForAffiliateFactory,
        HelperEmail $helperEmail,
        \Webkul\MpAffiliate\Logger\Logger $logger,
        \Magento\Framework\Pricing\Helper\Data $priceHelper
    ) {
        parent::__construct($context);
        $this->_priceCurrency = $priceCurrency;
        $this->customerSession = $customerSession;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->paymentFactory = $paymentFactory;
        $this->scopeConfig = $scopeConfig;
        $this->userBalance = $userBalance;
        $this->helperEmail = $helperEmail;
        $this->configrationForAffiliateFactory = $configrationForAffiliateFactory;
        $this->logger = $logger;
        $this->priceHelper = $priceHelper;
    }

    /**
     * Affiliate User pay page.
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        if ($this->getRequest()->isPost()) {
            try {
                $data = $this->getRequest()->getPostValue();
                $sellerid = $this->customerSession->getCustomer()->getId();
                $data['seller_id']=$sellerid;
                $minBalModel = $this->configrationForAffiliateFactory->create()->load($sellerid, "seller_id");
                $minBal = ($minBalModel->getEntityId()!="" ?
                            $minBalModel->getPayoutBalance()
                            : (float)$this->scopeConfig->getValue('Marketplaceaffiliate/general/min_pay_bal'));
                $balanceRecord = $this->userBalance->create()->getCollection()
                                                    ->addFieldToFilter('aff_customer_id', $data['aff_customer_id'])
                                                    ->addFieldToFilter('seller_id', $sellerid)
                                                    ->getFirstItem();
                if ($balanceRecord->getEntityId()) {
                    $balanceAmount = (float)$balanceRecord->getBalanceAmount();
                    if (is_numeric($data['transaction_amount']) && ($minBal <= $balanceAmount)
                        && ($balanceAmount >= (float)$data['transaction_amount'])) {
                        $balAmt = $balanceAmount - (float)$data['transaction_amount'];
                        $balanceRecord->setBalanceAmount($balAmt);
                        $balanceRecord->setPayNotify(0);
                        $this->_saveObj($balanceRecord);
                        $tmpPayment = $this->paymentFactory->create();
                        $tmpPayment->setData($data)
                                    ->setTransactionStatus("complete")
                                    ->setTransactionCurrency($this->_priceCurrency->getCurrency()->getCurrencyCode());
                        $this->_saveObj($tmpPayment);
                        $responce = ['status' => true, 'msg' => __('Transaction detail saved successfully.')];
                        
                        /** send payment credited in bank mail notification to Affiliate User*/
                        $amountCredited = $this->priceHelper->currency($data['transaction_amount'], true, false);
                        $sellerName = $this->customerSession->getCustomer()->getName();
                        $mailData = [
                            'email_content' => __('Your affiliate account has been credited ').
                                                $amountCredited.__(' by ').$sellerName.
                                                __(' in your bank account.'),
                            'email_subject' => __('Payment credited notification')
                        ];
                        $this->helperEmail->mailToAffPaymentCreditedNotify($data['aff_customer_id'], $mailData);
                    } else {
                        $responce = [
                            'status' => false,
                            'msg' => __('Invalid amount or user balance is less than minimum payout.')
                        ];
                    }
                } else {
                    $responce = [
                        'status' => false,
                        'msg' => __('Affiliate user does not exist.')
                    ];
                }
            } catch (\Exception $e) {
                $this->logger->info($e->getMessage());
            }
        } else {
            $responce = ['status' => false, 'msg' => __('Invalid request')];
        }
        return $this->resultJsonFactory->create()->setData($responce);
    }

    /**
     * Check Affiliate Pay To User  Permission.
     *
     * @return bool
     */
    public function _isAllowed()
    {
        return $this->_authorization->isAllowed('Webkul_Affiliate::affiliate_user');
    }

    /**
     * save object.
     *
     * @return object
     */
    private function _saveObj($object)
    {
        $object->save();
    }
}
