<?php
/**
 * Webkul Odoomarketplaceconnect Sellers Export Controller
 * @category  Webkul
 * @package   Webkul_Odoomarketplaceconnect
 * @author    Webkul
 * @copyright Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\Odoomarketplaceconnect\Controller\Adminhtml\Sellers;

class Export extends \Magento\Backend\App\Action
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
        \Webkul\Odoomarketplaceconnect\Model\ResourceModel\Sellers $sellersModel,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
    ) {

        $this->_sellersModel = $sellersModel;
        $this->_resultForwardFactory = $resultForwardFactory;
        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Webkul_Odoomarketplaceconnect::sellers_save');
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
        $total = $params['total'];
        $sellerId = $params['id'];
        $failure = $params['failure'];
        $counter = $params['counter'];
        $success = $params['success'];
        if ($sellerId) {
            $response = $this->_sellersModel->exportSpecificSeller($sellerId);
        }

        if ($counter == $total) {
            if ($response['odoo_id'] > 0) {
                $success = ++$success;
            } else {
                $failure = ++$failure;
            }
            if ($failure > 0) {
                $message = ' Marketplace Sellers(s) have not been Exported to Odoo for more details check Synchronization Logs !!';
                $this->messageManager->addError(__("Total of ".$failure.$message));
            }
            if ($success) {
                $message = " Marketplace Sellers(s) have been successfully Exported to Odoo.";
                $this->messageManager->addSuccess(__("Total of ".$success.$message));
            }
        }
        $this->getResponse()->clearHeaders()->setHeader('content-type', 'application/json', true);
        $this->getResponse()->setBody(json_encode(['response'=>$response]));
    }
}
