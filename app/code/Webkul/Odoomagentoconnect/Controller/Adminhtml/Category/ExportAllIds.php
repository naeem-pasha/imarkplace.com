<?php
/**
 * Webkul Odoomagentoconnect Category ExportAllIds Controller
 *
 * @category  Webkul
 * @package   Webkul_Odoomagentoconnect
 * @author    Webkul
 * @copyright Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\Odoomagentoconnect\Controller\Adminhtml\Category;

/**
 * Webkul Odoomagentoconnect Category ExportAllIds Controller class
 */
class ExportAllIds extends \Magento\Backend\App\Action
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
        \Magento\Catalog\Model\Category $categoryModel,
        \Webkul\Odoomagentoconnect\Helper\Connection $connection,
        \Webkul\Odoomagentoconnect\Model\Category $categoryMapping,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
    ) {
    
        $this->_connection = $connection;
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
        $exportIds = [];
        $helper = $this->_connection;
        $helper->getSocketConnect();
        $userId = $helper->getSession()->getUserId();
        if ($userId) {
            $mapCategories = [];
            $mappingCollection = $this->_categoryMapping->getCollection();
            foreach ($mappingCollection as $map) {
                array_push($mapCategories, $map['magento_id']);
            }

            $collection = $this->_categoryModel->getCollection()->addAttributeToFilter('level', ['neq' => 0]);
            $exportCategories = $mapCategories ? $collection->addAttributeToFilter('entity_id', ['nin' => $mapCategories]) : $collection;
            $exportIds = $exportCategories->getAllIds();
            
            if (empty($collection) && !$mapCategories) {
                $this->messageManager->addSuccess(__("No Product Category(s) exists at Magento."));
            } else {
                if (empty($exportIds)) {
                    $this->messageManager->addSuccess(__("All Product Categories have already been exported to Odoo."));
                }
            }
        } else {
            $errorMessage = $helper->getSession()->getErrorMessage();
            $this->messageManager->addError(
                __(
                    "The Product Category(s) have not been Exported to Odoo !! Reason : ".$errorMessage
                )
            );
        }
        $this->getResponse()->clearHeaders()->setHeader('content-type', 'application/json', true);
        $this->getResponse()->setBody(json_encode($exportIds));
    }
}
