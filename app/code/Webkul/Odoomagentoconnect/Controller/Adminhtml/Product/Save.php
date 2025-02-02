<?php
/**
 * Webkul Odoomagentoconnect Product Save Controller
 *
 * @category  Webkul
 * @package   Webkul_Odoomagentoconnect
 * @author    Webkul
 * @copyright Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\Odoomagentoconnect\Controller\Adminhtml\Product;

class Save extends \Webkul\Odoomagentoconnect\Controller\Adminhtml\Product
{
    /**
     * @return                                       void
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {

        $userId = (int)$this->getRequest()->getParam('user_id');
        $data = $this->getRequest()->getPostValue();
        if (!$data) {
            $this->_redirect('odoomagentoconnect/*/');
            return;
        }

        /**
 * Before updating admin user data, ensure that password of current admin user is entered and is correct
*/
        try {
            
            $productId = (int)$this->getRequest()->getParam('entity_id');
            $productmodel = $this->_productMapping;
            if ($productId) {
                $productmodel->load($productId);
                $productmodel->setId($productmodel);
                $data['id']=$productId;
            }

            $collection = $productmodel->getCollection()
                ->addFieldToFilter(['magento_id', 'odoo_id'], 
                [
                    ['eq' => $data['magento_id']],
                    ['eq' => $data['odoo_id']]
                ]);
            
            if (count($collection)) {
                $this->messageManager->addError(__("Selected product(s) mapping already exists!"));
            } else {
                $this->messageManager->addSuccess(__('The product mapping has been saved.'));
                $data['created_by'] = 'Manual Mapping';
                $productmodel->setData($data)->save();
                $this->_mapOnErp($data['magento_id'], $data['odoo_id']);
            }

            $this->_getSession()->setUserData(false);
            $this->_redirect('odoomagentoconnect/*/');
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            if ($e->getMessage()) $this->messageManager->addError($e->getMessage());
            $this->_redirect('odoomagentoconnect/*/');
        }
    }


    protected function _mapOnErp($magentoId, $odooId)
    {
        $helper = $this->_connection;
        $product = $this->_catalogModel->load($magentoId);
        $stockId = $product->getExtensionAttributes()->getStockItem()->getItemId();
        $mapArray = [
            'name'=>$odooId,
            'odoo_id'=>$odooId,
            'ecomm_id'=>$magentoId,
            'magento_stock_id'=>$stockId,
            'created_by'=>'Manual Mapping',
        ];
        $helper->callOdooMethod('connector.product.mapping', 'create', [$mapArray]);
        $templateMapArray = [
            'odoo_id'=>$odooId,
            'ecomm_id'=>$magentoId,
        ];
        $helper->callOdooMethod('connector.template.mapping', 'create_template_mapping', [$templateMapArray]);
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Webkul_Odoomagentoconnect::product_save');
    }

}
