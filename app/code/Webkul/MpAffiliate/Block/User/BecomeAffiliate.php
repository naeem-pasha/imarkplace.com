<?php
/**
 * Webkul MpAffiliate User Status.
 *
 * @category  Webkul
 * @package   Webkul_MpAffiliate
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\MpAffiliate\Block\User;

class BecomeAffiliate extends \Webkul\MpAffiliate\Block\User\UserAbstract
{
    /**
     * isAffilateRegistration
     * @return bool
     */
    public function getAffilateRegistrationTerms()
    {
        return $this->_scopeConfig->getValue('affiliate/terms/editor_textarea');
    }
}
