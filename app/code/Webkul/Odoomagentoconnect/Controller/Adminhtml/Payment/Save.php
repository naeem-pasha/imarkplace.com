<?php
/**
 * Webkul Odoomagentoconnect Payment Save Controller
 *
 * @category  Webkul
 * @package   Webkul_Odoomagentoconnect
 * @author    Webkul
 * @copyright Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\Odoomagentoconnect\Controller\Adminhtml\Payment;

use Magento\Framework\Exception\AuthenticationException;

/**
 * Webkul Odoomagentoconnect Payment Save Controller class
 */
class Save extends \Webkul\Odoomagentoconnect\Controller\Adminhtml\Payment
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
            
            
            $paymentId = (int)$this->getRequest()->getParam('entity_id');
            $paymentmodel = $this->_paymentMapping;
            if ($paymentId) {
                $paymentmodel->load($paymentId);
                $paymentmodel->setId($paymentmodel);
                $data['id']=$paymentId;
            }

            $collection = $paymentmodel->getCollection()
                ->addFieldToFilter(['magento_id', 'odoo_id'], 
                [
                    ['eq' => $data['magento_id']],
                    ['eq' => $data['odoo_id']]
                ]);
            
            if (count($collection)) {
                $this->messageManager->addError(__("Selected payment(s) mapping already exists!"));
            } else {
                $this->messageManager->addSuccess(__('The payment mapping has been saved.'));
                $data['created_by'] = 'Manual Mapping';
                $paymentmodel->setData($data)->save();
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
        return $this->_authorization->isAllowed('Webkul_Odoomagentoconnect::payment_save');
    }

}
