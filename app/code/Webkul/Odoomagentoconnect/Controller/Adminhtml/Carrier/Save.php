<?php
/**
 * Webkul Odoomagentoconnect Carrier Save Controller
 *
 * @category  Webkul
 * @package   Webkul_Odoomagentoconnect
 * @author    Webkul
 * @copyright Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\Odoomagentoconnect\Controller\Adminhtml\Carrier;

use Magento\Framework\Exception\AuthenticationException;

/**
 * Webkul Odoomagentoconnect Carrier Save Controller class
 */
class Save extends \Webkul\Odoomagentoconnect\Controller\Adminhtml\Carrier
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
            
            $carrierId = (int)$this->getRequest()->getParam('entity_id');
            $carriermodel = $this->_carrierMapping;
            if ($carrierId) {
                $carriermodel->load($carrierId);
                $carriermodel->setId($carriermodel);
                $data['id']=$carrierId;
            }
            
            $collection = $carriermodel->getCollection()
                ->addFieldToFilter(['carrier_code', 'odoo_id'], 
                [
                    ['eq' => $data['carrier_code']],
                    ['eq' => $data['odoo_id']]
                ]);
            
            if (count($collection)) {
                $this->messageManager->addError(__("Selected carrier(s) mapping already exists!"));
            } else {
                $this->messageManager->addSuccess(__('The carrier mapping has been saved.'));
                $shippingTitle = $this->_scopeConfig->getValue('carriers/'.$data['carrier_code'].'/title');
                $data['carrier_name'] = $shippingTitle;
                $data['created_by'] = 'Manual Mapping';
                $carriermodel->setData($data)->save();
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
        return $this->_authorization->isAllowed('Webkul_Odoomagentoconnect::carrier_save');
    }

}
