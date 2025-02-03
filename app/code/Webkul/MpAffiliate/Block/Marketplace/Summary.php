<?php
/**
 * Webkul MpAffiliate Summary.
 *
 * @category  Webkul
 * @package   Webkul_MpAffiliate
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\MpAffiliate\Block\Marketplace;

use Magento\Framework\View\Element\Template\Context;
use Magento\Customer\Model\Session;
use Webkul\MpAffiliate\Helper\Data as AffDataHelper;
use Webkul\MpAffiliate\Model\SaleFactory;
use Webkul\MpAffiliate\Model\UserBalanceFactory;
use Webkul\MpAffiliate\Model\UserFactory;
use Webkul\MpAffiliate\Model\ClicksFactory;

class Summary extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Webkul\MpAffiliate\Model\SaleFactory
     */
    private $salesFactory;

    /**
     * @var \Webkul\MpAffiliate\Model\UserBalanceFactory
     */
    private $userBalance;

    /**
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface
     */
    private $priceCurrency;

    /**
     * @var \Webkul\MpAffiliate\Model\UserFactory
     */
    private $userFactory;

    /**
     * @param Context                $context
     * @param Session                $customerSession,
     * @param PriceCurrencyInterface $priceCurrency
     * @param AffDataHelper          $affDataHelper,
     * @param SaleFactory            $salesFactory
     * @param UserBalanceFactory     $userBalance,
     * @param array                  $data
     */
    public function __construct(
        Context $context,
        Session $customerSession,
        AffDataHelper $affDataHelper,
        SaleFactory $salesFactory,
        UserBalanceFactory $userBalance,
        UserFactory $userFactory,
        ClicksFactory $clicksFactory,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Webkul\Marketplace\Model\Seller $mpSellerModel,
        \Webkul\MpAffiliate\Model\Requesttoseller $reqToSellerModel,
        \Webkul\MpAffiliate\Model\PaymentFactory $paymentFactory,
        array $data = []
    ) {
        $this->customerSession = $customerSession;
        $this->affDataHelper = $affDataHelper;
        $this->salesFactory = $salesFactory;
        $this->userBalance = $userBalance;
        $this->userFactory = $userFactory;
        $this->customerFactory = $customerFactory;
        $this->mpSellerModel = $mpSellerModel;
        $this->reqToSellerModel = $reqToSellerModel;
        $this->paymentFactory = $paymentFactory;
        $this->clicksFactory = $clicksFactory;
        $this->_storeManager = $context->getStoreManager();
        parent::__construct($context, $data);
    }

    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if ($this->getSummary()) {
            $pager = $this->getLayout()->createBlock(
                \Magento\Theme\Block\Html\Pager::class,
                'mpaffiliate.marketplace.summary.pager'
            )->setCollection(
                $this->getSummary()
            );
            $this->setChild('pager', $pager);
            $this->getSummary();
        }
        return $this;
    }
    public function getSummary()
    {
        $customerid = $this->customerSession->getCustomer()->getId();

        /*Filter By Name and Email*/
        $filterEmail =  $this->getRequest()->getParam('aff_email')!=""?$this->getRequest()->getParam('aff_email'):"";
        $filterName =   $this->getRequest()->getParam('aff_name')!=""?$this->getRequest()->getParam('aff_name'):"";
        $ids = $this->customerFactory->create()->getCollection()
                            ->addExpressionAttributeToSelect('name', 'LOWER(CONCAT({{firstname}}," ",
                            {{lastname}}))', ['firstname','lastname'])
                            ->addFieldToFilter('email', ['like'=>'%'.$filterEmail.'%'])
                            ->addFieldToFilter('name', ['like'=>'%'.$filterName.'%'])
                            ->getColumnValues('entity_id');

        $affIds = $this->reqToSellerModel->getCollection()
                        ->addFieldToFilter('affiliate_id', ['in'=>$ids])
                        ->addFieldToFilter('seller_id', ['eq'=>$customerid])
                        ->addFieldToFilter('status', ['neq'=>'0'])
                        ->getColumnValues('affiliate_id');

        $currentSeller = $this->userBalance->create()->getCollection()
                              ->addFieldToFilter('seller_id', ['eq'=>$customerid])
                              ->addFieldToFilter('aff_customer_id', ['in'=>$affIds])
                              ->setOrder('entity_id', 'desc');
                              
        return $this->setCollection($currentSeller);
    }

    public function getPaypalInfo()
    {
        return json_encode((object) [
                "urlFix" => ($this->affDataHelper->getAffiliateConfig()['sandbox']==0 ? "":"sandbox."),
                "currentCurrencyCode" => $this->_storeManager->getStore()->getCurrentCurrency()->getCode(),
                "returnurl" => $this->getUrl(
                    'mpaffiliate/marketplace/paypalsuccess',
                    ['_secure' => $this->getRequest()->isSecure()]
                ),
                "cancelReturn" => $this->getUrl(
                    'mpaffiliate/marketplace/paypalfailed',
                    ['_secure' => $this->getRequest()->isSecure()]
                ),
                "ipnnotify" => $this->getUrl(
                    'mpaffiliate/marketplace/paypalipnnotify',
                    ['_secure' => $this->getRequest()->isSecure()]
                ),
            ]);
    }

    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    //get Affiliate Total Sale
    public function getTotalSale($affId, $sellerId)
    {
        $totalSales = $this->salesFactory->create()->getCollection()
                        ->addFieldToFilter('aff_customer_id', ['eq'=>$affId])
                        ->addFieldToFilter('seller_id', ['eq'=>$sellerId])
                        ->addFieldToFilter('order_status', 'complete')
                        ->getSize();
        return $totalSales;
    }

    //get Affiliate Total Commission
    public function getTotalCommission($affId, $sellerId)
    {
        $totalCommission = $this->salesFactory->create()->getCollection()
                            ->addFieldToFilter('aff_customer_id', ['eq'=>$affId])
                            ->addFieldToFilter('seller_id', ['eq'=>$sellerId])
                            ->addFieldToFilter('order_status', 'complete')
                            ->getColumnValues('commission');
        return array_sum($totalCommission);
    }

    public function getCustomerData($customerId)
    {
        return $this->affDataHelper->getCustomerData($customerId);
    }

    //get Total paid
    public function getTotalPaid($affId, $sellerId)
    {
        $totalPaid = $this->paymentFactory->create()->getCollection()
                            ->addFieldToFilter('aff_customer_id', ['eq'=>$affId])
                            ->addFieldToFilter('seller_id', ['eq'=>$sellerId])
                            ->getColumnValues('transaction_amount');
        return array_sum($totalPaid);
    }

    public function getPaymentMethodData($affId)
    {
        $paymtData = $this->userFactory->create()->load($affId, 'customer_id')->getPaymentMethod();
        if ($paymtData == '') {
            $paymtData = '{"payment_method":"","account_data":{}}';
        }
        return $paymtData;
    }
}
