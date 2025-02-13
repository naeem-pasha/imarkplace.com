<?php
/**
 * Copyright © www.magebig.com - All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageBig\FastCheckout\Plugin\Checkout\Model;

use MageBig\FastCheckout\Helper\Data;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Checkout\Model\DefaultConfigProvider;
use Magento\Checkout\Model\Session;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\PaymentMethodManagementInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

class FastCheckoutConfigProvider
{
    /**
     * @var Session
     */
    protected $checkoutSession;

    /**
     * @var PaymentMethodManagementInterface
     */
    protected $paymentMethodManagement;

    /**
     * @var RequestInterface
     */
    protected $_request;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @param RequestInterface $request
     * @param Session $checkoutSession
     * @param PaymentMethodManagementInterface $paymentMethodManagement
     * @param ScopeConfigInterface $scopeConfig
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        RequestInterface $request,
        Session $checkoutSession,
        PaymentMethodManagementInterface $paymentMethodManagement,
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager
    ) {
        $this->_request = $request;
        $this->checkoutSession = $checkoutSession;
        $this->paymentMethodManagement = $paymentMethodManagement;
        $this->scopeConfig = $scopeConfig;
        $this->_storeManager = $storeManager;
    }

    /**
     * After get config
     *
     * @param DefaultConfigProvider $subject
     * @param array $result
     * @return array
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function afterGetConfig(
        DefaultConfigProvider $subject,
        array $result
    ) {
        $path = "\x66\141\x73\164\x63\x68\145\143\153\x6f\165\x74\57\x67\145\156\145\162\x61\154\57" .
                "\151\163\137\x61\143\x74\151\x76\x65\x5f\x6d\157\144\x75\154\145";
        $storeId = $this->_storeManager->getStore()->getId();

        if (!$this->scopeConfig->getValue($path, ScopeInterface::SCOPE_STORE, $storeId)) {
            $result['fastCheckout']['isEnable'] = 0;

            return $result;
        }

        if ($this->_request->getRouteName() === 'checkout') {
            $this->fixMissingDefaultAddressId($result);

            $quote = $this->checkoutSession->getQuote();
            if (!$quote->getIsVirtual()) {
                foreach ($this->paymentMethodManagement->getList($quote->getId()) as $paymentMethod) {
                    $result['paymentMethods'][] = [
                        'code' => $paymentMethod->getCode(),
                        'title' => $paymentMethod->getTitle()
                    ];
                }
            }
        }

        $bill = (bool)$this->scopeConfig->getValue('fastcheckout/shipping_address/country_id_billing_show');

        $result['fastCheckout'] = [
            'isEnable' => 1,
            'showLoginForm' => (int)$this->scopeConfig->getValue('fastcheckout/general/show_login_form'),
            'showCountry' => (bool)$this->scopeConfig->getValue('fastcheckout/shipping_address/country_id_show'),
            'showCountryBilling' => $bill,
            'shippingMethod' => (int)$this->scopeConfig->getValue('fastcheckout/general/default_shipping_method'),
            'paymentMethod' => (int)$this->scopeConfig->getValue('fastcheckout/general/default_payment_method'),
            'fullName' => (int)$this->scopeConfig->getValue('fastcheckout/additional/show_full_name')
        ];

        return $result;
    }

    /**
     * Fixed missing default address.
     *
     * @param array &$result
     * @return void
     */
    public function fixMissingDefaultAddressId(&$result)
    {
        if (!empty($result['customerData']) &&
            !empty($result['customerData']['addresses']) &&
            empty($result['customerData']['default_shipping'])
        ) {
            $id = key($result['customerData']['addresses']);
            $result['customerData']['default_shipping'] = $id;
            $result['customerData']['addresses'][$id]['default_shipping'] = true;
        }
    }
}
