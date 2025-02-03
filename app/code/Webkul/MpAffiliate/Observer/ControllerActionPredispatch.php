<?php
/**
 * Webkul MpAffiliate Controller Action Predispatch Observer.
 * @category  Webkul
 * @package   Webkul_MpAffiliate
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\MpAffiliate\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Customer\Model\Session;
use Webkul\MpAffiliate\Model\ClicksFactory;
use Webkul\MpAffiliate\Model\UserBalanceFactory;
use Webkul\MpAffiliate\Model\UserFactory;
use Webkul\MpAffiliate\Model\ConfigrationForAffiliateFactory;
use Webkul\MpAffiliate\Helper\Data as AffDataHelper;
use Webkul\MpAffiliate\Model\RequesttosellerFactory as RequesttosellerFactory;

/**
 * Webkul Affiliate ControllerActionPredispatch Observer.
 */
class ControllerActionPredispatch implements ObserverInterface
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    private $timezone;

    /**
     * @var \Magento\Customer\Model\Session
     */
    private $customerSession;

    /**
     * @var \Webkul\Affiliate\Model\ClicksFactory
     */
    private $clicksFactory;

    /**
     * @var \Webkul\Affiliate\Model\UserBalanceFactory
     */
    private $userBalance;

    /**
     * @var \Webkul\Affiliate\Model\UserFactory
     */
    private $userFactory;

    /**
     * @param StoreManagerInterface $storeManagerInterface,
     * @param TimezoneInterface $timezone,
     * @param Session $customerSession,
     * @param ClicksFactory $clicksFactory,
     * @param UserBalanceFactory $userBalance,
     * @param UserFactory $userFactory
     */

    public function __construct(
        \Magento\Framework\Registry $registry,
        StoreManagerInterface $storeManagerInterface,
        TimezoneInterface $timezone,
        Session $customerSession,
        \Webkul\Marketplace\Model\Product $mpProductModel,
        ClicksFactory $clicksFactory,
        UserBalanceFactory $userBalance,
        UserFactory $userFactory,
        AffDataHelper $affDataHelper,
        RequesttosellerFactory $requesttosellerFactory,
        ConfigrationForAffiliateFactory $configrationForAffiliateFactory,
        \Webkul\MpAffiliate\Logger\Logger $logger
    ) {
        $this->registry = $registry;
        $this->storeManager = $storeManagerInterface;
        $this->timezone = $timezone;
        $this->customerSession = $customerSession;
        $this->mpProductModel = $mpProductModel;
        $this->clicksFactory = $clicksFactory;
        $this->userBalance = $userBalance;
        $this->userFactory = $userFactory;
        $this->affDataHelper = $affDataHelper;
        $this->requesttosellerFactory = $requesttosellerFactory;
        $this->configrationForAffiliateFactory = $configrationForAffiliateFactory;
        $this->logger = $logger;
    }

    /**
     * customer register event handler.
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        try {
            $request = $observer->getControllerAction()->getRequest();
            $serverData = $request->getServer();
            $product = $this->registry->registry('current_product');
            $postData = '';
            $postData = $request->getParams();
            if (isset($postData['aff_id'])) {
                $postData['aff_id'] = $postData['aff_id'];
            } else {
                $urlDa = explode("aff_id=", $serverData->get('HTTP_REFERER'));
                if (isset($urlDa[1])) {
                    $postData['aff_id'] =  $urlDa[1];
                }
            }
            $baseUrl = $this->storeManager->getStore()->getBaseUrl();
            if (!empty($postData['aff_id'])) {
                $urlData = explode("aff_id=", $serverData->get('HTTP_REFERER'));
                if (isset($urlData[1])) {
                    $postData['aff_id'] = $urlData[1];
                }
                $hitData = explode('catalog/product/view/id/', $request->getPathInfo());
                $hitId = isset($hitData[1])? $hitData[1] : $request->getParam('banner');
                $hitType  = isset($hitData[1])? 'product' : 'textbanner';
                $productData = explode('?', $hitData[1]);
                $product = $this->registry->registry('current_product');
                $sellerId = $this->mpProductModel->load($productData[0], "mageproduct_id")->getSellerId();
                $userColl = $this->requesttosellerFactory->create()
                                                    ->getCollection()
                                                    ->addFieldToFilter('affiliate_id', $postData['aff_id'])
                                                          ->addFieldToFilter('seller_id', $sellerId)
                                                    ->addFieldToFilter('status', 1)
                                                    ->getSize();
                                                    
                if ($userColl) {
                    $data = [
                        'customer_ip'       =>  $serverData->get('REMOTE_ADDR'),
                        'customer_domain'   =>  $serverData->get('HTTP_HOST'),
                        'hit_id'            =>  $hitId,
                        'hit_type'          =>  $hitType,
                        'aff_customer_id'   =>  $postData['aff_id'],
                        'seller_id'         =>  $sellerId,
                        'aff_commission'    =>  '',
                        'created_time'      =>  $this->timezone->formatDate(),
                        'come_from'         =>  $serverData->get('HTTP_REFERER'),
                    ];
                   
                    $cilckData = $this->clicksFactory->create()->getCollection()
                    ->addFieldToFilter('hit_id', $hitId)
                    ->addFieldToFilter('customer_ip', $serverData->get('REMOTE_ADDR'));
                    if ($cilckData->getSize() < 1) {
                        $clickDetail = $this->getAffUserClickAndComm($postData['aff_id'], $data, $sellerId);
                        $data['aff_commission'] = $clickDetail['comm'];
                        $data['commission'] = $clickDetail['comm'];
                        /** save click detail*/
                        $clickTmp = $this->clicksFactory->create();
                        $clickTmp->setData($data);
                        $clickTmp->save();
                   
                        // update balance data
                        if ($clickDetail) {
                            $userBalanceColl = $this->userBalance->create()->getCollection()
                                                ->addFieldToFilter('aff_customer_id', ['eq' => $postData['aff_id']])
                                                ->addFieldToFilter('seller_id', ['eq' => $sellerId]);
                            $this->userAmount($userBalanceColl, $clickDetail, $postData, $sellerId);
                        }
                    }
                // save in session
                    $totalAffIds = $this->customerSession->getData('aff_ids');
                    $this->saveInSession($data);
                }
            }
        } catch (\Exception $e) {
            $this->logger->info($e->getMessage());
        }
    }

    public function userAmount($userBalanceColl, $clickDetail, $postData, $sellerId)
    {
        if ($userBalanceColl->getSize()) {
            $this->userAmountSave($userBalanceColl, $clickDetail);
        } else {
            $dataTmp = [
            'aff_customer_id' => $postData['aff_id'],
            'seller_id' => $sellerId,
            'clicks' => $clickDetail['click'],
            'unique_clicks' => $clickDetail['unique_click'],
            'balance_amount' => $clickDetail['comm']
            ];
            $tempBal = $this->userBalance->create();
            $tempBal->setData($dataTmp);
            $tempBal->save();
        }
    }
    public function saveInSession($data)
    {
        $totalAffIds = $this->customerSession->getData('aff_ids');
        if (!empty($totalAffIds)) {
            $status = true;
            foreach ($totalAffIds as $affData) {
                if ($affData['hit_id'] == $data['hit_id']) {
                    $status = false;
                }
            }
            if ($status) {
                array_push($totalAffIds, $data);
                $this->customerSession->setData('aff_ids', $totalAffIds);
            }
        } else {
            $totalAffIds = [$data];
            $this->customerSession->setData('aff_ids', $totalAffIds);
        }
    }

    /**
     * getIsUniqueClick
     * @param array
     * @return bool
     */

    private function getIsUniqueClick($data)
    {
        $clickColl = $this->clicksFactory->create()->getCollection()
                                            ->addFieldToFilter('customer_ip', ['eq' => $data['customer_ip']])
                                            ->addFieldToFilter('aff_customer_id', ['eq' => $data['aff_customer_id']])
                                            ->setPageSize(1)->getFirstItem();
        return $clickColl->getEntityId() ? false : true;
    }
    public function userAmountSave($userBalanceColl, $clickDetail)
    {
        foreach ($userBalanceColl as $userBalance) {
            $clicks = $clickDetail['click'] + (int) $userBalance->getClicks();
            $uniqueClicks = $clickDetail['unique_click'] + (int) $userBalance->getUniqueClicks();
            $balanceAmount = $userBalance->getBalanceAmount()+$clickDetail['comm'];
            $userBalance->setClicks($clicks);
            $userBalance->setUniqueClicks($uniqueClicks);
            $userBalance->setBalanceAmount($balanceAmount);
            $this->_saveObject($userBalance);
        }
    }

    /**
     * getAffUserClickAndComm
     * @param int affId
     * @param array $data
     * @return false|array
     */

    private function getAffUserClickAndComm($affId, $data, $sellerId)
    {
        $affUserColl = $this->userFactory->create()->getCollection()->addFieldToFilter('customer_id', $affId)
                                                    ->setPageSize(1)->setCurPage(1)->getFirstItem();
        $response = false;
        if ($affUserColl->getId()) {
            $response = [];
            if ($this->getIsUniqueClick($data)) {
                $response['comm'] = $this->getPayPerUniqueClick($sellerId);
                $response['unique_click'] = 1;
                $response['click'] = 1;
            } else {
                $response['unique_click'] = 0;
                $response['click'] = 1;
                $response['comm'] = $this->getPayPerClick($sellerId);
            }
        }
        return $response;
    }

    public function getPayPerUniqueClick($sellerId)
    {
        $uniqueClick = $this->configrationForAffiliateFactory->create()->load($sellerId, "seller_id")->getUniqueClick();
        if ($uniqueClick) {
            return $uniqueClick;
        } else {
            $affData = $this->affDataHelper->getAffiliateConfig();
            return $affData['unique_click'];
        }
    }

    public function getPayPerClick($sellerId)
    {
        $perClick = $this->configrationForAffiliateFactory->create()->load($sellerId, "seller_id")->getPerClick();
        if ($perClick) {
            return $perClick;
        } else {
            $affData = $this->affDataHelper->getAffiliateConfig();
            return $affData['per_click'];
        }
    }

    /**
     * _saveObject
     * @param Object $object
     */
    private function _saveObject($object)
    {
        $object->save();
    }
}
