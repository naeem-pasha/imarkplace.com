<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_MarketplaceLearningManagementSystem
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\MarketplaceLearningManagementSystem\Block\Adminhtml\Form\Field;

use Magento\Framework\UrlInterface;

/**
 * Course Content File Uploader
 */
class FileUploader extends \Magento\Backend\Block\Template
{
    /**
     * Block template.
     *
     * @var string
     */
    protected $_template = 'Form/Field/fileUploader.phtml';

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param UrlInterface $urlBuilder
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        UrlInterface $urlBuilder,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * Get Uploader Url
     *
     * @return string
     */
    public function getUploaderUrl()
    {
        return $this->urlBuilder->getUrl('mplms/content/upload');
    }
}
