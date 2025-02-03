<?php
/**
 * Webkul MpAffiliate Text Banner controller.
 * @category  Webkul
 * @package   Webkul_MpAffiliate
 * @author    Webkul
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\MpAffiliate\Controller\Marketplace;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Controller\ResultFactory;

class CategoryCommissionSave extends \Magento\Customer\Controller\AbstractAccount
{
    /**
     * @var PageFactory
     */
    private $resultPageFactory;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        \Webkul\Marketplace\Helper\Data $marketplaceHelper,
        \Magento\Customer\Model\Session $customerSession,
        \Webkul\MpAffiliate\Model\CatcommissionFactory $catcommissionFact
    ) {

        $this->resultPageFactory = $resultPageFactory;
        $this->marketplaceHelper = $marketplaceHelper;
        $this->catcommissionFact = $catcommissionFact;
        $this->customerSession = $customerSession;
        parent::__construct($context);
    }

    /**
     * Add Auction on product page
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        if ($this->getRequest()->isPost()) {
            try {
                $data = $this->getRequest()->getParams();
                if ($data) {
                    $seller_id = $this->customerSession->getCustomer()->getId();
                    $isExist = 0;
                    if (!isset($data['entity_id'])) {
                        $isExist = $this->catcommissionFact->create()->getCollection()
                                                        ->addFieldToFilter("seller_id", $seller_id)
                                                        ->addFieldToFilter("category_id", $data['category_id'])
                                                        ->getSize();
                    }
                    if (!$isExist) {
                        $data['seller_id'] = $seller_id;
                        $tempAffCatComm = $this->catcommissionFact->create();
                        $tempAffCatComm->setData($data);
                        $tempAffCatComm->save();
                        $this->messageManager->addSuccess(__('Category Commission is saved.'));
                    } else {
                        $this->messageManager->addError(__('Commission for this category already exist.'));
                    }
                }
                $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
                $resultRedirect->setUrl($this->_url->getUrl("mpaffiliate/marketplace/categorycommission"));
                return $resultRedirect;
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }
        } else {
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            $this->messageManager->addError(__('Category Commission is not saved.'));
            $resultRedirect->setUrl($this->_url->getUrl("mpaffiliate/marketplace/categorycommission"));
            return $resultRedirect;
        }
    }
}
