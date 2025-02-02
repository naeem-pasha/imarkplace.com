<?php
/**
 * Webkul Odoomarketplaceconnect Product Update Controller
 * @category  Webkul
 * @package   Webkul_Odoomarketplaceconnect
 * @author    Webkul
 * @copyright Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\Odoomarketplaceconnect\Controller\Adminhtml\Product;

class Update extends \Magento\Backend\App\Action
{
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Webkul\Odoomarketplaceconnect\Model\ResourceModel\Product $productModel,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
    ) {
        $this->_productModel = $productModel;
        $this->resultForwardFactory = $resultForwardFactory;
        parent::__construct($context);
    }
    /**
     * @return void
     */
    public function execute()
    {
        $count = 0;
        $fail = 0;
        $response = 0;
        $params = $this->getRequest()->getParams();
        $productId = $params['id'];
        $total = $params['total'];
        $counter = $params['counter'];
        $failure = $params['failure'];
        $success = $params['success'];
        if ($productId) {
            $response = $this->_productModel->updateSpecificProduct($productId);
        }
        if ($counter == $total) {
            if ($response['odoo_id'] > 0) {
                $success = ++$success;
            } else {
                $failure = ++$failure;
            }
            if ($failure > 0) {
                $this->messageManager
                    ->addError(__("Total of ".$failure.' Seller Product(s) have not been updated to Odoo for more details check Synchronization Logs !!'));
            }
            if ($success) {
                $this->messageManager
                    ->addSuccess(__("Total of ".$success." Seller Product(s) have been successfully Updated to Odoo."));
            }
        }
        $this->getResponse()->clearHeaders()->setHeader('content-type', 'application/json', true);
        $this->getResponse()->setBody(json_encode(['response'=>$response]));
    }
    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Webkul_Odoomarketplaceconnect::product_new');
    }
}
