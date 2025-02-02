<?php
/**
 * Webkul MpAffiliate Paypal Success controller.
 * @category  Webkul
 * @package   Webkul_MpAffiliate
 * @author    Webkul
 * @copyright Copyright (c)  Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\MpAffiliate\Controller\Marketplace;

use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\Action;
use Magento\Framework\View\Result\PageFactory;
use Webkul\MpAffiliate\Model\PaymentFactory;
use Webkul\MpAffiliate\Model\UserBalanceFactory;
use Webkul\MpAffiliate\Helper\Email as HelperEmail;
use Magento\Customer\Model\Session;

class PaypalSuccess extends \Magento\Customer\Controller\AbstractAccount
{

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var PaymentFactory
     */
    private $paymentFactory;

    /**
     * @var UserBalanceFactory
     */
    private $userBalance;

    /**
     * @var HelperEmail
     */
    private $helperEmail;

    /**
     * @param Context             $context
     * @param Logger              $logger,
     * @param PaymentFactory      $paymentFactory,
     * @param UserBalanceFactory  $userBalance,
     * @param HelperEmail         $helperEmail
     */
    public function __construct(
        Action\Context $context,
        Session $customerSession,
        PaymentFactory $paymentFactory,
        UserBalanceFactory $userBalance,
        HelperEmail $helperEmail,
        \Webkul\MpAffiliate\Logger\Logger $logger,
        \Magento\Framework\Pricing\Helper\Data $priceHelper
    ) {
        $this->customerSession = $customerSession;
        $this->paymentFactory = $paymentFactory;
        $this->userBalance = $userBalance;
        $this->helperEmail = $helperEmail;
        $this->logger = $logger;
        $this->priceHelper = $priceHelper;
        parent::__construct($context);
    }
    public function execute()
    {
        $customerId = $this->customerSession->getCustomer()->getId();
        $data = $this->getRequest()->getParams();

        try {
          //  $invoice = explode('-', $data['invoice']);
            if (!empty($data) && isset($data['st']) && !empty($data['st'])
                && strtolower($data['st']) == 'completed'
            ) {
                $affId = $data['aff_id'];
                $transdata = [
                    'seller_id' => $customerId,
                    'aff_customer_id' =>  $affId,
                    'transaction_id' => $data['tx'],
                   // 'transaction_email' => $data['payer_email'],
                    //'ipn_transaction_id' => $data['ipn_track_id'],
                  //  'transaction_date' => $data['payment_date'],
                    'transaction_status' => $data['st'],
                    'transaction_amount' => $data['amt'],
                    'transaction_currency' => $data['cc'],
                    'transaction_data' => json_encode($data),
                    'payment_method' => 'paypal_standard'
                ];
                $tranCollection = $this->paymentFactory->create()->getCollection()
                                            ->addFieldToFilter('transaction_id', ['eq' => $data['tx']])
                                            ->setPageSize(1)->setCurPage(1)->getFirstItem();
                if (!$tranCollection->getEntityId()) {
                    $paymentTmp = $this->paymentFactory->create();
                    $paymentTmp->setData($transdata);
                    $paymentTmp->save();
                    //Update balance record
                    $balanceRecord = $this->userBalance->create()->getCollection()
                                                ->addFieldToFilter('aff_customer_id', $affId)
                                                ->addFieldToFilter('seller_id', $transdata['seller_id']);
                    foreach ($balanceRecord as $balRecord) {
                        $balanceAmount = (float)$balRecord->getBalanceAmount();
                        $balAmt = $balanceAmount - (float)$data['amt'];
                        $balRecord->setBalanceAmount($balAmt);
                        $balRecord->setPayNotify(0);
                        $this->_saveObj($balRecord);
                    }
                     /** send payment credited in bank mail notification to Affiliate User*/
                    $amountCredited = $this->priceHelper->currency($transdata['transaction_amount'], true, false);
                    $sellerName = $this->customerSession->getCustomer()->getName();
                    $mailData = [
                        'email_content' => __('Your affiliate account has been credited ').$amountCredited
                                            .__(' by ').$sellerName.__(' in your paypal account'),
                        'email_subject' => __('Payment credited notification')
                    ];
                    $this->helperEmail->mailToAffPaymentCreditedNotify($transdata['aff_customer_id'], $data);
                    $this->messageManager->addSuccess(__('Your payment has been done successfully.'));
                    return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)
                    ->setPath('mpaffiliate/marketplace/summary/');
                }
            }
        } catch (\Exception $e) {
            $this->logger->info('ipnNotify : '.$e->getMessage());
        }
    }
       /**
        * save Object
        *
        * @return object
        */
    private function _saveObj($object)
    {
        $object->save();
    }
}
