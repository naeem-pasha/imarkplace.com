<?php
/**
 * Webkul Software.
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
 * Webkul Odoomarketplaceconnect PaySellerAfterObserver Observer.
 */
class PaySellerAfterObserver implements ObserverInterface
{
    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    private $_messageManager;

    /**
     * @param \Magento\Framework\Message\ManagerInterface $messageManager,
     * @param CollectionFactory                           $collectionFactory
     */
    public function __construct(
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Webkul\Odoomagentoconnect\Helper\Connection $connection,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Webkul\Odoomarketplaceconnect\Model\ResourceModel\Transaction $transactionModel
    ) {
        $this->_scopeConfig = $scopeConfig;
        $this->_connection = $connection;
        $this->_transactionModel = $transactionModel;
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
        $autoSync = $this->_scopeConfig->getValue('odoomagentoconnect/odoomarketplaceconnect/auto_transaction');
        if ($autoSync) {
            $data = $observer->getEvent()->getData();
            if (array_key_exists("mp_trans_row_id", $data[0])) {
                $transId = $data[0]['mp_trans_row_id'];
                if ($transId) {
                    $response = $this->_transactionModel->exportSpecificTransaction($transId);
                    if ($response['odoo_id'] > 0) {
                        $message = "Seller's Transaction has been successfully Exported at Odoo.";
                        $this->_messageManager->addSuccess(__($message));
                    } else {
                        $message = "Seller's Transaction has not been Exported at Odoo for more details check logs.";
                        $this->_messageManager->addError(__($message));
                    }
                }
            }
        }
    }
}
