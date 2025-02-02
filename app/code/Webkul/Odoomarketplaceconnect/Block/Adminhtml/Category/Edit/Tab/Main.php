<?php
/**
 * Webkul Odoomarketplaceconnect Main
 * @category  Webkul
 * @package   Webkul_Odoomarketplaceconnect
 * @author    Webkul
 * @copyright Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\Odoomarketplaceconnect\Block\Adminhtml\Category\Edit\Tab;

class Main extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Backend\Model\Auth\Session $authSession
     * @param \Magento\Framework\Locale\ListsInterface $localeLists
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Webkul\Odoomarketplaceconnect\Model\ResourceModel\Category $categoryMapping,
        \Magento\Framework\Data\FormFactory $formFactory,
        array $data = []
    ) {
        $this->_categoryMapping = $categoryMapping;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare form fields
     *
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     * @return \Magento\Backend\Block\Widget\Form
     */
    protected function _prepareForm()
    {
        $mageCategory = $this->_categoryMapping->getMageCategoryArray();
        $odooCategory = $this->_categoryMapping->getErpCategoryArray();
        $model = $this->_coreRegistry->registry('odoomarketplaceconnect_user');
        $categorymodel = $this->_coreRegistry->registry('odoomarketplaceconnect_category');

        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('user_');

        $baseFieldset = $form->addFieldset(
            'base_fieldset',
            ['legend' => __('Website Category Mapping'), 'class' => 'fieldset-wide']
        );

        if ($categorymodel->getEntityId()) {
            $baseFieldset->addField('entity_id', 'hidden', ['name' => 'entity_id']);
        }

        $baseFieldset->addField(
            'magento_id',
            'select',
            [
                'label' => __('Magento Category'),
                'title' => __('Magento Category'),
                'name' => 'magento_id',
                'required' => true,
                'options' => $mageCategory
            ]
        );
        $baseFieldset->addField(
            'odoo_id',
            'select',
            [
                'label' => __('Odoo Category'),
                'title' => __('Odoo Category'),
                'name' => 'odoo_id',
                'required' => true,
                'options' => $odooCategory
            ]
        );

        $data= $categorymodel->getData();
        $form->setValues($data);

        $this->setForm($form);

        return parent::_prepareForm();
    }
}
