<?php
/**
 * Webkul MpAffiliate Checkout Submit All After Observer.
 * @category  Webkul
 * @package   Webkul_MpAffiliate
 * @author    Webkul
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\MpAffiliate\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Customer\Model\Session;
use Webkul\MpAffiliate\Model\UserFactory;
use Webkul\MpAffiliate\Model\SaleFactory;
use Webkul\MpAffiliate\Helper\Email as HelperEmail;
use Webkul\MpAffiliate\Logger\Logger;
use Webkul\MpAffiliate\Model\RequesttosellerFactory as RequesttosellerFactory;
use Webkul\MpAffiliate\Model\ConfigrationForAffiliateFactory;
use Webkul\MpAffiliate\Helper\Data as AffDataHelper;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\Session\SessionManagerInterface as CoreSession;

/**
 * Webkul MpAffiliate CheckoutSubmitAllAfter Observer Model.
 */
class CheckoutSubmitAllAfter implements ObserverInterface
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    private $customerSession;

    /**
     * @var \Webkul\Affiliate\Model\SaleFactory
     */
    private $saleFactory;

    /**
     * @var \Webkul\Affiliate\Model\UserFactory
     */
    private $userFactory;

    /**
     * @var \Webkul\Affiliate\Helper\Email
     */
    private $helperEmail;

    /**
     * @var \Webkul\Affiliate\Logger\Logger
     */
    private $logger;

    /**
     * @param \Magento\Framework\Event\Manager            $eventManager
     * @param \Magento\Framework\ObjectManagerInterface   $objectManager
     * @param \Magento\Customer\Model\Session             $customerSession
     * @param SessionManager                              $coreSession
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     */
    public function __construct(
        Session $customerSession,
        UserFactory $userFactory,
        SaleFactory $saleFactory,
        HelperEmail $helperEmail,
        RequesttosellerFactory $requesttosellerFactory,
        ConfigrationForAffiliateFactory $configrationForAffiliateFactory,
        AffDataHelper $affDataHelper,
        \Magento\Catalog\Model\ProductRepository $productRepo,
        \Webkul\MpAffiliate\Model\CatcommissionFactory $catcommissionFactory,
        Logger $logger,
        CheckoutSession $checkoutSession,
        CoreSession $coreSession
    ) {
        $this->customerSession = $customerSession;
        $this->userFactory = $userFactory;
        $this->saleFactory = $saleFactory;
        $this->helperEmail = $helperEmail;
        $this->requesttosellerFactory = $requesttosellerFactory;
        $this->logger = $logger;
        $this->configrationForAffiliateFactory = $configrationForAffiliateFactory;
        $this->affDataHelper = $affDataHelper;
        $this->productRepo = $productRepo;
        $this->catcommissionFactory = $catcommissionFactory;
        $this->checkoutSession = $checkoutSession;
        $this->coreSession = $coreSession;
    }

    /**
     * Sales Order Place After event handler.
     *
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        try {
            /** @var $orderInstance Order */
            $order = $observer->getOrder();
            
            $lastOrderId = $observer->getOrder()->getId();
            $totalAffIds = $this->customerSession->getAffIds();
            if (!$totalAffIds || empty($totalAffIds) || $totalAffIds=="" || $totalAffIds==null) {
                $totalAffIds = $this->checkoutSession->getAffIds();
            }
            if (!$totalAffIds || empty($totalAffIds) || $totalAffIds=="" || $totalAffIds==null) {
                $totalAffIds = $this->coreSession->getAffIds();
            }

            $orderProducts = [];
            foreach ($order->getAllVisibleItems() as $item) {
                $orderProducts[$item->getProductId()][] = $item;
            }

            $totalAffCount = count($totalAffIds);
            if ($totalAffCount) {
                foreach ($totalAffIds as $affDetail) {
                    if ($affDetail["hit_type"]=="product" && isset($orderProducts[$affDetail["hit_id"]])) {

                        $productItems = $orderProducts[$affDetail["hit_id"]];
                        foreach ($productItems as $productItem) {
                            $this->getOrderedProduct($productItem, $order, $affDetail);
                        }
                    }
                }

                $this->customerSession->unsAffIds();
                $this->checkoutSession->unsAffIds();
                $this->coreSession->unsAffIds();
            }
        } catch (\Exception $e) {
            $this->logger->info('affiliate order place : '.$e->getMessage());
        }
    }

    /**
     * save sales detail and notify seller via mail
     */
    private function saveDetail($order, $affDetail, $totalPrice, $orderedQty, $affCommission = null)
    {
        $activeAffUser = $this->requesttosellerFactory->create()
                                        ->getCollection()
                                        ->addFieldToFilter('affiliate_id', $affDetail['aff_customer_id'])
                                        ->addFieldToFilter('seller_id', $affDetail['seller_id'])
                                        ->addFieldToFilter('status', 1);
        if ($activeAffUser->getSize()) {
            foreach ($activeAffUser as $affUser) {
                if ($affCommission==null) {
                    $affCommission = $this->getCommissionRate($affDetail['seller_id']);
                    if ($this->getCommissionType($affDetail['seller_id']) == 'percentage') {
                        $affCommission=($totalPrice * $affCommission)/100;
                    } else {
                        $affCommission = $affCommission * $orderedQty;
                    }
                }
                $data = [
                    'order_id'           =>  $order->getId(),
                    'order_increment_id' =>  $order->getIncrementId(),
                    'aff_customer_id'    =>  $affDetail['aff_customer_id'],
                    'order_status'       =>  $order->getStatus(),
                    'seller_id'          =>  $affDetail['seller_id'],
                    'affilate_status'    =>  0,
                    'price'              =>  $totalPrice,
                    'commission'         =>  $affCommission,
                    'come_from'          =>  $affDetail['come_from'],
                    'created_at'         =>  $order->getCreatedAt()
                ];

                $this->saveSale($data);
                /*send placed order mail notification to seller*/
                $this->helperEmail->sendMailToAffiliateAdmin($data['aff_customer_id'], $order->getIncrementId());
            }
        }
    }

    // get detail of products in order
    private function getOrderedProduct($productItem, $order, $affDetail)
    {
        $totalPrice = $productItem->getBaseRowTotal();
        $orderedQty = $productItem->getQtyOrdered();

        $catIds = $this->productRepo->getById($productItem->getProductId())->getCategoryIds();
        $catcommCollection = $this->catcommissionFactory->create()->getCollection()
                                        ->addFieldToFilter("category_id", ["in"=>$catIds])
                                        ->addFieldToFilter("seller_id", $affDetail['seller_id']);
        $commissionArr = [];
        foreach ($catcommCollection as $catcomm) {
            $catAffCommission = $catcomm->getCommission();
            if ($catAffCommission != "") {
                $catCommission = $catAffCommission;
                if ($catcomm->getCommissionType()=="percent") {
                    $catCommission = $catCommission * $totalPrice / 100;
                } else {
                    $catCommission = $catCommission * $orderedQty;
                }
                $commissionArr[] = $catCommission;
            }
        }
        if (!empty($commissionArr)) {
            $this->saveDetail($order, $affDetail, $totalPrice, $orderedQty, max($commissionArr));
        } else {
            $this->saveDetail($order, $affDetail, $totalPrice, $orderedQty);
        }
    }

    //get commission type for the seller
    private function getCommissionType($sellerId)
    {
        $commissionType = $this->configrationForAffiliateFactory->create()
                                    ->load($sellerId, "seller_id")->getCommissionType();
        if ($commissionType) {
            return $commissionType;
        } else {
            $affData = $this->affDataHelper->getAffiliateConfig();
            return $affData['type_on_sale'];
        }
    }

    private function getCommissionRate($sellerId)
    {
        $commissionRate = $this->configrationForAffiliateFactory->create()
                                    ->load($sellerId, "seller_id")->getCommissionRate();
        if ($commissionRate) {
            return $commissionRate;
        } else {
            $affData = $this->affDataHelper->getAffiliateConfig();
            return $affData['rate'];
        }
    }
    private function saveSale($data)
    {
        $affTmpSale = $this->saleFactory->create();
        $affTmpSale->setData($data);
        $affTmpSale->save();
    }
}
