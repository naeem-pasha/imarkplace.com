<?php
/**
 * Webkul MpAffiliate Affilliate Status.
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
use Webkul\Marketplace\Model\Seller as sellerFactory;
use Webkul\MpAffiliate\Model\Requesttoseller as RequesttosellerFactory;

class Requesttoseller extends \Webkul\MpAffiliate\Block\User\UserAbstract
{
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    private $productlists;
    protected $_customerRepositoryInterface;
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    private $collectionFactory;
    private $sellerFactory;
    protected $_customer;
    private $requesttosellerFactory;
    protected $customerSession;
    /**
     * @var \Magento\Catalog\Helper\Image
     */

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
        sellerFactory $sellerFactory,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface,
        RequesttosellerFactory $requesttosellerFactory,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        CollectionFactory $collectionFactory,
        array $data = []
    ) {
        $this->_customerRepositoryInterface = $customerRepositoryInterface;
        $this->_customerFactory = $customerFactory;
        $this->sellerFactory = $sellerFactory;
        $this->collectionFactory = $collectionFactory;
        $this->requesttosellerFactory = $requesttosellerFactory;
        $this->customerSession = $customerSession;
        parent::__construct($context, $customerSession, $affDataHelper, $customerFactory, $data);
    }

    /* Get all seller from marketplace*/
    public function getAllSeller()
    {
        $customerId = $this->customerSession->getCustomer()->getId();
        $requestCollection = $this->requesttosellerFactory->getCollection()
                                        ->addFieldToFilter('affiliate_id', ['eq' => $customerId])
                                        ->addFieldToSelect("seller_id");
        $alreadyRequested=[0];
        foreach ($requestCollection->getData() as $request) {
            array_push($alreadyRequested, $request['seller_id']);
        }
        
        $collection = $this->sellerFactory->getCollection()
                        ->addFieldToFilter('is_seller', ['eq' => 1])
                        ->addFieldToFilter('seller_id', ['nin' => $alreadyRequested])
                        ->setOrder('entity_id', 'desc');
        return $collection->getData();
    }

    /*Get customer name*/
    public function getNameCustomer($customerId)
    {
        $customer = $this->_customerRepositoryInterface->getById($customerId);
        $data  = $customer;
        return $data->getFirstName()." ".$data->getLastName();
    }

    public function getShopName($customerId)
    {
        $shopName = $this->sellerFactory->getCollection()->addFieldToFilter('seller_id', $customerId)
                                                            ->addFieldToSelect("shop_url")
                                                            ->getFirstItem()
                                                            ->getShopUrl();
        return $shopName;
    }

    public function getSaveAction()
    {
        return $this->getUrl('mpaffiliate/user/senttorequest', ['_secure' => $this->getRequest()->isSecure()]);
    }

    public function getAllRequest()
    {
        $customerId = $this->customerSession->getCustomer()->getId();
        $collection = $this->requesttosellerFactory->getCollection()
                        ->addFieldToFilter('affiliate_id', ['eq' => $customerId]);
        return ($collection->getData());
    }

    public function getEmailSeller($customerId)
    {
        $customer = $this->_customerRepositoryInterface->getById($customerId);
        return $customer->getEmail();
    }

    public function getBlogUrl()
    {
        $customerId = $this->customerSession->getCustomer()->getId();
    }
}
