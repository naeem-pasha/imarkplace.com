<?php

/**
 * Webkul MpAffiliate RegisterLink.
 *
 * @category  Webkul
 * @package   Webkul_MpAffiliate
 * @author    Webkul
 * @copyright Copyright (c)  Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\MpAffiliate\Block\User;

/**
 * "Orders and Returns" link
 */
class RegisterLink extends \Magento\Framework\View\Element\Html\Link\Current
{
     /**
      * Constructor
      *
      * @param \Magento\Framework\View\Element\Template\Context $context
      * @param \Magento\Framework\App\DefaultPathInterface $defaultPath
      * @param array $data
      */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\App\DefaultPathInterface $defaultPath,
        \Magento\Customer\Model\Session $session,
        array $data = []
    ) {
        parent::__construct($context, $defaultPath, $data);
        $this->session = $session;
    }

    /**
     * @return string
     */
    public function _toHtml()
    {
        $customerSession = $this->session;
        $affiliateStatus1 = $this->_scopeConfig->getValue('Marketplaceaffiliate/general/enable');
        $affiliateStatus2 = $this->_scopeConfig->getValue('Marketplaceaffiliate/general/registration');
        if ($affiliateStatus1 && $affiliateStatus2 && !$customerSession->isLoggedIn()) {
            return parent::_toHtml();
        }
        return '';
    }
}
