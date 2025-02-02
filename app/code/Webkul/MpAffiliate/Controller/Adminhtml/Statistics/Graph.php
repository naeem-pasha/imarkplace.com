<?php
/**
 * Webkul MpAffiliate Statistics Graph Controller
 * @category  Webkul
 * @package   Webkul_MpAffiliate
 * @author    Webkul
 * @copyright Copyright (c)  Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\MpAffiliate\Controller\Adminhtml\Statistics;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Graph extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    private $resultPageFactory;

    /**
     * @param Context     $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory
    ) {

        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * Affiliate Statistics Graph page.
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Webkul_Marketplace::seller');
        $resultPage->getConfig()->getTitle()->prepend(__('Statistics Graph'));

        return $resultPage;
    }

    /**
     * Check Affiliate Payment List Permission.
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Webkul_MpAffiliate::statistics_graph');
    }
}
