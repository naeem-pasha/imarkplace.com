<?php
/**
 * Webkul Odoomarketplaceconnect Save Controller
 * @category  Webkul
 * @package   Webkul_Odoomarketplaceconnect
 * @author    Webkul
 * @copyright Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\Odoomarketplaceconnect\Controller\Adminhtml\Category;

class Save extends \Webkul\Odoomarketplaceconnect\Controller\Adminhtml\Category
{
    /**
     * @return void
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        if (!$data) {
            $this->_redirect('odoomarketplaceconnect/*/');
            return;
        }

        /** Before updating admin user data, ensure that password of current admin user is entered and is correct */
        try {   
            $categoryId = (int)$this->getRequest()->getParam('entity_id');
            $categoryModel = $this->_mpCategoryMapping;
            if ($categoryId) {
                $categoryModel->load($categoryId);
                $categoryModel->setId($categoryModel);
                $data['id']=$categoryId;
            }

            $collection = $categoryModel->getCollection()
                ->addFieldToFilter(['magento_id', 'odoo_id'], 
                [
                    ['eq' => $data['magento_id']],
                    ['eq' => $data['odoo_id']]
                ]);

            if (count($collection)) {
                $this->messageManager->addError(__("Selected Website Category's Mapping already exists!!"));
            } else {
                $collection = $this->_categoryMapping->getCollection()->addFieldToFilter('magento_id', $data['magento_id']);
                if (!count($collection)) {
                    $this->_categoryModel->createSpecificCategory($data['magento_id'], 'create');
                }
                $this->messageManager->addSuccess(__("The Website Category Mapping has been saved.")); 
                $data['created_by'] = 'Manual Mapping';
                $data['need_sync'] = 'no';
                $categoryModel->setData($data)->save();
                $this->_mapOnErp((int)$data['magento_id'], (int)$data['odoo_id']);
            }

            $this->_getSession()->setUserData(false);
            $this->_redirect('odoomarketplaceconnect/*/');
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            if ($e->getMessage()) {
                $this->messageManager->addError($e->getMessage());
            }
            $this->_redirect('odoomarketplaceconnect/*/');
        }
    }

    protected function _mapOnErp($magentoId, $odooId)
    {
        $helper = $this->_connection;
        $data = [
                        'website_category_id'=>$odooId,
                        'website_category_name'=>$odooId,
                        'ecomm_id'=>$magentoId
                        ];
        $helper->callOdooMethod('connector.category.mapping', 'updateCategoryMapping', [$data], true);
        return;
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Webkul_Odoomarketplaceconnect::category_save');
    }
}
