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
use Magento\Customer\Model\Session;

class Approve extends \Magento\Customer\Controller\AbstractAccount
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
        \Webkul\MpAffiliate\Logger\Logger $logger
    ) {
        $this->_resultPageFactory = $resultPageFactory;
        $this->requesttosellerFactory = $requesttosellerFactory;
        $this->jsonHelper = $jsonHelper;
        $this->customerSession = $customerSession;
        $this->logger = $logger;
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
            if ($this->getRequest()->isPost()) {
                $flag = 0;
                $sellerId = $this->customerSession->getCustomer()->getId();
                $data=$this->getRequest()->getParams();
                if ($data['type']==1 || $data['type']==0) {
                    $tempAff = $this->requesttosellerFactory->create()
                                    ->getCollection()
                                    ->addFieldToFilter("affiliate_request_id", ["eq"=>$data['req_id']])
                                    ->addFieldToFilter("seller_id", ["eq"=>$sellerId]);
                    foreach ($tempAff as $req) {
                        if ($data['type']==1) {
                            $req->setId($data['req_id']);
                            $req->setStatus(1);
                        } else {
                            $req->setId($data['req_id']);
                            $req->setStatus(0);
                        }
                        $req->save();
                        $flag = 1;
                    }
                }

                if ($data['type']==2) {
                    $tempAff = $this->requesttosellerFactory->create()
                                    ->getCollection()
                                    ->addFieldToFilter("affiliate_request_id", ["eq"=>$data['req_id']])
                                    ->addFieldToFilter("seller_id", ["eq"=>$sellerId]);
                    foreach ($tempAff as $req) {
                        $req->setId($data['req_id']);
                        $req->delete();
                        $flag = 1;
                    }
                }
            }
            $this->getResponse()->representJson(
                $this->jsonHelper->jsonEncode(['result' => $flag])
            );
        } catch (\Exception $e) {
            $this->logger->info($e->getMessage());
            $this->getResponse()->representJson(
                $this->jsonHelper->jsonEncode($e->getMessage())
            );
        }
    }
}
