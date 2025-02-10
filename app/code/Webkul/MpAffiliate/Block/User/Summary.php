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

namespace Webkul\MpAffiliate\Block\User;

use Magento\Framework\View\Element\Template\Context;
use Magento\Customer\Model\Session;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Webkul\MpAffiliate\Helper\Data as AffDataHelper;
use Webkul\MpAffiliate\Model\SaleFactory;
use Webkul\MpAffiliate\Model\UserBalanceFactory;
use Webkul\MpAffiliate\Model\UserFactory;
use Webkul\MpAffiliate\Model\ClicksFactory;

class Summary extends \Webkul\MpAffiliate\Block\User\UserAbstract
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
        PriceCurrencyInterface $priceCurrency,
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
        $this->priceCurrency = $priceCurrency;
        $this->affDataHelper = $affDataHelper;
        $this->salesFactory = $salesFactory;
        $this->userBalance = $userBalance;
        $this->userFactory = $userFactory;
        $this->customerFactory = $customerFactory;
        $this->mpSellerModel = $mpSellerModel;
        $this->reqToSellerModel = $reqToSellerModel;
        $this->paymentFactory = $paymentFactory;
        $this->clicksFactory = $clicksFactory;
        parent::__construct($context, $customerSession, $affDataHelper, $customerFactory, $data);
    }

    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if ($this->getSummary()) {
            $pager = $this->getLayout()->createBlock(
                \Magento\Theme\Block\Html\Pager::class,
                'mpaffiliate.user.summary.pager'
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
        $sellerInfo = $this->mpSellerModel->getCollection();
        /*Filter By Email*/
        $filterEmail =  $this->getRequest()->getParam('seller_email')!=""?
                        $this->getRequest()->getParam('seller_email'):"";
        if ($filterEmail != '') {
            $ids = [];
            $customerDetails = $this->customerFactory->create()->getCollection()
                                                ->addFieldToFilter('email', ['like'=>'%'.$filterEmail.'%']);
            foreach ($customerDetails as $value) {
                $ids[] = $value->getEntityId();
            }
            $sellerInfo->addFieldToFilter('seller_id', ['in'=>$ids]);
        }
        $sellerIds = [];
        foreach ($sellerInfo as $value) {
            array_push($sellerIds, $value->getSellerId());
        }
        $summary = [];
        $collection = $this->reqToSellerModel->getCollection()
                        ->addFieldToFilter('seller_id', ['in'=>$sellerIds])
                        ->addFieldToFilter('affiliate_id', ['eq'=>$customerid])
                        ->addFieldToFilter('status', ['neq'=>'0']);
        $id = [];
        foreach ($collection as $value) {
            $id[] = $value->getSellerId();
        }

        $currentSeller = $this->userBalance->create()->getCollection()
                              ->addFieldToFilter('aff_customer_id', ['eq'=>$customerid])
                              ->addFieldToFilter('seller_id', ['in'=>$id])
                              ->setOrder('entity_id', 'desc');
        return $this->setCollection($currentSeller);
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
                        ->addFieldToFilter('seller_id', ['in'=>$sellerId])
                        ->addFieldToFilter('order_status', 'complete')
                        ->getSize();
        return $totalSales;
    }

    //get Affiliate Total Commission
    public function getTotalCommission($affId, $sellerId)
    {
        $totalCommission = $this->salesFactory->create()->getCollection()
                            ->addFieldToFilter('aff_customer_id', ['eq'=>$affId])
                            ->addFieldToFilter('seller_id', ['in'=>$sellerId])
                            ->addFieldToFilter('order_status', 'complete')
                            ->getColumnValues('commission');
        return array_sum($totalCommission);
    }

    //get Seller Data
    public function getSellerData($sellerId)
    {
        return $this->affDataHelper->getCustomerData($sellerId);
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
}
