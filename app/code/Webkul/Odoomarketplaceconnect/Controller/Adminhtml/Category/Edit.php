<?php
/**
 * Webkul Odoomarketplaceconnect Edit Controller
 * @category  Webkul
 * @package   Webkul_Odoomarketplaceconnect
 * @author    Webkul
 * @copyright Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\Odoomarketplaceconnect\Controller\Adminhtml\Category;

use Magento\Framework\Locale\Resolver;

class Edit extends \Webkul\Odoomarketplaceconnect\Controller\Adminhtml\Category
{
    /**
     * @return void
     */
    public function execute()
    {
        $categoryId=(int)$this->getRequest()->getParam('id');
        $categorymodel=$this->_categoryFactory->create();
        if ($categoryId) {
            $categorymodel->load($categoryId);
            if (!$categorymodel->getEntityId()) {
                $this->_redirect('category/*/');
                return;
            }
        }
        $userId = $categorymodel->getId();
        /** @var \Magento\User\Model\User $model */
        $model = $this->_userFactory->create();

        $data = $this->_session->getUserData(true);

        if (!empty($data)) {
            $model->setData($data);
        }

        $this->_coreRegistry->register('category_user', $model);
        $this->_coreRegistry->register('odoomarketplaceconnect_category', $categorymodel);

        if (isset($userId)) {
            $breadcrumb = __('Edit Store');
        } else {
            $breadcrumb = __('Map New Website Category');
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
                ->prepend($model->getId() ? $model->getName() : __('Map New Website Category'));
        $this->_view
                ->renderLayout();
    }
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Webkul_Odoomarketplaceconnect::category_view');
    }
}
