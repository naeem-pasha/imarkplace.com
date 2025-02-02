<?php
/**
 * Webkul MpAffiliate Marketplace Request.
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
use Webkul\MpAffiliate\Helper\Data as AffDataHelper;
use Webkul\MpAffiliate\Model\Requesttoseller as RequesttosellerFactory;

class Request extends \Magento\Framework\View\Element\Template
{
    private $productlists;
    protected $_customer;
    private $requesttosellerFactory;
    protected $customerSession;
    protected $_affReqlists;
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
        RequesttosellerFactory $requesttosellerFactory,
        \Magento\Customer\Model\Customer $customer,
        \Magento\Framework\App\ResourceConnection $resource,
        array $data = []
    ) {
        $this->customerSession = $customerSession;
        $this->affDataHelper = $affDataHelper;
        $this->customerFactory = $customerFactory;
        $this->requesttosellerFactory = $requesttosellerFactory;
        $this->_customer = $customer;
        $this->_resource = $resource;
        parent::__construct($context, $data);
    }

    /**
     * Get all request affiliate data
     *
     * @return  collection
     */
    public function getAllAffiliate()
    {
        if (!$this->_affReqlists) {
            $filter = $this->getRequest()->getParams();
            $sellerId = $this->customerSession->getCustomer()->getId();
            $collection = $this->requesttosellerFactory->getCollection()
                            ->addFieldToFilter("seller_id", ["eq"=>$sellerId]);
            if (isset($filter['status']) && $filter['status']!="") {
                $collection->addFieldToFilter("status", ["eq"=>$filter['status']]);
            }

            if (isset($filter['blog_url']) && $filter['blog_url']!="") {
                $collection->addFieldToFilter("aff_link", ["like"=>'%'.$filter['blog_url'].'%']);
            }

            /**
            * $customerTable name of customer table
            */
            $customerTable = $this->_resource->getTableName('customer_entity');
            $collection->getSelect()->join(
                [
                             'ct'=>$customerTable
                         ],
                "main_table.affiliate_id = ct.entity_id",
                [
                             'email' => 'ct.email',
                             'firstname' => 'ct.firstname',
                             'lastname'  => 'ct.lastname'
                         ]
            );
            if (isset($filter['s']) && $filter['s']!="") {
                $collection->addFieldToFilter("email", ["like"=>'%'.$filter['s'].'%']);
            }
            $collection->setOrder('affiliate_request_id', 'desc');
            $this->_affReqlists =  $collection;
        }
        return $this->_affReqlists;
    }

    /**
     * @return $this
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if ($this->getAllAffiliate()) {
            $pager = $this->getLayout()->createBlock(
                \Magento\Theme\Block\Html\Pager::class,
                'marketplace.affiliate.request.list.pager'
            )->setCollection(
                $this->getAllAffiliate()
            );
            $this->setChild('pager', $pager);
            $this->getAllAffiliate()->load();
        }
        return $this;
    }

    public function massActionOptions()
    {
        $options = [ 0=>'Unapprove', 1=>'Approve',2=>'Delete'];
        return $options;
    }

    /**
     * @return string
     */
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }
}
