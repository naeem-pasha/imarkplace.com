<?php
/**
 * Webkul Odoomarketplaceconnect Sellers Model
 * @category  Webkul
 * @package   Webkul_Odoomarketplaceconnect
 * @author    Webkul
 * @copyright Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\Odoomarketplaceconnect\Model\ResourceModel;

use Webkul\Odoomagentoconnect\Helper\Connection;

/**
 * Blog post mysql resource
 */
class Sellers extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Construct
     *
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param string|null $resourcePrefix
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        Connection $connection,
        \Webkul\Marketplace\Model\Seller $sellerModel,
        \Webkul\Marketplace\Model\Saleperpartner $saleperpartnerModel,
        \Magento\Customer\Model\Customer $customerObj,
        \Webkul\Odoomagentoconnect\Model\Customer $customerMapping,
        \Webkul\Odoomagentoconnect\Model\ResourceModel\Customer $customerModel,
        $resourcePrefix = null
    ) {
        $this->_customerObj = $customerObj;
        $this->_connection = $connection;
        $this->_sellerModel = $sellerModel;
        $this->_saleperpartnerModel = $saleperpartnerModel;
        $this->_customerMapping = $customerMapping;
        $this->_customerModel = $customerModel;
        parent::__construct($context, $resourcePrefix);
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('odoomarketplaceconnect_sellers', 'entity_id');
    }

    public function exportSpecificSeller($sellerId)
    {
        $response = [];
        $sellerObj = $this->_sellerModel->load($sellerId);
        $customerId = $sellerObj->getSellerId();
        $customer =  $this->_customerObj->load($customerId);
        if (!$customer->getName()) {
            $response['odoo_id'] = 0;
            return $response;
        }

        $this->_connection->updateMpSetting();
        $mappingCollection = $this->_customerMapping
            ->getCollection()
            ->addFieldToFilter('address_id', 'customer')
            ->addFieldToFilter('magento_id', $customerId);

        if (count($mappingCollection) == 0) {
            $odooId = $this->exportSpecificCustomer($customer, $customerId, $sellerObj);
            if ($odooId) {
                $this->updateCustomerMapping($sellerId, $customerId, $odooId);
            }
        } else {
            $odooId = $mappingCollection->getFirstItem()->getOdooId();
            if ($odooId) {
                $this->updateCustomerMapping($sellerId, $customerId, $odooId);
            }
            $this->updateSellerData($customer, $customerId, $odooId);
        }

        $response['odoo_id'] = $odooId;
        return $response;
    }

    public function exportSpecificCustomer($customer, $customerId, $sellerObj)
    {
        $odooCustomerId = 0;
        if ($customerId) {
            $customerArray = $this->getSellerArray($customer, $customerId);
            $customerArray['url_handler'] = $sellerObj->getShopUrl();
            $odooCustomerId = $this->_customerModel->odooCustomerCreate($customerArray, $customerId, 'customer');
            if ($odooCustomerId) {
                foreach ($customer->getAddresses() as $address) {
                    $odooAddressId = $this->_customerModel
                        ->syncSpecificAddressAtOdoo($odooCustomerId, $customer, $address);
                }
            }
        }
        return $odooCustomerId;
    }

    public function getSellerArray($customer, $customerId)
    {
        $sellerArray =[];

        $sellerCommission = $this->_saleperpartnerModel->getCollection();
        $sellerCommission->addFieldToFilter('seller_id', $customerId);
        $commision = $this->_connection->getStoreConfig('marketplace/general_settings/percent');
        foreach ($sellerCommission as $comm) {
            $commision = $comm->getData()['commission_rate'];
            break;
        }
     
        $sellerArray =  array(
            'seller'=>true,
            'is_company'=>false,
            'name'=>urlencode($customer->getName()),
            'email'=>urlencode($customer->getEmail()),
            'commission'=>(double)$commision,
            'customer_rank'=>1,
            'state'=>'pending',
        );
        return $sellerArray;
    }

    public function updateCustomerMapping($sellerId, $customerId, $odooId)
    {
        $helper = $this->_connection;
        $mappingData = [
                'odoo_id'=>$odooId,
                'magento_id'=>$customerId,
                'mp_seller_id'=>$sellerId,
                'need_sync'=>'no',
                'created_by'=>$helper::$mageUser
            ];
        $helper->createMapping(\Webkul\Odoomarketplaceconnect\Model\Sellers::class, $mappingData);
    
        $mapArray = [
            'isSeller'=>true,
            'odoo_id'=>(int)$odooId,
            'mpSellerId'=>(int)$sellerId,
        ];
        $helper->callOdooMethod('connector.partner.mapping', 'updateCustomerMapping', [$mapArray], true);
        return true;
    }

    public function updateSellerData($customer, $customerId, $odooId)
    {
        $sellerArray = $this->getSellerArray($customer, $customerId);
        $odooCustomerId = $this->_customerModel->odooCustomerUpdate($customerId, 'customer', $sellerArray, $odooId);
        $this->updateSellerAddress($customer, $customerId, $odooId);
        return true;
    }

    public function updateSpecificSeller($mappingId)
    {
        $response = [];
        $helper =  $this->_connection;
        $helper->updateMpSetting();
        $mappingObj = $helper->getModel(\Webkul\Odoomarketplaceconnect\Model\Sellers::class)->load($mappingId);
        $odooId = $mappingObj->getOdooId();
    
        $sellerObj = $this->_sellerModel->load($mappingObj->getMpSellerId());
        $customerId = $sellerObj->getSellerId();
        $customer =  $this->_customerObj->load($customerId);
        $sellerArray = $this->getSellerArray($customer, $customerId);
        $sellerArray['url_handler'] = $sellerObj->getShopUrl();
        $odooCustomerId = $this->_customerModel->odooCustomerUpdate($customerId, 'customer', $sellerArray, (int)$odooId);
        $this->updateSellerAddress($customer, $customerId, $odooId);
     
        $mappingObj->setNeedSync('no')->save();
        $response['odoo_id'] = $odooId;
        return $response;
    }

    public function updateSellerAddress($customer, $customerId, $odooCustomerId)
    {
        #Need to Add Marketplace Dependency
        foreach ($customer->getAddresses() as $address) {
            $addressCollection = $this->_customerMapping->getCollection()
                ->addFieldToFilter('magento_id', $customerId)
                ->addFieldToFilter('address_id', $address->getId());
            if ($addressCollection->getSize() > 0) {
                    $addressMap = $addressCollection->getFirstItem();
                    $needSync = $addressMap->getNeedSync();
                    if ($needSync == 'yes') {
                        $odooId = $addressMap->getOdooId();
                        $odooAddressId = $this->_customerModel->syncSpecificAddressAtOdoo($odooCustomerId,$customer, $address, 'write', $odooId);
                    }
            } else {
                $odooAddressId = $this->_customerModel->syncSpecificAddressAtOdoo($odooCustomerId, $customer, $address);
            }
        }
        return true;
    }
}
