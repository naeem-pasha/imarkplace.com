<?php
/**
 * Webkul MpAffiliate Commission Block.
 *
 * @category  Webkul
 * @package   Webkul_MpAffiliate
 * @author    Webkul
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
use Webkul\MpAffiliate\Model\ConfigrationForAffiliateFactory;

class Commission extends \Webkul\MpAffiliate\Block\User\UserAbstract
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
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Webkul\Marketplace\Model\Seller $mpSellerModel,
        \Webkul\MpAffiliate\Model\Requesttoseller $reqToSellerModel,
        \Webkul\MpAffiliate\Model\PaymentFactory $paymentFactory,
        ConfigrationForAffiliateFactory $configrationForAffiliateFactory,
        \Magento\Framework\App\ResourceConnection $resource,
        array $data = []
    ) {
        $this->customerSession = $customerSession;
        $this->resource = $resource;
        $this->priceCurrency = $priceCurrency;
        $this->affDataHelper = $affDataHelper;
        $this->salesFactory = $salesFactory;
        $this->userBalance = $userBalance;
        $this->userFactory = $userFactory;
        $this->customerFactory = $customerFactory;
        $this->mpSellerModel = $mpSellerModel;
        $this->reqToSellerModel = $reqToSellerModel;
        $this->paymentFactory = $paymentFactory;
        $this->configrationForAffiliateFactory = $configrationForAffiliateFactory;
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
                        ->addFieldToFilter('main_table.seller_id', ['in'=>$sellerIds])
                        ->addFieldToFilter('main_table.affiliate_id', ['eq'=>$customerid])
                        ->addFieldToFilter('main_table.status', ['eq'=>'1']);

        $connection  = $this->resource->getConnection();
        $tableName   = $this->resource->getTableName('mp_affiliate_setting');
      
        $collection->getSelect()->joinLeft(
            ['sconf'=>$tableName],
            "main_table.seller_id = sconf.seller_id",
            ['per_click','unique_click','commission_type','commission_rate']
        );
        $collection->setOrder('main_table.affiliate_request_id', 'desc');
        $this->setCollection($collection);
        //for non config users
        return $collection;
    }

    public function getAdminConfig()
    {
        return $this->affDataHelper->getAffiliateConfig();
    }

    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    //get Affiliate Total Sale
    public function getTotalSale($affId, $sellerId)
    {
        return $this->salesFactory->create()->getTotalSales($affId, $sellerId);
    }

    //get Affiliate Total Commission
    public function getTotalCommission($affId, $sellerId)
    {
        return $this->salesFactory->create()->getTotalCommission($affId, $sellerId);
    }

    //get Seller Data
    public function getSellerData($sellerId)
    {
        return $this->affDataHelper->getCustomerData($sellerId);
    }

    //get Total paid
    public function getTotalPaid($affId, $sellerId)
    {
        return $this->paymentFactory->create()->getPaidAmount($affId, $sellerId);
    }
}
