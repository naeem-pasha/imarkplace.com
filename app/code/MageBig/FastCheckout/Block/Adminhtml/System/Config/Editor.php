<?php
/**
 * Copyright © www.magebig.com - All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageBig\FastCheckout\Block\Adminhtml\System\Config;

use Magento\Backend\Block\Template\Context;
use Magento\Cms\Model\Wysiwyg\Config as WysiwygConfig;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

class Editor extends Field
{
    /**
     * @var WysiwygConfig
     */
    private $_wysiwygConfig;

    /**
     * @param Context $context
     * @param WysiwygConfig $wysiwygConfig
     * @param array $data
     */
    public function __construct(
        Context $context,
        WysiwygConfig $wysiwygConfig,
        array $data = []
    ) {
        $this->_wysiwygConfig = $wysiwygConfig;
        parent::__construct($context, $data);
    }

    /**
     * Get html
     *
     * @param AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element): string
    {
        // set wysiwyg for element
        $element->setWysiwyg(true);
        // set configuration values
        $config = $this->_wysiwygConfig->getConfig(['add_variables' => false]);
        $config['height'] = '200px';
        $element->setConfig($config);
        return parent::_getElementHtml($element);
    }
}
