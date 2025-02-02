<?php
/**
 * Webkul Odoomarketplaceconnect DisapproveSellerAfterObserver
 *
 * @category  Webkul
 * @package   Webkul_Odoomarketplaceconnect
 * @author    Webkul
 * @copyright Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\Odoomarketplaceconnect\Observer;

use Magento\Framework\Event\ObserverInterface;

/**
 * Webkul Odoomarketplaceconnect DisapproveSellerAfterObserver Observer.
 */
class DisapproveSellerAfterObserver implements ObserverInterface
{
    public function __construct(
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Webkul\Marketplace\Model\Seller $sellerModel,
        \Webkul\Odoomarketplaceconnect\Model\Sellers $sellersMapping,
        \Webkul\Odoomagentoconnect\Helper\Connection $connection
    ) {
        $this->_sellerModel = $sellerModel;
        $this->_sellersMapping = $sellersMapping;
        $this->_connection = $connection;
        $this->_messageManager = $messageManager;
    }

    /**
     * admin customer save after event handler.
     *
     * #param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $helper = $this->_connection;
        $customerId = $observer->getEvent()->getSeller()->getId();
        if ($customerId) {
            $mpSellerId = $this->_sellerModel->getCollection()
                ->addFieldToFilter('seller_id', $customerId)
                ->getFirstItem()
                ->getId();
            $sellerCollection = $this->_sellersMapping->getCollection()
                ->addFieldToFilter('mp_seller_id', $mpSellerId);
            foreach ($sellerCollection as $seller) {
                $odooId = (int)$seller->getOdooId();
                $data = ['state'=> 'denied'];
                $response = $helper->callOdooMethod("res.partner", "write", [$odooId, $data], true);
                if ($response && $response[0]) {
                    $message = "The Marketplace Seller have been disapproved at Odoo.";
                    $this->_messageManager->addSuccess(__($message));
                } else {
                    $message = 'The Marketplace Seller have not been disapproved at Odoo for more details check Synchronization Logs !!';
                    $this->_messageManager->addError(__($message));
                }
            }
        }
    }
}
