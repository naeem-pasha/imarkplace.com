<?php
namespace Imark\M2Modal\Block;

class Popover extends \Magento\Framework\View\Element\Template
{
    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    public function getContent() : string
    {
        return '<img alt="imarkplace" src=\'https://imarkplace.com/media/dumpimg/flashbanner.png\' width="961" height="540" /> ';
    }
}
