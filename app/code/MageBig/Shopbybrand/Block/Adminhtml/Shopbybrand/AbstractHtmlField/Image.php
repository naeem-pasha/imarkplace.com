<?php
/**
 * Copyright © 2020 MageBig, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace MageBig\Shopbybrand\Block\Adminhtml\Shopbybrand\AbstractHtmlField;

class Image extends \MageBig\Shopbybrand\Block\Adminhtml\Shopbybrand\AbstractHtmlField
{
    /**
     * Form element which re-rendering
     *
     * @var \Magento\Framework\Data\Form\Element\Fieldset
     */
    protected $_element;

    /**
     * @var string
     */
    protected $_template = 'shopbybrand/fieldset/image.phtml';

    /**
     * Retrieve an element
     *
     * @return \Magento\Framework\Data\Form\Element\Fieldset
     */
    public function getElement()
    {
        return $this->_element;
    }

    /**
     * Render element
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $this->_element = $element;
        return $this->toHtml();
    }

    /**
     * Return html for store switcher hint
     *
     * @return string
     */
    public function getBaseUrl()
    {
        return $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
    }

    public function getHintHtml()
    {
        $element = $this->_element;
        $elementId = $element->getHtmlId();
        $imageId = $elementId . '_image';

        if (!empty($element->getValue())) {
            $imageUrl = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . $element->getValue();
        } else {
            $imageUrl = $this->assetRepo->getUrl('MageBig_Shopbybrand/images/placeholder_thumbnail.jpg');
        }

        $html = $this->_getButtonHtml(
            [
                'title' => __('Choose Image'),
                'style' => ' float: left; margin-right: 10px;',
                'onclick' => "MbMediabrowserUtility.openDialog(this, '" . $this->getUrl(
                    'cms/wysiwyg_images/index',
                    ['target_element_id' => $elementId]
                )
                    . "', null, null,'" . $this->escapeQuote(
                        __('Upload Images'),
                        true
                    ) . "');",
                'disabled' => $element->getDisabled()
            ]
        );
        $html .= '<button class="clear-img scalable" onclick="clearValue(this,\'' . $elementId . '\');return false;"' . ($element->getDisabled() ? 'disabled' : '') . '><span><span><span>&times;</span></span></span></button>';
        $html .= '<a class="attached_image" style="float: left;" href="' . $imageUrl . '" onclick="imagePreview(\'' . $imageId . '\'); return false;"><img alt="imarkplace" ' . (($element->getDisabled()) ? 'style="display:none"' : '') . ' height="33" id="' . $imageId . '" src="' . $imageUrl . '" /></a>';
        return $html;
    }
}
