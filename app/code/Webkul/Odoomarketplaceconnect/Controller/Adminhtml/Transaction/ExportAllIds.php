<?php
/**
 * Webkul Odoomarketplaceconnect Transaction Edit Controller
 * @category  Webkul
 * @package   Webkul_Odoomarketplaceconnect
 * @author    Webkul
 * @copyright Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\Odoomarketplaceconnect\Controller\Adminhtml\Transaction;

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
        \Webkul\Odoomarketplaceconnect\Model\Transaction $transactionMapping,
        \Webkul\Marketplace\Model\Sellertransaction $transactionModel,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
    ) {
    
        $this->_transactionMapping = $transactionMapping;
        $this->_transactionModel = $transactionModel;
        $this->_connection = $connection;
        $this->_resultForwardFactory = $resultForwardFactory;
        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Webkul_Odoomarketplaceconnect::transaction_save');
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
            $mapTransaction = [];
            $transactionCollection = $this->_transactionMapping->getCollection()
                                         ->addFieldToSelect('magento_id')->getData();
            foreach ($transactionCollection as $value) {
                array_push($mapTransaction, $value['magento_id']);
            }
            if ($mapTransaction) {
                $collection = $this->_transactionModel->getCollection()
                                        ->addFieldToFilter('entity_id', ['nin' => $mapTransaction]);
            } else {
                $collection = $this->_transactionModel->getCollection();
            }
            $exportIds = $collection->getAllIds();
            
            if (count($collection) == 0) {
                $this->messageManager->addSuccess(__("No Transaction are exist at Magento."));
            } else {
                if (count($exportIds) == 0) {
                    $this->messageManager->addSuccess(__("All Magento Transaction are already exported at Odoo."));
                }
            }
        } else {
            $errorMessage = $helper->getSession()->getErrorMessage();
            $this->messageManager->addError(
                __(
                    "Transaction(s) have not been Exported at Odoo !! Reason : ".$errorMessage
                )
            );
        }
        $this->getResponse()->clearHeaders()->setHeader('content-type', 'application/json', true);
        $this->getResponse()->setBody(json_encode($exportIds));
    }
}
