<?php
/**
 * Webkul Odoomarketplaceconnect Tabs
 * @category  Webkul
 * @package   Webkul_Odoomarketplaceconnect
 * @author    Webkul
 * @copyright Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\Odoomarketplaceconnect\Block\Adminhtml\Category\Edit;

class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    /**
     * Class constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('page_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Website Category Info'));
    }

    /**
     * @return $this
     */
    protected function _beforeToHtml()
    {
        $this->addTab(
            'main_section',
            [
                'label' => __('Website Category Mapping'),
                'title' => __('Website Category Mapping'),
                'content' => $this->getLayout()
                                    ->createBlock(
                                        'Webkul\Odoomarketplaceconnect\Block\Adminhtml\Category\Edit\Tab\Main'
                                    )
                                    ->toHtml(),
                'active' => true
            ]
        );
        return parent::_beforeToHtml();
    }
}
