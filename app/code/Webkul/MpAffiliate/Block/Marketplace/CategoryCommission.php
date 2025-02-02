<?php
/**
 * Webkul MpAffiliate Marketplace Category Commission.
 *
 * @category  Webkul
 * @package   Webkul_MpAffiliate
 * @author    Webkul
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\MpAffiliate\Block\Marketplace;

use Magento\Framework\View\Element\Template\Context;
use Magento\Customer\Model\Session;
use Webkul\MpAffiliate\Helper\Data as AffDataHelper;

class CategoryCommission extends \Magento\Framework\View\Element\Template
{

    /**
     * @param Context         $context
     * @param Session         $customerSession,
     * @param
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\View\Model\PageLayout\Config\BuilderInterface $pageLayoutBuilder,
        \Magento\Catalog\Ui\Component\Product\Form\Categories\Options $categories,
        \Webkul\MpAffiliate\Model\CatcommissionFactory $catcommissionFact,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Catalog\Model\CategoryFactory $categoryFact,
        array $data = []
    ) {
        $this->categories = $categories;
        $this->categoryFact = $categoryFact;
        $this->catcommissionFact = $catcommissionFact;
        $this->customerSession = $customerSession;
        $this->formKey = $context->getFormKey();
        parent::__construct($context, $data);
    }
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if ($this->getCommissions()) {
            $pager = $this->getLayout()->createBlock(
                \Magento\Theme\Block\Html\Pager::class,
                'mpaffiliate.commission.list.pager'
            )->setCollection(
                $this->getCommissions()
            );
            $this->setChild('pager', $pager);
            $this->getCommissions()->load();
        }

        return $this;
    }

    //get saved commisssions list
    public function getCommissions()
    {
        //get values of current page
        $page=($this->getRequest()->getParam('p'))? $this->getRequest()->getParam('p') : 1;
        //get values of current limit
        $pageSize=($this->getRequest()->getParam('limit'))? $this->getRequest()->getParam('limit') : 10;
        
        $sellerId = $this->customerSession->getCustomer()->getId();
        $collection = $this->catcommissionFact->create()
                            ->getCollection()
                            ->addFieldToFilter("seller_id", ["eq"=>$sellerId]);
        $collection->setPageSize($pageSize);
        $collection->setCurPage($page);
        return $collection;
    }

    //catagory tree in json format for the ui-component
    public function getCategoriesTree()
    {
        $categories = $this->categories->toOptionArray();
        return json_encode($categories);
    }

    //pager for commissions
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    //get category name from cat id
    public function getCategoryName($catId)
    {
        return $this->categoryFact->create()->load($catId)->getName();
    }

    //get commission detail for edit
    public function getCommission($id)
    {
        $sellerId = $this->customerSession->getCustomer()->getId();
        $collection = $this->catcommissionFact->create()
                    ->getCollection()
                    ->addFieldToFilter("seller_id", ["eq"=>$sellerId])
                    ->addFieldToFilter("entity_id", ["eq"=>$id])
                    ->getFirstItem();
        return $collection;
    }
}
