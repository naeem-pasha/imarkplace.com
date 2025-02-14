<?php
/**
 * Webkul Odoomagentoconnect Product Edit Controller
 *
 * @category  Webkul
 * @package   Webkul_Odoomagentoconnect
 * @author    Webkul
 * @copyright Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\Odoomagentoconnect\Controller\Adminhtml\Product;

use Magento\Framework\Locale\Resolver;

/**
 * Webkul Odoomagentoconnect Product Edit Controller class
 */
class Edit extends \Webkul\Odoomagentoconnect\Controller\Adminhtml\Product
{
    /**
     * @return void
     */
    public function execute()
    {
        $productId = (int)$this->getRequest()->getParam('id');
        $productmodel = $this->_productFactory->create();
        if ($productId) {
            $productmodel->load($productId);
            if (!$productmodel->getEntityId()) {
                $this->messageManager->addError(__('This Product no longer exists.'));
                $this->_redirect('odoomagentoconnect/*/');
                return;
            }
        }
   
        $userId = $productmodel->getId();
        /**
 * @var \Magento\User\Model\User $model
*/
        $model = $this->_userFactory->create();

        $data = $this->_session->getUserData(true);

        if (!empty($data)) {
            $model->setData($data);
        }

        $this->_coreRegistry->register('product_user', $model);
        $this->_coreRegistry->register('odoomagentoconnect_product', $productmodel);

        if (isset($userId)) {
            $breadcrumb = __('Edit Product');
        } else {
            $breadcrumb = __('New Product');
        }
        $this->_initAction()->_addBreadcrumb($breadcrumb, $breadcrumb);
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Users'));
        $this->_view->getPage()->getConfig()
            ->getTitle()
            ->prepend($model->getId() ? $model->getName() : __('Product Manual Mapping'));
        $this->_view->renderLayout();
    }
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Webkul_Odoomagentoconnect::product_view');
    }
}
