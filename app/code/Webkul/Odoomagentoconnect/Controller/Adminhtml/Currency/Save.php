<?php
/**
 * Webkul Odoomagentoconnect Currency Save Controller
 *
 * @category  Webkul
 * @package   Webkul_Odoomagentoconnect
 * @author    Webkul
 * @copyright Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\Odoomagentoconnect\Controller\Adminhtml\Currency;

use Magento\Framework\Exception\AuthenticationException;

/**
 * Webkul Odoomagentoconnect Currency Save Controller class
 */
class Save extends \Webkul\Odoomagentoconnect\Controller\Adminhtml\Currency
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
            
            $currencyId = (int)$this->getRequest()->getParam('entity_id');
            $currencymodel = $this->_currencyMapping;
            if ($currencyId) {
                $currencymodel->load($currencyId);
                $currencymodel->setId($currencymodel);
                $data['id']=$currencyId;
            }

            $collection = $currencymodel->getCollection()
                ->addFieldToFilter(['magento_id', 'odoo_id'], 
                [
                    ['eq' => $data['magento_id']],
                    ['eq' => $data['odoo_id']]
                ]);
            
            if (count($collection)) {
                $this->messageManager->addError(__("Selected currency mapping already exists!"));
            } else {
                $this->messageManager->addSuccess(__('The currency mapping has been saved.'));
                $data['created_by'] = 'Manual Mapping';
                $currencymodel->setData($data)->save();
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
        return $this->_authorization->isAllowed('Webkul_Odoomagentoconnect::currency_save');
    }

}
