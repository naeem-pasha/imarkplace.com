<?php
/**
 * Webkul MpAffiliate Banner.
 * @category  Webkul
 * @package   Webkul_MpAffiliate
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\MpAffiliate\Block\User;

use Magento\Framework\View\Element\Template\Context;
use Magento\Customer\Model\Session;
use Webkul\MpAffiliate\Helper\Data as AffDataHelper;
use Magento\Catalog\Helper\Image as ImageHelper;

class Banner extends \Webkul\MpAffiliate\Block\User\UserAbstract
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    private $customerSession;

    /**
     * @var \Webkul\MpAffiliate\Model\Requesttoseller
     */
    private $reqToSellerModel;

    /**
     * @var \Webkul\Marketplace\Model\Product
     */
    private $mpProductModel;

    /**
     * @var \Webkul\MpAffiliate\Model\TextBanner
     */
    private $textBannerModel;

    /**
     * @var \Magento\Catalog\Model\Product
     */
    private $productModel;

    /**
     * @param Context                                           $context
     * @param Session                                           $customerSession,
     * @param AffDataHelper                                     $affDataHelper,
     * @param \Magento\Customer\Model\CustomerFactory           $customerFactory,
     * @param \Webkul\MpAffiliate\Model\Requesttoseller         $reqToSellerModel,
     * @param \Webkul\Marketplace\Model\Product                 $mpProductModel,
     * @param \Webkul\MpAffiliate\Model\TextBanner              $textBannerModel,
     * @param \Magento\Catalog\Model\Product                    $productModel,
     * @param array                                             $data
     */
    public function __construct(
        Context $context,
        Session $customerSession,
        AffDataHelper $affDataHelper,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Webkul\MpAffiliate\Model\Requesttoseller $reqToSellerModel,
        \Webkul\Marketplace\Model\Product $mpProductModel,
        \Webkul\MpAffiliate\Model\TextBanner $textBannerModel,
        \Magento\Catalog\Model\Product $productModel,
        ImageHelper $imageHelper,
        array $data = []
    ) {
        $this->customerSession = $customerSession;
        $this->reqToSellerModel = $reqToSellerModel;
        $this->mpProductModel = $mpProductModel;
        $this->textBannerModel = $textBannerModel;
        $this->productModel = $productModel;
        $this->imageHelper = $imageHelper;
        $this->customerFactory = $customerFactory;
        parent::__construct($context, $customerSession, $affDataHelper, $customerFactory, $data);
    }

    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if ($this->getAllBanner()) {
            $pager = $this->getLayout()->createBlock(
                \Magento\Theme\Block\Html\Pager::class,
                'mpaffiliate.user.banner.pager'
            )->setCollection(
                $this->getAllBanner()
            );
            $this->setChild('pager', $pager);
            $this->getAllBanner();
        }
        return $this;
    }

    public function getAllBanner()
    {
        //get values of current page
        $page=($this->getRequest()->getParam('p'))? $this->getRequest()->getParam('p') : 1;
        //get values of current limit
        $pageSize=($this->getRequest()->getParam('limit'))? $this->getRequest()->getParam('limit') : 10;

        $bannerType = $this->getRequest()->getParam('ads');
        $customerid = $this->customerSession->getCustomer()->getId();
        $collection = $this->reqToSellerModel->getCollection()
                        ->addFieldToFilter('affiliate_id', ['eq'=>$customerid])
                        ->addFieldToFilter('status', ['eq'=>1]);
        $sellerIds = [];
        foreach ($collection as $value) {
            array_push($sellerIds, $value->getSellerId());
        }

        $proColl = $this->mpProductModel->getCollection()
                            ->addFieldToFilter('seller_id', ['in'=>$sellerIds]);
        $products = [];
        foreach ($proColl as $data) {
            array_push($products, $data->getMageproductId());
        }

        $query = $this->getRequest()->getParam('q');

        if ($bannerType =='text') {
            $bannerColl = $this->textBannerModel->getCollection()
                ->addFieldToFilter('title', ['like'=>'%'.$query.'%'])
                ->addFieldToFilter('seller_id', ['in'=>$sellerIds])
                ->addFieldToFilter('status', ['eq'=>1]);
        } else {
            $bannerColl = $this->productModel->getCollection()
                ->addAttributeToSelect('*')
                ->addFieldToFilter('name', ['like'=>'%'.$query.'%'])
                ->addFieldToFilter('status', ['eq'=>1])
                ->addFieldToFilter('entity_id', ['in'=>$products])
                ->addFieldToFilter('visibility', ['neq'=>1])
                ->joinField(
                    'is_in_stock',
                    'cataloginventory_stock_item',
                    'is_in_stock',
                    'product_id=entity_id',
                    'is_in_stock=1',
                    '{{table}}.stock_id=1',
                    'left'
                );
        }
      
        $bannerColl->setPageSize($pageSize);
        $bannerColl->setCurPage($page);
        $bannerColl->setOrder('entity_id', 'desc');
        $this->setCollection($bannerColl);
        return $bannerColl;
    }

    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    public function getProImgUrl($products)
    {
        return $this->imageHelper->init($products, 'product_page_image_small')
                                    ->setImageFile($products->getFile())->getUrl();
    }
    public function getSellerNameByProdId($productId)
    {
        $sellerId = $this->mpProductModel->load($productId, "mageproduct_id")->getSellerId();
        $sellerName = $this->customerFactory->create()->load($sellerId)->getName();
        return $sellerName;
    }
    public function getSellerNameBySelllerId($sellerId)
    {
        $sellerName = $this->customerFactory->create()->load($sellerId)->getName();
        return $sellerName;
    }
    public function getAffId()
    {
        return $this->customerSession->getCustomer()->getId();
    }
    public function getHtmlCodeForAds($product, $customerid)
    {
        $productLink = $this->getUrl().'catalog/product/view/id/'.$product->getId();
        return "<div style='padding: 10px; border: 1px solid #CCC;width:12%; '>
        <a href='".$productLink."?&aff_id=".$customerid."'
         style='text-decoration: none;'><div style='text-align:center;'><strong>".$product->getName()."
         </strong></div><img src='".$this->getProImgUrl($product)."' alt='Ad' /></a></div>";
    }

    public function getCurrentUrl()
    {
        return $this->_urlBuilder->getCurrentUrl('');
    }
}
