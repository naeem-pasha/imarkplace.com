<?php
/**
 * Webkul MpAffiliate Configuration.
 *
 * @category  Webkul
 * @package   Webkul_MpAffiliate
 * @author    Webkul
 * @copyright Copyright (c)  Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\MpAffiliate\Block\Marketplace;

use Magento\Framework\View\Element\Template\Context;
use Magento\Customer\Model\Session;
use Webkul\MpAffiliate\Helper\Data as AffDataHelper;
use Webkul\MpAffiliate\Model\ConfigrationForAffiliateFactory;

class Configuration extends \Magento\Framework\View\Element\Template
{
    /**
     * @param Context         $context
     * @param Session         $customerSession,
     * @param AffDataHelper   $affDataHelper,
     * @param array           $data
     */
    public function __construct(
        Context $context,
        Session $customerSession,
        AffDataHelper $affDataHelper,
        ConfigrationForAffiliateFactory $configrationForAffiliateFactory,
        array $data = []
    ) {
        $this->customerSession = $customerSession;
        $this->affDataHelper = $affDataHelper;
        $this->configrationForAffiliateFactory = $configrationForAffiliateFactory;
        parent::__construct($context, $data);
    }

    public function getConfigration()
    {
        $sellerId = $this->customerSession->getCustomerId();
        $configModel = $this->configrationForAffiliateFactory->create();
        $configColl = $configModel->getCollection()->addFieldToFilter("seller_id", $sellerId);
        foreach ($configColl as $value) {
            $configModel = $value;
        }
        return $configModel;
    }
}
