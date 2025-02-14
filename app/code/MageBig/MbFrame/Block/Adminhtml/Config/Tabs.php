<?php
/**
 * Copyright © magebig.com - All rights reserved.
 * See LICENSE.txt for license details.
 */

/**
 * System configuration tabs block.
 *
 * @method setTitle(string $title)
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */

namespace MageBig\MbFrame\Block\Adminhtml\Config;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Tabs extends \Magento\Backend\Block\Widget
{
    /**
     * Tabs.
     *
     * @var \Magento\Config\Model\Config\Structure\Element\Iterator
     */
    protected $_tabs;

    /**
     * Block template filename.
     *
     * @var string
     */
    protected $_template = 'Magento_Config::system/config/tabs.phtml';

    /**
     * Currently selected section id.
     *
     * @var string
     */
    protected $_currentSectionId;

    /**
     * Current website code.
     *
     * @var string
     */
    protected $_websiteCode;

    /**
     * Current store code.
     *
     * @var string
     */
    protected $_storeCode;

    /**
     * @var \Magento\Backend\Helper\Data
     */
    protected $_backendHelper;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Config\Model\Config\Structure  $configStructure
     * @param \Magento\Backend\Helper\Data            $backendHelper
     * @param array                                   $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \MageBig\MbFrame\Model\Config\Structure $configStructure,
        \Magento\Backend\Helper\Data $backendHelper,
        array $data = []
    ) {
        $this->_backendHelper = $backendHelper;
        parent::__construct($context, $data);
        $this->_tabs = $configStructure->getTabs();
        $this->setId('system_config_tabs');
        $this->setTitle(__('Configuration'));
        $this->_currentSectionId = $this->getRequest()->getParam('section');

        $this->_backendHelper->addPageHelpUrl($this->getRequest()->getParam('section').'/');
    }

    /**
     * Get all tabs.
     *
     * @return \Magento\Config\Model\Config\Structure\Element\Iterator
     */
    public function getTabs()
    {
        return $this->_tabs;
    }

    /**
     * Retrieve section url by section id.
     *
     * @param \Magento\Config\Model\Config\Structure\Element\Section $section
     *
     * @return string
     */
    public function getSectionUrl(\Magento\Config\Model\Config\Structure\Element\Section $section)
    {
        return $this->getUrl('*/*/*', ['_current' => true, 'section' => $section->getId()]);
    }

    /**
     * Check whether section should be displayed as active.
     *
     * @param \Magento\Config\Model\Config\Structure\Element\Section $section
     *
     * @return bool
     */
    public function isSectionActive(\Magento\Config\Model\Config\Structure\Element\Section $section)
    {
        return $section->getId() == $this->_currentSectionId;
    }
}
