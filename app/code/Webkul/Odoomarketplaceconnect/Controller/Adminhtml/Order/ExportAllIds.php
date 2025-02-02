<?php
/**
 * Webkul Odoomarketplaceconnect Order ExportAllIds Controller
 * @category  Webkul
 * @package   Webkul_Odoomarketplaceconnect
 * @author    Webkul
 * @copyright Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\Odoomarketplaceconnect\Controller\Adminhtml\Order;

class ExportAllIds extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Backend\Model\View\Result\Forward
     */
    protected $_resultForwardFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Webkul\Odoomagentoconnect\Helper\Connection $connection,
        \Webkul\Odoomarketplaceconnect\Model\Order $mpOrderMapping,
        \Webkul\Marketplace\Model\Saleslist $salesList,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
    ) {
        $this->_salesList = $salesList;
        $this->_connection = $connection;
        $this->_mpOrderMapping = $mpOrderMapping;
        $this->_resultForwardFactory = $resultForwardFactory;
        parent::__construct($context);
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Webkul_Odoomarketplaceconnect::order_save');
    }

    /**
     * Forward to edit
     *
     * @return \Magento\Backend\Model\View\Result\Forward
     */
    public function execute()
    {
        $exportIds = [];
        $helper = $this->_connection;
        $helper->getSocketConnect();
        $userId = $helper->getSession()->getUserId();
        if ($userId) {
            $mapOrders = [];
            $orderCollection = $this->_mpOrderMapping->getCollection();
            foreach ($orderCollection as $order) {
                array_push($mapOrders, $order->getMpSaleId());
            }

            $collection = $this->_salesList->getCollection();
            $exportOrders = $mapOrders ? $collection->addFieldToFilter('entity_id', ['nin' => $mapOrders]) : $collection;
            $exportIds = $exportOrders->getAllIds();

            if (count($exportOrders) == 0 && !$mapOrders) {
                $this->messageManager->addSuccess(__("No Marketplace Order(s) exists at Magento."));
            } else {
                if (count($exportIds) == 0) {
                    $this->messageManager->addSuccess(__("All Marketplace Orders have already been Exported to Odoo."));
                }
            }
        } else {
            $errorMessage = $helper->getSession()->getErrorMessage();
            $this->messageManager->addError(
                __(
                    "The Marketplace Order(s) have not been Exported to Odoo !! Reason : ".$errorMessage
                )
            );
        }
        $this->getResponse()->clearHeaders()->setHeader('content-type', 'application/json', true);
        $this->getResponse()->setBody(json_encode($exportIds));
    }
}
