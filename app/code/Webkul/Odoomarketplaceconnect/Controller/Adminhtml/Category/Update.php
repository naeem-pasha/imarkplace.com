<?php
/**
 * Webkul Odoomarketplaceconnect Category Update Controller
 * @category  Webkul
 * @package   Webkul_Odoomarketplaceconnect
 * @author    Webkul
 * @copyright Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\Odoomarketplaceconnect\Controller\Adminhtml\Category;

class Update extends \Magento\Backend\App\Action
{

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Webkul\Odoomarketplaceconnect\Model\ResourceModel\Category $categorysModel,
        \Webkul\Odoomarketplaceconnect\Model\Category $categorysMapping,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
    ) {
    
        $this->_categorysMapping = $categorysMapping;
        $this->_categorysModel = $categorysModel;
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
        $categoryId = $params['id'];
        $total = $params['total'];
        $counter = $params['counter'];
        $failure = $params['failure'];
        $success = $params['success'];
        if ($categoryId) {
            $response = $this->_categorysModel->updateSpecificCategory($categoryId);
        }
        if ($counter == $total) {
            if ($response['odoo_id'] > 0) {
                $success = ++$success;
            } else {
                $failure = ++$failure;
            }
            if ($failure > 0) {
                $this->messageManager
                ->addError(__("Total of ".$failure.' Website Category(s) have not been updated to Odoo for more details check Synchronization Logs !!'));
            }
            if ($success) {
                $this->messageManager
                    ->addSuccess(__("Total of ".$success." Website Category(s) have been successfully Updated to Odoo."));
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
        return $this->_authorization->isAllowed('Webkul_Odoomarketplaceconnect::categorys_new');
    }
}
