<?php
/**
 * Webkul MpAffiliate Update controller.
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

class Updatebanner extends \Magento\Customer\Controller\AbstractAccount
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
                    $id =$bankdata['banner_id'] ;
                    $tempAff = $this->textbannerFactory->create();
                    $tempAff->load($id);
                    $tempAff->setTitle($bankdata['title']);
                    $tempAff->setText($bankdata['text']);
                    $tempAff->setLink($bankdata['link']);
                    $tempAff->setBanner_size($bankdata['banner_size']);

                    $tempAff->save();
                    $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
                    $this->messageManager->addSuccess(__(' Banner is updated.'));
                    return $this->resultRedirectFactory->create()->setPath(
                        '*/*/textbanner',
                        ['_secure' => $this->getRequest()->isSecure()]
                    );
                }
            } catch (\Exception $e) {
                $this->logger->info($e->getMessage());
                $this->messageManager->addError($e->getMessage());
            }
        } else {
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            $this->messageManager->addSuccess(__(' Banner is not updated.'));
            return $this->resultRedirectFactory->create()->setPath(
                '*/*/textbanner',
                ['_secure' => $this->getRequest()->isSecure()]
            );
        }
    }
}
