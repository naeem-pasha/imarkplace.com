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

namespace Webkul\MpAffiliate\Block\Marketplace;

use Magento\Framework\View\Element\Template\Context;
use Magento\Customer\Model\Session;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Webkul\MpAffiliate\Helper\Data as AffDataHelper;
use Magento\Catalog\Helper\Image as ImageHelper;
use Webkul\MpAffiliate\Model\RequesttosellerFactory as RequesttosellerFactory;

class Sentmailtoseller extends \Magento\Framework\View\Element\Template
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
        RequesttosellerFactory $requesttosellerFactory,
        \Magento\Framework\App\ResourceConnection $resource,
        array $data = []
    ) {
            $this->collectionFactory = $collectionFactory;
            $this->imageHelper = $imageHelper;
            $this->requesttosellerFactory = $requesttosellerFactory;
            $this->customerSession = $customerSession;
            $this->_resource = $resource;
            parent::__construct($context, $data);
    }
    public function getAllAffiliate()
    {
        $sellerId = $this->customerSession->getCustomer()->getId();
        $collection = $this->requesttosellerFactory->create()->getCollection()
                        ->addFieldToFilter("seller_id", ["eq"=>$sellerId])
                        ->addFieldToFilter("status", ["eq"=>1]);
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
                            'firstname' => 'ct.firstname',
                            'lastname'  => 'ct.lastname'
                        ]
        );
        return $collection;
    }
    public function getSaveAction()
    {
        return $this->getUrl('mpaffiliate/Marketplace/mailtoaffiliate', ['_secure' => $this->getRequest()->isSecure()]);
    }
}
