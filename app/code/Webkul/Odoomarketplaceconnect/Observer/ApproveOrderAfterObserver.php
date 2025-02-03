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
 * Webkul Odoomarketplaceconnect ApproveOrderAfterObserver Observer.
 */
class ApproveOrderAfterObserver implements ObserverInterface
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
        \Webkul\Odoomarketplaceconnect\Model\Order $orderMapping,
        \Webkul\Odoomagentoconnect\Helper\Connection $connection,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->_orderMapping = $orderMapping;
        $this->_scopeConfig = $scopeConfig;
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
        $orderObj = $observer->getEvent()->getOrder();
        if ($orderObj) {
            $failure = 0;
            $success = 0;
            $mpOrderId = $orderObj->getId();
            $collection = $this->_orderMapping->getCollection()->addFieldToFilter('magento_id', $mpOrderId);
            foreach ($collection as $value) {
                $odooId = (int)$value->getOdooLineId();
                $resp = $helper->callOdooMethod("sale.order.line", "write", [$odooId, ['marketplace_state'=> 'approved']]);
                if ($resp && $resp[0]) {
                    $success = $success + 1;
                } else {
                    $failure = $failure + 1;
                }
            }
            if ($success) {
                $message = " Marketplace Order(s) have been successfully approved at Odoo.";
                $this->_messageManager->addSuccess(__("Total of ".$success.$message));
            }
            if ($failure) {
                $message = " Marketplace Order(s) have not been approved at Odoo for more details check Synchronization Logs !!";
                $this->_messageManager->addError(__("Total of ".$failure.$message));
            }
        }
    }
}
