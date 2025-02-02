<?php

namespace Webkul\Odoomarketplaceconnect\Controller\Adminhtml;

/**
 * Webkul Odoomarketplaceconnect Category
 * @category  Webkul
 * @package   Webkul_Odoomarketplaceconnect
 * @author    Webkul
 * @copyright Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

abstract class Category extends \Magento\Backend\App\AbstractAction
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * User model factory
     *
     * @var \Magento\User\Model\UserFactory
     */
    protected $_userFactory;

    /**
     * Category model factory
     *
     * @var \Webkul\Odoomarketplaceconnect\Model\CategoryFactory
     */
    protected $_categoryFactory;

    /**
     * Category model factory
     *
     * @var \Magento\Sales\Model\ResourceModel\Order\CollectionFactory
     */
    protected $_salesOrderCollectionFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\User\Model\UserFactory $userFactory
     * @param \Webkul\Odoomarketplaceconnect\Model\CategoryFactory $categoryFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\User\Model\UserFactory $userFactory,
        \Webkul\Odoomagentoconnect\Helper\Connection $connection,
        \Webkul\Odoomagentoconnect\Model\Category $categoryMapping,
        \Webkul\Odoomagentoconnect\Model\ResourceModel\Category $categoryModel,
        \Webkul\Odoomarketplaceconnect\Model\Category $mpCategoryMapping,
        \Webkul\Odoomarketplaceconnect\Model\CategoryFactory $categoryFactory
    ) {
        parent::__construct($context);
        $this->_coreRegistry = $coreRegistry;
        $this->_userFactory = $userFactory;
        $this->_connection = $connection;
        $this->_categoryFactory = $categoryFactory;
        $this->_categoryMapping = $categoryMapping;
        $this->_categoryModel = $categoryModel;
        $this->_mpCategoryMapping = $mpCategoryMapping;
    }

    /**
     * @return $this
     */
    protected function _initAction()
    {
        $this->_view->loadLayout();
        $this->_setActiveMenu(
            'Webkul_Odoomagentoconnect::manager'
        );
        return $this;
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_User::acl_users');
    }
}
