<?php
/**
 * Webkul MpAffiliate Change Order Status Observer.
 * @category  Webkul
 * @package   Webkul_MpAffiliate
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
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
use Webkul\MpAffiliate\Model\UserBalanceFactory;

/**
 * Webkul MpAffiliate CheckoutSubmitAllAfter Observer Model.
 */
class ChangeOrderStatus implements ObserverInterface
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
        UserBalanceFactory $userBalance,
        Logger $logger
    ) {
        $this->customerSession = $customerSession;
        $this->userFactory = $userFactory;
        $this->saleFactory = $saleFactory;
        $this->helperEmail = $helperEmail;
        $this->requesttosellerFactory = $requesttosellerFactory;
        $this->logger = $logger;
        $this->configrationForAffiliateFactory = $configrationForAffiliateFactory;
        $this->userBalance = $userBalance;
        $this->affDataHelper = $affDataHelper;
    }

    /**
     * Sales Order Place After event handler.
     *
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        try {
            $order = $observer->getOrder();
            $collection = $this->saleFactory->create()
                                            ->getCollection()
                                            ->addFieldToFilter("order_increment_id", $order->getIncrementId());
            foreach ($collection as $model) {
                if ($model->getId()) {
                    $model->setOrderStatus($order->getState());
                    if ($order->getState() == "complete") {
                        $this->helperEmail->sendMailToAffiliateUser(
                            $model->getAffCustomerId(),
                            $model->getSellerId(),
                            $model->getOrderIncrementId()
                        );
                        $this->helperEmail->sendMailToSeller(
                            $model->getSellerId(),
                            $model->getOrderIncrementId()
                        );
                        $userBalance = $this->userBalance->create()->getCollection()
                                                ->addFieldToFilter('aff_customer_id', $model->getAffCustomerId())
                                                ->addFieldToFilter('seller_id', ['eq' => $model->getSellerId()])
                                                ->getFirstItem();
                        $userBalance->setBalanceAmount($userBalance->getBalanceAmount() + $model->getCommission());
                        $this->_save($userBalance);
                    }
                    $this->_save($model);
                }
            }
        } catch (\Exception $e) {
            $this->logger->info('affiliate order status change : '.$e->getMessage());
        }
    }
    
    private function _save($obj)
    {
        $obj->save();
    }
}
