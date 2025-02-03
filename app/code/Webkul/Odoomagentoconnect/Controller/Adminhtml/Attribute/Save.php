<?php
/**
 * Webkul Odoomagentoconnect Attribute Save Controller
 *
 * @category  Webkul
 * @package   Webkul_Odoomagentoconnect
 * @author    Webkul
 * @copyright Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\Odoomagentoconnect\Controller\Adminhtml\Attribute;

class Save extends \Webkul\Odoomagentoconnect\Controller\Adminhtml\Attribute
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
            
            $attributeId = (int)$this->getRequest()->getParam('entity_id');
            $attributemodel = $this->_attributeMapping;
            if ($attributeId) {
                $attributemodel->load($attributeId);
                $attributemodel->setId($attributemodel);
                $data['id']=$attributeId;
            }

            $mapCollection = $attributemodel->getCollection()
                ->addFieldToFilter(['magento_id', 'odoo_id'], 
                [
                    ['eq' => $data['magento_id']],
                    ['eq' => $data['odoo_id']]
                ]);

            if (count($mapCollection)) {
                $this->messageManager->addError(__("Selected product attribute(s) mapping already exists!"));
            } else {
                $this->messageManager->addSuccess(__('The attribute mapping has been saved.'));
                $collection = $this->_catalogModel->getResource()
                ->getAttribute($data['magento_id']);
                $code = $collection->getAttributeCode();
                $data['created_by'] = 'Manual Mapping';
                $data['name'] = $code;
                $attributemodel->setData($data)->save();
                $this->_mapOnErp($data['magento_id'], $data['odoo_id'], $code);
            }
            
            $this->_getSession()->setUserData(false);
            $this->_redirect('odoomagentoconnect/*/');
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            if ($e->getMessage()) $this->messageManager->addError($e->getMessage());
            $this->_redirect('odoomagentoconnect/*/');
        }
    }

    protected function _mapOnErp($magentoId, $odooId, $code)
    {
        $attrMapArray = [
            'name'=>$odooId,
            'odoo_id'=>$odooId,
            'ecomm_id'=>$magentoId,
            'ecomm_attribute_code'=>$code,
            'created_by'=>'Manual Mapping',
        ];
        $resp = $this->_connection->callOdooMethod('connector.attribute.mapping', 'create', [$attrMapArray]);
    }
    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Webkul_Odoomagentoconnect::attribute_save');
    }

}
