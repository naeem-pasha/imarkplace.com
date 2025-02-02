<?php
/** Webkul MpAffiliate Wysiwyg Editor Block
 * @category  Webkul
 * @package   Webkul_MpAffiliate
 * @author    Webkul
 * @copyright Copyright (c)  Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\MpAffiliate\Block\Adminhtml\System\Config;

use Magento\Backend\Block\Template\Context;
use Magento\Cms\Model\Wysiwyg\Config as WysiwygConfig;
use Magento\Framework\Data\Form\Element\AbstractElement;

class Editor extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
     * WysiwygConfig
     * @var $wysiwygConfig
     */
    private $wysiwygConfig;

    /**
     * @param Context       $context
     * @param WysiwygConfig $wysiwygConfig
     * @param array         $data
     */
    public function __construct(
        Context $context,
        WysiwygConfig $wysiwygConfig,
        array $data = []
    ) {

        $this->wysiwygConfig = $wysiwygConfig;
        parent::__construct($context, $data);
    }

    public function _getElementHtml(AbstractElement $element)
    {
        $element->setWysiwyg(true);
        $data = $this->wysiwygConfig->getConfig($element)->getData();
        unset($data['plugins'][2]);
        $obj = new \Magento\Framework\DataObject();
        $data = $obj->setData($data);
        $element->setConfig($data);
        return parent::_getElementHtml($element);
    }
}
