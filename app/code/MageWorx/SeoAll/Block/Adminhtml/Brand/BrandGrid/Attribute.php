<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoAll\Block\Adminhtml\Brand\BrandGrid;

/**
 * Class Attribute
 */
class Attribute extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\Text
{
    /**
     * @var DataProvider
     */
    protected $dataProvider;

    /**
     * Attribute constructor.
     *
     * @param \MageWorx\SeoAll\Block\Adminhtml\Brand\BrandGrid\DataProvider $dataProvider
     * @param \Magento\Backend\Block\Context $context
     * @param array $data
     */
    public function __construct(
        DataProvider $dataProvider,
        \Magento\Backend\Block\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->dataProvider = $dataProvider;
    }

    /**
     * @param \Magento\Framework\DataObject $row
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function render(\Magento\Framework\DataObject $row)
    {
        parent::render($row);

        return $this->dataProvider->getAttributeLabel($row->getAttributeId());
    }
}
