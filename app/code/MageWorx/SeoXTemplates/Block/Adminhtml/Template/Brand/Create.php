<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoXTemplates\Block\Adminhtml\Template\Brand;

use Magento\Backend\Block\Widget\Form\Container as FormContainer;
use Magento\Backend\Block\Widget\Context;
use Magento\Framework\Registry;

class Create extends FormContainer
{
    /**
     * @var string
     */
    protected $_mode = 'create';

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * Constructor
     *
     * @param Context $context
     * @param Registry $registry
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        array $data = []
    ) {
    
        $this->coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * Initialize template brand create block
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_objectId   = 'template_id';
        $this->_blockGroup = 'MageWorx_SeoXTemplates';
        $this->_controller = 'adminhtml_template_brand';
        parent::_construct();
        $this->buttonList->update('save', 'label', __('Continue'));
        $this->buttonList->remove('reset');
        $this->buttonList->remove('delete');
    }

    /**
     * Retrieve text for header element depending on loaded template
     *
     * @return string
     */
    public function getHeaderText()
    {
        /** @var \MageWorx\SeoXTemplates\Model\Template $template */
        $temlate = $this->coreRegistry->registry('mageworx_seoxtemplates_template');
        if ($temlate && $temlate->getId()) {
            return __("Edit Brand Page Template '%1'", $this->escapeHtml($temlate->getName()));
        }
        return __('New Template');
    }
}
