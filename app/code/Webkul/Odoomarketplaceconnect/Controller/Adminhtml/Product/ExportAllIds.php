<?php
/**
 * Webkul Odoomarketplaceconnect Product ExportAllIds Controller
 * @category  Webkul
 * @package   Webkul_Odoomarketplaceconnect
 * @author    Webkul
 * @copyright Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\Odoomarketplaceconnect\Controller\Adminhtml\Product;

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
        \Webkul\Marketplace\Model\Product $productModel,
        \Webkul\Odoomagentoconnect\Helper\Connection $connection,
        \Webkul\Odoomarketplaceconnect\Model\Product $productMapping,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
    ) {
    
        $this->_connection = $connection;
        $this->_productModel = $productModel;
        $this->_productMapping = $productMapping;
        $this->_resultForwardFactory = $resultForwardFactory;
        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Webkul_Odoomarketplaceconnect::product_save');
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
            $mapProducts = [];
            $productMappingCollection = $this->_productMapping->getCollection();
            foreach ($productMappingCollection as $product) {
                array_push($mapProducts, $product['mp_product_id']);
            }
            
            $collection = $this->_productModel->getCollection();
            $exportProducts = $mapProducts ? $collection->addFieldToFilter('entity_id', ['nin' => $mapProducts]) : $collection;
            foreach ($exportProducts as $mpProduct) {
                array_push($exportIds, $mpProduct->getEntityId());    
            }
    
            
            if (count($exportProducts) == 0 && !$mapProducts) {
                $this->messageManager->addSuccess(__("No Seller Product(s) exists at Magento."));
            } else {
                if (count($exportIds) == 0) {
                    $this->messageManager->addSuccess(__("All Seller Products have already been Exported to Odoo."));
                }
            }
        } else {
            $errorMessage = $helper->getSession()->getErrorMessage();
            $this->messageManager->addError(
                __(
                    "The Seller Products(s) have not been Exported to Odoo !! Reason : ".$errorMessage
                )
            );
        }
        $this->getResponse()->clearHeaders()->setHeader('content-type', 'application/json', true);
        $this->getResponse()->setBody(json_encode($exportIds));
    }
}
