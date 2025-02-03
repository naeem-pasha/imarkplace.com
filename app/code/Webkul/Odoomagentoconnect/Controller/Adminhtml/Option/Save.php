<?php
/**
 * Webkul Odoomagentoconnect Option Save Controller
 *
 * @category  Webkul
 * @package   Webkul_Odoomagentoconnect
 * @author    Webkul
 * @copyright Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\Odoomagentoconnect\Controller\Adminhtml\Option;

class Save extends \Webkul\Odoomagentoconnect\Controller\Adminhtml\Option
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
            
            $optionId = (int)$this->getRequest()->getParam('entity_id');
            $optionmodel = $this->_optionMapping;
            if ($optionId) {
                $optionmodel->load($optionId);
                $optionmodel->setId($optionmodel);
                $data['id']=$optionId;
            }
            
            $collection = $optionmodel->getCollection()
                ->addFieldToFilter(['magento_id', 'odoo_id'], 
                [
                    ['eq' => $data['magento_id']],
                    ['eq' => $data['odoo_id']]
                ]);
            
            if (count($collection)) {
                $this->messageManager->addError(__("Selected attribute option(s) mapping already exists!"));
            } else {
                $this->messageManager->addSuccess(__('The option mapping has been saved.'));
                $optionValue = $this->_attrOptionCollectionFactory->create()
                    ->setPositionOrder('asc')
                    ->setIdFilter($data['magento_id'])
                    ->setStoreFilter()
                    ->load()
                    ->getFirstItem()
                    ->getValue();
                $data['name'] = $optionValue;
                $data['created_by'] = 'Manual Mapping';
                $optionmodel->setData($data)->save();
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
        $mapArray = [
            'name'=>$odooId,
            'odoo_id'=>$odooId,
            'ecomm_id'=>$magentoId,
            'created_by'=>'Manual Mapping',
        ];
        $resp = $this->_connection->callOdooMethod('connector.option.mapping', 'create', [$mapArray]);
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Webkul_Odoomagentoconnect::option_save');
    }
    
}
