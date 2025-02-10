<?php
/**
 * Webkul MpAffiliate User Status controller.
 * @category  Webkul
 * @package   Webkul_MpAffiliate
 * @author    Webkul
 * @copyright Copyright (c)  Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\MpAffiliate\Controller\Marketplace;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Webkul\MpAffiliate\Model\TextBannerFactory;

class Actionbanner extends \Magento\Customer\Controller\AbstractAccount
{
    /**
     * @var PageFactory
     */
    private $_resultPageFactory;

    private $textbannerFactory;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        TextBannerFactory $textbannerFactory,
        PageFactory $resultPageFactory,
        \Webkul\MpAffiliate\Logger\Logger $logger
    ) {

        $this->_resultPageFactory = $resultPageFactory;
        $this->textbannerFactory = $textbannerFactory;
        $this->logger = $logger;
        parent::__construct($context);
    }

    /**
     * Add Auction on product page
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        if ($this->getRequest()->isPost()) {
            $data=$this->getRequest()->getParams();
            if ($data['actiontype']==1 || $data['actiontype']==0) {
                $tempAff = $this->textbannerFactory->create()->load($data['requestid']);
                if ($data['actiontype']==1) {
                    $tempAff->setStatus(1);
                } else {
                    $tempAff->setStatus(0);
                }
                $tempAff->save();
            }
            if ($data['actiontype']==2) {
                $tempAff = $this->textbannerFactory->create()->load($data['requestid']);
                $tempAff->delete();
            }
        }
    }
}
