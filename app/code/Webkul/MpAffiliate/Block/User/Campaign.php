<?php
/**
 * Webkul MpAffiliate Campaign.
 *
 * @category  Webkul
 * @package   Webkul_MpAffiliate
 * @author    Webkul
 * @copyright Copyright (c)  Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\MpAffiliate\Block\User;

use Magento\Framework\View\Element\Template\Context;
use Magento\Customer\Model\Session;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Webkul\MpAffiliate\Helper\Data as AffDataHelper;
use Magento\Catalog\Helper\Image as ImageHelper;

class Campaign extends \Webkul\MpAffiliate\Block\User\UserAbstract
{
    /**
     * @param Context           $context
     * @param Session           $customerSession,
     * @param AffDataHelper     $affDataHelper,
     * @param CollectionFactory $collectionFactory,
     * @param array             $data
     */
    public function __construct(
        Context $context,
        Session $customerSession,
        AffDataHelper $affDataHelper,
        CollectionFactory $collectionFactory,
        ImageHelper $imageHelper,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Webkul\MpAffiliate\Model\Requesttoseller $reqToSellerModel,
        \Webkul\Marketplace\Model\Product $mpProductModel,
        \Magento\Catalog\Model\Product $productModel,
        array $data = []
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->imageHelper = $imageHelper;
        $this->_customerFactory = $customerFactory;
        $this->customerSession = $customerSession;
        $this->reqToSellerModel = $reqToSellerModel;
        $this->mpProductModel = $mpProductModel;
        $this->productModel = $productModel;
        parent::__construct($context, $customerSession, $affDataHelper, $customerFactory, $data);
    }

    public function getSaveAction()
    {
        return $this->getUrl('mpaffiliate/user/campaign', ['_secure' => $this->getRequest()->isSecure()]);
    }

    public function getAllDetail($affId)
    {

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
        $productColl = $this->productModel->getCollection()
                                        ->addAttributeToSelect('*')
                                        ->addFieldToFilter('status', ['eq'=>1])
                                        ->addFieldToFilter('entity_id', ['in'=>$products])
                                        ->addFieldToFilter('visibility', ['neq'=>1])
                                        ->setOrder('entity_id', 'desc');
        $string='';
        $i=1;
        foreach ($productColl as $product) {
            $string=$string."<div class='showdata' id = 'showmain".$i."'
             data='".$product->getProductUrl()."?&aff_id=".$affId."'>
             <div ><img style='height:100px;' src='".$this->getProImgUrl($product)."'
              alt='Ad'><div>".$product->getName()."</div></div></div>";
                $i++;
        }
        return $string;
    }

    public function getProImgUrl($products)
    {
        return $this->imageHelper->init($products, 'product_page_image_small')
                                  ->setImageFile($products->getFile())->getUrl();
    }
}
