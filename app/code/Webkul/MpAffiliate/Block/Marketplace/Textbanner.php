<?php
/**
 * Webkul MpAffiliate Marketplace Textbanner.
 *
 * @category  Webkul
 * @package   Webkul_MpAffiliate
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\MpAffiliate\Block\Marketplace;

use Magento\Framework\View\Element\Template\Context;
use Magento\Customer\Model\Session;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Webkul\MpAffiliate\Helper\Data as AffDataHelper;
use Webkul\MpAffiliate\Model\Requesttoseller as RequesttosellerFactory;

class Textbanner extends \Magento\Framework\View\Element\Template
{
    private $textbanner;

    /**
     * @param Context         $context
     * @param Session         $customerSession,
     * @param RedirectFactory $redirect,
     * @param AffDataHelper   $affDataHelper,
     * @param array           $data
     */
    public function __construct(
        Context $context,
        Session $customerSession,
        AffDataHelper $affDataHelper,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Customer\Model\Customer $customer,
        CollectionFactory $productModel,
        \Magento\Framework\App\ResourceConnection $resource,
        \Webkul\Marketplace\Block\Product\Productlist $mpProductList,
        \Webkul\MpAffiliate\Model\TextBannerFactory $textBannerFactory,
        \Webkul\Marketplace\Model\ProductFactory $marketplaceProduct,
        RequesttosellerFactory $requesttosellerFactory,
        array $data = []
    ) {
        $this->customerSession = $customerSession;
        $this->affDataHelper = $affDataHelper;
        $this->customerFactory = $customerFactory;
        $this->_customer = $customer;
        $this->_productModel = $productModel;
        $this->_resource = $resource;
        $this->_mpProductList = $mpProductList;
        $this->_textBannerFactory = $textBannerFactory;
        $this->_marketplaceProduct = $marketplaceProduct;
        $this->_requesttosellerFactory = $requesttosellerFactory;
        parent::__construct($context, $data);
    }
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if ($this->getTextBanner()) {
            $pager = $this->getLayout()->createBlock(
                \Magento\Theme\Block\Html\Pager::class,
                'marketplace.banner.list.pager'
            )->setCollection(
                $this->getTextBanner()
            );
            $this->setChild('pager', $pager);
            $this->getTextBanner()->load();
        }

        return $this;
    }

    public function getHtmlCodeForAds($val)
    {
        return "<div style='padding: 10px; '><input type='radio' name='pay' value='".$val."'> $val </div>";
    }

    public function getSaveBanner()
    {
        return $this->getUrl('mpaffiliate/marketplace/savebanner', ['_secure' => $this->getRequest()->isSecure()]);
    }

    public function getTextBanner()
    {
        //get values of current page
        $page=($this->getRequest()->getParam('p'))? $this->getRequest()->getParam('p') : 1;
        //get values of current limit
        $pageSize=($this->getRequest()->getParam('limit'))? $this->getRequest()->getParam('limit') : 10;
        
        $sellerId = $this->customerSession->getCustomer()->getId();
        $collection = $this->_textBannerFactory->create()
                            ->getCollection()
                            ->addFieldToFilter("seller_id", ["eq"=>$sellerId]);
        $collection->setPageSize($pageSize);
        $collection->setCurPage($page);
        return $collection;
    }

    public function getTextBannerById($id)
    {
        $collection = $this->_textBannerFactory->create()->getCollection();
        $collection = $collection->addFieldToFilter('entity_id', $id);
        return(($collection->getData()));
    }

    //Get All Seller Product
    public function getSellerProducts()
    {
        $customerId = $this->customerSession->getCustomerId();
        $sellerIds = $this->_requesttosellerFactory->getCollection()
                                        ->addFieldToFilter('seller_id', ['eq' => $customerId])
                                        ->addFieldToFilter('status', ['eq' => 1])
                                        ->addFieldToSelect("seller_id")
                                        ->getColumnValues('seller_id');
        $productIds = $this->_marketplaceProduct->create()->getCollection()
                                        ->addFieldToFilter('seller_id', ['in' => $sellerIds])
                                        ->addFieldToFilter('status', ['eq' => 1])
                                        ->addFieldToSelect("mageproduct_id")
                                        ->getColumnValues('mageproduct_id');
        $collection = $this->_productModel->create()->addAttributeToSelect('*')
                                                    ->addFieldToFilter('entity_id', ['in' => $productIds]);

        return $collection;
    }

    public function getSaveAction()
    {
        return $this->getUrl('mpaffiliate/marketplace/savetextbanner', ['_secure' => $this->getRequest()->isSecure()]);
    }

    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }
}
