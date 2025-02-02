<?php
/**
 * Webkul Odoomagentoconnect Category Update Controller
 *
 * @category  Webkul
 * @package   Webkul_Odoomagentoconnect
 * @author    Webkul
 * @copyright Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\Odoomagentoconnect\Controller\Adminhtml\Category;

/**
 * Webkul Odoomagentoconnect Category Update Controller class
 */
class Update extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Backend\Model\View\Result\Forward
     */
    protected $_resultForwardFactory;

    /**
     * @param \Magento\Backend\App\Action\Context               $context
     * @param \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Webkul\Odoomagentoconnect\Model\Category $categoryMapping,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Webkul\Odoomagentoconnect\Model\ResourceModel\Category $categoryModel
    ) {
        $this->_categoryModel = $categoryModel;
        $this->_categoryMapping = $categoryMapping;
        $this->_resultForwardFactory = $resultForwardFactory;
        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Webkul_Odoomagentoconnect::category_save');
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
        $categoryId = $params['id'];
        $total = $params['total'];
        $counter = $params['counter'];
        $failure = $params['failure'];
        $success = $params['success'];
        if ($categoryId) {
            $categoryObj = $this->_categoryMapping->load($categoryId);
            $magentoId = $categoryObj->getMagentoId();
            $odooId = (int)$categoryObj->getOdooId();
            $response = $this->_categoryModel->createSpecificCategory($magentoId, 'write', $odooId, $categoryObj);
        }
        if ($counter == $total) {
            $response['odoo_id'] ? $success++ : $failure++;
            if ($failure) {
                $message = " Product Category(s) have not been Updated to Odoo for more details check Synchronization Logs !!";
                $this->messageManager->addError(__("Total of ".$failure.$message));
            }
            if ($success) {
                $message = " Product Category(s) have been successfully Updated to Odoo.";
                $this->messageManager->addSuccess(__("Total of ".$success.$message));
            }
        }
        $this->getResponse()->clearHeaders()->setHeader('content-type', 'application/json', true);
        $this->getResponse()->setBody(json_encode(['response'=>$response]));
    }
}
