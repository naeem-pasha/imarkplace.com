<?php
/**
 * Webkul MpAffiliate Preferences.
 *
 * @category  Webkul
 * @package   Webkul_MpAffiliate
 * @author    Webkul
 * @copyright Copyright (c)  Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\MpAffiliate\Block\User;

use Magento\Framework\View\Element\Template\Context;
use Magento\Customer\Model\Session;
use Webkul\MpAffiliate\Helper\Data as AffDataHelper;
use Webkul\MpAffiliate\Model\Config\Source\AffiliateAllowedPaymentMethodsList as AffAllowPayMethods;
use Webkul\MpAffiliate\Model\UserFactory;

class Preferences extends \Webkul\MpAffiliate\Block\User\UserAbstract
{
    /**
     * @var \Webkul\Affiliate\Model\Config\Source\AffiliateAllowedPaymentMethodsList
     */
    private $affAllowPayMethods;

    /**
     * @param Context         $context
     * @param Session         $customerSession,
     * @param RedirectFactory $redirect,
     * @param AffDataHelper   $affDataHelper,
     * @param array           $data
     */
    public function __construct(
        Context $context,
        Session $customerSession,
        AffDataHelper $affDataHelper,
        AffAllowPayMethods $affAllowPayMethods,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        UserFactory $userFactory,
        array $data = []
    ) {

        $this->affAllowPayMethods = $affAllowPayMethods;
        $this->customerFactory = $customerFactory;
        $this->affDataHelper = $affDataHelper;
        $this->customerSession = $customerSession;
        $this->userFactory = $userFactory;
        parent::__construct($context, $customerSession, $affDataHelper, $customerFactory, $data);
    }

    /**
     * getSaveAction
     * @return array
     */
    public function getAllowedPaymentMethod()
    {
        $config = $this->getAffiliateConfig();
        return explode(',', $config['payment_methods']);
    }

    /**
     * getPayMethodLabel
     * @param string $methodCode
     * @param string
     */
    public function getPayMethodLabel($methodCode)
    {
        return $this->affAllowPayMethods->getOptionText($methodCode);
    }

    /**
     * getSaveAction
     */
    public function getSaveAction()
    {
        return $this->getUrl('mpaffiliate/user/preferences', ['_secure' => $this->getRequest()->isSecure()]);
    }

    /**
     * getPaymentMethodData\
     * @return json
     */
    public function getPaymentMethodData()
    {
        $customerId = $this->customerSession->getCustomerId();
        $paymtData = $this->userFactory->create()->load($customerId, 'customer_id')->getPaymentMethod();
        if ($paymtData == '') {
            $paymtData = '{"payment_method":"checkmo","account_data":{}}';
        }
        return $paymtData;
    }
}
