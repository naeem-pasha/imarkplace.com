<?php
/**
 * Webkul Odoomarketplaceconnect Order Export Controller
 * @category  Webkul
 * @package   Webkul_Odoomarketplaceconnect
 * @author    Webkul
 * @copyright Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\Odoomarketplaceconnect\Controller\Adminhtml\Order;

class Export extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Backend\Model\View\Result\Forward
     */
    protected $_resultForwardFactory;
    protected $_mpOrderModel;


    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Webkul\Odoomarketplaceconnect\Model\ResourceModel\Order $mpOrderModel,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
    ) {
        $this->_mpOrderModel = $mpOrderModel;
        $this->_resultForwardFactory = $resultForwardFactory;
        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
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
        $response = 0;
        $params = $this->getRequest()->getParams();
        $orderId = $params['id'];
        $total = $params['total'];
        $counter = $params['counter'];
        $failure = $params['failure'];
        $success = $params['success'];

        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/m2odoo.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info("Params : ".print_r($params,true));

        if ($orderId) {
            $response = $this->_mpOrderModel->exportSpecificOrder($orderId);
        }

        if ($counter == $total) {
            if ($response['odoo_id'] > 0) {
                $success = ++$success;
            } else {
                $failure = ++$failure;
            }
           
            if ($failure > 0) {
                $message = ' Marketplace Order(s) have not been Exported to Odoo for more details check Synchronization Logs !!';
                $this->messageManager->addError(__("Total of ".$failure.$message));
            }
            if ($success) {
                $message = " Marketplace Order(s) have been successfully Exported to Odoo.";
                $this->messageManager->addSuccess(__("Total of ".$success.$message));
            }
        }
        $this->getResponse()->clearHeaders()->setHeader('content-type', 'application/json', true);
        $this->getResponse()->setBody(json_encode(['response'=>$response]));
    }
}
