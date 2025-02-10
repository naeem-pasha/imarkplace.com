<?php
/**
 * Webkul Odoomarketplaceconnect Category ExportAllIds Controller
 * @category  Webkul
 * @package   Webkul_Odoomarketplaceconnect
 * @author    Webkul
 * @copyright Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\Odoomarketplaceconnect\Controller\Adminhtml\Category;

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
        \Webkul\Odoomarketplaceconnect\Model\Category $categoryMapping,
        \Webkul\Odoomarketplaceconnect\Model\ResourceModel\Category $categoryModel,
        \Magento\Catalog\Model\Category $catalogModel,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
    ) {
        $this->_categoryModel = $categoryModel;
        $this->_categoryMapping = $categoryMapping;
        $this->_catalogModel = $catalogModel;
        $this->_connection = $connection;
        $this->_resultForwardFactory = $resultForwardFactory;
        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Webkul_Odoomarketplaceconnect::category_save');
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
            $websiteEnable = $this->_categoryModel->checkWebsiteCategorySetting();
            if ($websiteEnable) {
                $mapCategories = [];
                $categoryMappingCollection = $this->_categoryMapping->getCollection();
                foreach ($categoryMappingCollection as $category) {
                    array_push($mapCategories, $category['magento_id']);
                }
                
                $collection = $this->_catalogModel->getCollection()->addAttributeToFilter('level', ['neq' => 0]);
                $exportCatgories = $mapCategories ? $collection->addFieldToFilter('entity_id', ['nin' => $mapCategories]) : $collection;
                $exportIds = $exportCatgories->getAllIds();
                
                if (count($exportCatgories) == 0 && !$mapCategories) {
                    $this->messageManager->addSuccess(__("No Website Category(s) exists at Magento."));
                } else {
                    if (count($exportIds) == 0) {
                        $this->messageManager->addSuccess(__("All Magento Website Categories have already been Exported to Odoo."));
                    }
                }
            } else {
                $this->messageManager->addError(__("The Website Category setting disabled at Odoo. Kindly enable related setting to sync Website Categories !!"));
            }
        } else {
            $errorMessage = $helper->getSession()->getErrorMessage();
            $this->messageManager->addError(
                __(
                    "The Magento Website Category(s) have not been Exported to Odoo !! Reason : ".$errorMessage
                )
            );
        }
        $this->getResponse()->clearHeaders()->setHeader('content-type', 'application/json', true);
        $this->getResponse()->setBody(json_encode($exportIds));
    }
}
