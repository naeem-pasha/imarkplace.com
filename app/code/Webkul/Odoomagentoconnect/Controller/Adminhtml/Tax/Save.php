<?php
/**
 * Webkul Odoomagentoconnect Tax Save Controller
 *
 * @category  Webkul
 * @package   Webkul_Odoomagentoconnect
 * @author    Webkul
 * @copyright Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\Odoomagentoconnect\Controller\Adminhtml\Tax;

use Magento\Framework\Exception\AuthenticationException;

/**
 * Webkul Odoomagentoconnect Tax Save Controller class
 */
class Save extends \Webkul\Odoomagentoconnect\Controller\Adminhtml\Tax
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
        if (isset($data['magento_id'])) {
            $taxColl = $this->_rateModel->load($data['magento_id']);
            $taxData = $taxColl->getData();
            $data['code'] = $taxData['code'];
        }
        if (!$data) {
            $this->_redirect('odoomagentoconnect/*/');
            return;
        }

        /**
 * Before updating admin user data, ensure that password of current admin user is entered and is correct
*/
        try {
            
            $taxId = (int)$this->getRequest()->getParam('entity_id');
            $taxmodel = $this->_taxMapping;
            if ($taxId) {
                $taxmodel->load($taxId);
                $taxmodel->setId($taxmodel);
                $data['id']=$taxId;
            }
            
            $collection = $taxmodel->getCollection()
                ->addFieldToFilter(['magento_id', 'odoo_id'], 
                [
                    ['eq' => $data['magento_id']],
                    ['eq' => $data['odoo_id']]
                ]);

            if (count($collection)) {
                $this->messageManager->addError(__("Selected tax(s) mapping already exists!"));
            } else {
                $this->messageManager->addSuccess(__('The tax mapping has been saved.'));
                $data['created_by'] = 'Manual Mapping';
                $taxmodel->setData($data)->save();
            }
            
            $this->_getSession()->setUserData(false);
            $this->_redirect('odoomagentoconnect/*/');
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            if ($e->getMessage()) $this->messageManager->addError($e->getMessage());
            $this->_redirect('odoomagentoconnect/*/');
        }
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Webkul_Odoomagentoconnect::tax_save');
    }

}
