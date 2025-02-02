<?php
/**
 * Webkul MpAffiliate Campaign controller.
 * @category  Webkul
 * @package   Webkul_MpAffiliate
 * @author    Webkul
 * @copyright Copyright (c)  Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\MpAffiliate\Controller\User;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Webkul\MpAffiliate\Model\Requesttoseller;
use Magento\Framework\Controller\ResultFactory;
use Webkul\MpAffiliate\Helper\Email as HelperEmail;

class Senttorequest extends \Magento\Customer\Controller\AbstractAccount
{
    protected $customerSession;
    private $requesttosellerFactory;

    public function __construct(
        Context $context,
        Requesttoseller $requesttosellerFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Webkul\MpAffiliate\Logger\Logger $logger,
        HelperEmail $helperEmail
    ) {
        $this->requesttosellerFactory = $requesttosellerFactory;
        $this->customerSession = $customerSession;
        $this->logger = $logger;
        $this->helperEmail = $helperEmail;
        parent::__construct($context);
    }

    /**
     * Affiliate Request To Seller
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        if ($this->getRequest()->isPost()) {
            try {
                $bankdata = $this->getRequest()->getParams();
                if ($bankdata) {
                    $aff_id = $this->customerSession->getCustomer()->getId();
                    $data=[
                        'seller_id' => $bankdata['selecttoseller'],
                        'affiliate_id' => $aff_id,
                        'aff_link' => $bankdata['blog_url'],
                        'status' => 2,
                    ];
                    $tempAff = $this->requesttosellerFactory;
                    $tempAff->setData($data);
                    $tempAff->save();
                    $this->helperEmail->sellerNotifyforAffiliateRequest($bankdata['selecttoseller']);
                    $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
                    $this->messageManager->addSuccess(__('Sent the request to seller.'));
                    $resultRedirect->setUrl($this->_redirect->getRefererUrl());
                    return $resultRedirect;
                }
            } catch (\Exception $e) {
                $this->logger->info($e->getMessage());
                $this->messageManager->addError($e->getMessage());
            }
        } else {
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            $this->messageManager->addError(__('Not Sent the request to seller.'));
            $resultRedirect->setUrl($this->_redirect->getRefererUrl());
            return $resultRedirect;
        }
    }
}
