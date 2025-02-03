<?php
/**
 * Webkul Odoomarketplaceconnect Sellers ExportAllIds Controller
 * @category  Webkul
 * @package   Webkul_Odoomarketplaceconnect
 * @author    Webkul
 * @copyright Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\Odoomarketplaceconnect\Controller\Adminhtml\Sellers;

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
        \Webkul\Marketplace\Model\Seller $sellerModel,
        \Magento\Backend\App\Action\Context $context,
        \Webkul\Odoomagentoconnect\Helper\Connection $connection,
        \Webkul\Odoomarketplaceconnect\Model\Sellers $sellerMapping,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
    ) {

        $this->_sellerMapping = $sellerMapping;
        $this->_sellerModel = $sellerModel;
        $this->_connection = $connection;
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
        $exportIds = [];
        $helper = $this->_connection;
        $helper->getSocketConnect();
        $userId = $helper->getSession()->getUserId();
        if ($userId) {
            $mapSellers = [];
            $sellerMappingCollection = $this->_sellerMapping->getCollection();
            foreach ($sellerMappingCollection as $seller) {
                array_push($mapSellers, $seller['mp_seller_id']);
            }
            
            $collection = $this->_sellerModel->getCollection();
            $exportSellers = $mapSellers ? $collection->addFieldToFilter('entity_id', ['nin' => $mapSellers]) : $collection;
            $exportIds = $exportSellers->getAllIds();

            if (count($exportSellers) == 0 && !$mapSellers) {
                $this->messageManager->addSuccess(__("No Marketplace Seller(s) exists at Magento."));
            } else {
                if (count($exportIds) == 0) {
                    $this->messageManager->addSuccess(__("All Marketplace Sellers have already been Exported to Odoo."));
                }
            }
        } else {
            $errorMessage = $helper->getSession()->getErrorMessage();
            $this->messageManager->addError(
                __(
                    "The Marketplace Seller(s) have not been Exported to Odoo !! Reason : ".$errorMessage
                )
            );
        }
        $this->getResponse()->clearHeaders()->setHeader('content-type', 'application/json', true);
        $this->getResponse()->setBody(json_encode($exportIds));
    }
}
