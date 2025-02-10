<?php
/**
 * Webkul MpAffiliate Marketplace Approve controller.
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
use Webkul\MpAffiliate\Model\RequesttosellerFactory;
use Webkul\MpAffiliate\Model\UserBalanceFactory;
use Magento\Customer\Model\Session;
use Webkul\MpAffiliate\Helper\Email as HelperEmail;

class MassRequestAction extends \Magento\Customer\Controller\AbstractAccount
{
    /**
     * @var PageFactory
     */
    private $_resultPageFactory;

    private $requesttosellerFactory;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        RequesttosellerFactory $requesttosellerFactory,
        PageFactory $resultPageFactory,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        Session $customerSession,
        UserBalanceFactory $userBalnaceFactory,
        \Webkul\MpAffiliate\Helper\Data $mpAffiliateHelper,
        \Webkul\MpAffiliate\Logger\Logger $logger,
        HelperEmail $helperEmail
    ) {
        $this->_resultPageFactory = $resultPageFactory;
        $this->requesttosellerFactory = $requesttosellerFactory;
        $this->jsonHelper = $jsonHelper;
        $this->customerSession = $customerSession;
        $this->userBalanceFactory = $userBalnaceFactory;
        $this->mpAffiliateHelper = $mpAffiliateHelper;
        $this->logger = $logger;
        $this->helperEmail = $helperEmail;
        parent::__construct($context);
    }

    /**
     * Add Auction on product page
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        try {
            $recordUpdate = 0;
            if ($this->getRequest()->isPost()) {
                $sellerId = $this->customerSession->getCustomer()->getId();
                $data = $this->getRequest()->getParams();
                $this->dataUpdate($data, $sellerId, $recordUpdate);
            }
        } catch (\Exception $e) {
            $this->logger->info($e->getMessage());
            $this->messageManager->addError($e->getMessage());
        }
        return $this->resultRedirectFactory->create()->setPath(
            $this->_url->getUrl('*/*/affiliaterequest'),
            ['_secure' => $this->getRequest()->isSecure()]
        );
    }
    
    public function dataUpdate($data, $sellerId, $recordUpdate)
    {
        if ($data['massaction']==1 || $data['massaction']==0) {
            foreach ($data['mass_ids'] as $affId) {
                $tempAff = $this->requesttosellerFactory->create()
                                ->getCollection()
                                ->addFieldToFilter("affiliate_request_id", ["eq"=>$affId])
                                ->addFieldToFilter("seller_id", ["eq"=>$sellerId]);
                foreach ($tempAff as $req) {
                    if ($data['massaction']==1) {
                        $req->setId($req['affiliate_request_id']);
                        $req->setStatus(1);
                        $this->helperEmail->accountUpdateNotifyBySeller($req['affiliate_id'], __("Approved"));
                        $this->registerinUserBalance($req['affiliate_id'], $sellerId);
                    } else {
                        $req->setId($req['affiliate_request_id']);
                        $this->helperEmail->accountUpdateNotifyBySeller($req['affiliate_id'], __("Unapproved"));
                        $req->setStatus(0);
                    }
                    $req->save();
                    $recordUpdate++;
                }
            }
            $this->messageManager->addSuccess(__('A total of %1 user(s) have been updated.', $recordUpdate));
        }

        if ($data['massaction']==2) {
            foreach ($data['mass_ids'] as $affId) {
                $tempAff = $this->requesttosellerFactory->create()
                                ->getCollection()
                                ->addFieldToFilter("affiliate_request_id", ["eq"=>$affId])
                                ->addFieldToFilter("seller_id", ["eq"=>$sellerId]);
                foreach ($tempAff as $req) {
                    $req->setId($req['affiliate_request_id']);
                    $req->delete();
                    $this->helperEmail->accountUpdateNotifyBySeller($req['affiliate_id'], __("Deleted"));
                    $recordUpdate++;
                }
            }
            $this->messageManager->addSuccess(__('A total of %1 users(s) have been removed.', $recordUpdate));
        }
    }

    public function registerinUserBalance($affId, $sellerId)
    {
        $userBalance = $this->userBalanceFactory->create()->getCollection()
                            ->addFieldToFilter("aff_customer_id", ["eq"=>$affId])
                            ->addFieldToFilter("seller_id", ["eq"=>$sellerId]);
        if ($userBalance->getSize()==0) {
            $customer = $this->mpAffiliateHelper->getCustomerData($affId);
            $updatedata=[
                'aff_customer_id'   =>  $affId,
                'seller_id'         =>  $sellerId,
                'aff_name'          =>  $customer->getFirstname()." ".$customer->getLastname(),
                'aff_clicks'        =>  0,
                'aff_uniqueclicks'  =>  0,
                'aff_paymentmethod' =>  '',
                'balance'           =>  0,
            ];
            $this->userBalanceFactory->create()->addData($updatedata)->save();
        }
    }
}
