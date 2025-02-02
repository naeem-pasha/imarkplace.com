<?php

namespace Webkul\Odoomarketplaceconnect\Controller\Adminhtml\Transaction;

/**
* Webkul Odoomarketplaceconnect Save Controller
* @category  Webkul
* @package   Webkul_Odoomarketplaceconnect
* @author    Webkul
* @copyright Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
* @license   https://store.webkul.com/license.html
*/

use Magento\Framework\Exception\AuthenticationException;
use Webkul\Odoomagentoconnect\Helper\Connection;
use xmlrpc_client;
use xmlrpcval;
use xmlrpcmsg;

class Save extends \Webkul\Odoomarketplaceconnect\Controller\Adminhtml\Transaction
{
    /**
     * @return void
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {

        $userId = (int)$this->getRequest()->getParam('user_id');
        $data = $this->getRequest()->getPostValue();
        if (!$data) {
            $this->_redirect('transaction/*/');
            return;
        }

        /** Before updating admin user data, ensure that password of current admin user is entered and is correct */
        try {
            $this->messageManager->addSuccess(__('You saved the store mapping.'));
            
            $storeMappingId = (int)$this->getRequest()->getParam('entity_id');
            $storeMappingModel = $this->_objectManager
                                        ->create('Webkul\Odoomarketplaceconnect\Model\Transaction');
            if ($storeMappingId) {
                $storeMappingModel->load($storeMappingId);
                $storeMappingModel->setId($storeMappingModel);
                $data['id']=$storeMappingId;
            }
            if ($storeMappingId && $storeMappingModel->isObjectNew()) {
                $this->messageManager->addError(__('This store mapping no longer exists.'));
                $this->_redirect('transaction/*/');
                return;
            }
            $data['created_by'] = 'Manual Mapping';
            
            $storeMappingModel->setData($data);
            $storeMappingModel->save();
            $this->_mapOnErp($data['magento_store_id'], $data['odoo_lang']);
            
            $this->_getSession()->setUserData(false);
            $this->_redirect('transaction/*/');
        } catch (\Magento\Framework\Validator\Exception $e) {
            $messages = $e->getMessages();
            $this->messageManager->addMessages($messages);
            $this->redirectToEdit($data);
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            if ($e->getMessage()) {
                $this->messageManager->addError($e->getMessage());
            }
            $this->redirectToEdit($data);
        }
    }

    protected function _mapOnErp($magentoStoreId, $odooLang)
    {
        $helper = $this->_objectManager->create('\Webkul\Odoomagentoconnect\Helper\Connection');
        $helper->getSocketConnect();
        $userId = $helper->getSession()->getUserId();
        $errorMessage = $helper->getSession()->getErrorMessage();
        if ($userId > 0) {
            $context = $helper->getOdooContext();
            $client = $helper->getClientConnect();
            $mapArray = [
                                'name'=>new xmlrpcval($odooLang, "string"),
                                'mage_store_id'=>new xmlrpcval($magentoStoreId, "int"),
                                'instance_id'=>$context['instance_id']
                            ];
            $msg = new xmlrpcmsg('execute');
            $msg->addParam(new xmlrpcval($helper::$odooDb, "string"));
            $msg->addParam(new xmlrpcval($userId, "int"));
            $msg->addParam(new xmlrpcval($helper::$odooPwd, "string"));
            $msg->addParam(new xmlrpcval("language.mapping", "string"));
            $msg->addParam(new xmlrpcval("create", "string"));
            $msg->addParam(new xmlrpcval($mapArray, "struct"));
            $msg->addParam(new xmlrpcval($context, "struct"));
            $resp = $client->send($msg);
        }
    }


    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Webkul_Odoomarketplaceconnect::transaction_save');
    }

    /**
     * @param \Magento\User\Model\User $model
     * @param array $data
     * @return void
     */
    protected function redirectToEdit(array $data)
    {
        $this->_getSession()->setUserData($data);
        $data['entity_id']=isset($data['entity_id'])?$data['entity_id']:0;
        $arguments = $data['entity_id'] ? ['id' => $data['entity_id']]: [];
        $arguments = array_merge($arguments, ['_current' => true, 'active_tab' => '']);
        if ($data['entity_id']) {
            $this->_redirect('transaction/*/edit', $arguments);
        } else {
            $this->_redirect('transaction/*/index', $arguments);
        }
    }
}
