<?php
/**
 * Webkul MpAffiliate User Status.
 *
 * @category  Webkul
 * @package   Webkul_MpAffiliate
 * @author    Webkul
 * @copyright Copyright (c)  Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\MpAffiliate\Block\User;

class Status extends \Webkul\MpAffiliate\Block\User\UserAbstract
{
    /**
     * getBlogUrlHint for check affiliate user status
     * @return array;
     */
    public function getBlogUrlHint()
    {
        $config = $this->getAffiliateConfig();
        return $config['blog_url_hint'];
    }

    /**
     * isAffilateRegistration
     * @return bool
     */
    public function getAffilateRegistrationTerms()
    {
        return $this->_scopeConfig->getValue('affiliate/terms/editor_textarea');
    }
}
