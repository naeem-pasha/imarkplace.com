<?php
/**
 * Webkul MpAffiliate Text Banner Save Controller.
 * @category  Webkul
 * @package   Webkul_MpAffiliate
 * @author    Webkul
 * @copyright Copyright (c)  Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\MpAffiliate\Controller\Marketplace;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Webkul\MpAffiliate\Model\TextBannerFactory;
use Magento\Framework\Controller\ResultFactory;

class Savetextbanner extends \Magento\Customer\Controller\AbstractAccount
{
    protected $customerSession;
    private $textbannerFactory;
    public function __construct(
        Context $context,
        TextBannerFactory $textbannerFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Webkul\MpAffiliate\Logger\Logger $logger
    ) {
    
        $this->textbannerFactory = $textbannerFactory;
        $this->customerSession = $customerSession;
        $this->logger = $logger;
        parent::__construct($context);
    }
    /**
     * Affiliate Email Campaign
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {

        if ($this->getRequest()->isPost()) {
            try {
                $bankdata = $this->getRequest()->getParams();

                if ($bankdata) {
                    $seller_id = $this->customerSession->getCustomer()->getId();
                    $bankdata['seller_id'] = $seller_id;
                    $tempAff = $this->textbannerFactory->create();
                    $tempAff->setData($bankdata);
                    $tempAff->save();
                    $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
                    $this->messageManager->addSuccess(__(' Banner is created.'));
                    $resultRedirect->setUrl($this->_redirect->getRefererUrl());
                    return $resultRedirect;
                }
            } catch (\Exception $e) {
                $this->logger->info($e->getMessage());
                $this->messageManager->addError($e->getMessage());
            }
        } else {
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            $this->messageManager->addSuccess(__(' Banner is not created.'));
            $resultRedirect->setUrl($this->_redirect->getRefererUrl());
            return $resultRedirect;
        }
    }
}
