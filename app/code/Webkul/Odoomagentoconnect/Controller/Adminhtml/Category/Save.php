<?php
/**
 * Webkul Odoomagentoconnect Category Save Controller
 *
 * @category  Webkul
 * @package   Webkul_Odoomagentoconnect
 * @author    Webkul
 * @copyright Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\Odoomagentoconnect\Controller\Adminhtml\Category;

/**
 * Webkul Odoomagentoconnect Category Save Controller class
 */
class Save extends \Webkul\Odoomagentoconnect\Controller\Adminhtml\Category
{
    /**
     * @return                                       void
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        if (!$data) {
            $this->_redirect('odoomagentoconnect/*/');
            return;
        }

        /**
        * Before updating admin user data, ensure that password of current admin user is entered and is correct
        */
        try {       
            $categoryId = (int)$this->getRequest()->getParam('entity_id');
            $categoryModel = $this->_categoryMapping;
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
                $this->messageManager->addError(__("Selected product category(s) mapping already exists!"));
            } else {
                $this->messageManager->addSuccess(__('The category mapping has been saved.'));
                $data['created_by'] = 'Manual Mapping';
                $categoryModel->setData($data)->save();
                $this->_mapOnErp($data['magento_id'], $data['odoo_id']);
            }

            $this->_getSession()->setUserData(false);
            $this->_redirect('odoomagentoconnect/*/');
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            if ($e->getMessage()) $this->messageManager->addError($e->getMessage());
            $this->_redirect('odoomagentoconnect/*/');
        }
    }

    /**
     * Map category(s) to Odoo
     *
     * @param int $magentoId
     * @param int $odooId
     * 
     * @return null
     */
    protected function _mapOnErp($magentoId, $odooId)
    {
        $helper = $this->_connection;
        $context = $helper->getOdooContext();
        $data = [
            'name'=>$odooId,
            'odoo_id'=>$odooId,
            'ecomm_id'=>$magentoId,
            'instance_id'=>$context['instance_id'],
            'created_by'=>'Manual Mapping',
        ];
        $resp = $helper->callOdooMethod('connector.category.mapping', 'create', [$data]);
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Webkul_Odoomagentoconnect::category_save');
    }
}
