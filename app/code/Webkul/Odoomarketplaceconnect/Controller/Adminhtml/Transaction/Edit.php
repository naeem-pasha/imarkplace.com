<?php

namespace Webkul\Odoomarketplaceconnect\Controller\Adminhtml\Transaction;

/**
 * Webkul Odoomarketplaceconnect Edit Controller
 * @category  Webkul
 * @package   Webkul_Odoomarketplaceconnect
 * @author    Webkul
 * @copyright Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

use Magento\Framework\Locale\Resolver;

class Edit extends \Webkul\Odoomarketplaceconnect\Controller\Adminhtml\Transaction
{
    /**
     * @return void
     */
    public function execute()
    {
        $transactionId=(int)$this->getRequest()->getParam('id');
        $transactionmodel=$this->_transactionFactory->create();
        if ($transactionId) {
            $transactionmodel->load($transactionId);
            if (!$transactionmodel->getEntityId()) {
                $this->messageManager->addError(__('This Store no longer exists.'));
                $this->_redirect('transaction/*/');
                return;
            }
        }
        $userId = $transactionmodel->getId();
        /** @var \Magento\User\Model\User $model */
        $model = $this->_userFactory->create();

        $data = $this->_session->getUserData(true);

        if (!empty($data)) {
            $model->setData($data);
        }

        $this->_coreRegistry->register('transaction_user', $model);
        $this->_coreRegistry->register('odoomarketplaceconnect_transaction', $transactionmodel);

        if (isset($userId)) {
            $breadcrumb = __('Edit Store');
        } else {
            $breadcrumb = __('New Store');
        }
        $this->_initAction()->_addBreadcrumb($breadcrumb, $breadcrumb);
        $this->_view
                ->getPage()
                ->getConfig()
                ->getTitle()
                ->prepend(__('Users'));
        $this->_view
                ->getPage()
                ->getConfig()
                ->getTitle()
                ->prepend($model->getId() ? $model->getName() : __('New Store'));
        $this->_view
                ->renderLayout();
    }
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Webkul_Odoomarketplaceconnect::transaction_view');
    }
}
