<?php
/**
 * Copyright © www.magebig.com - All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageBig\FastCheckout\Plugin\Checkout\Block;

use Magento\Checkout\Block\Checkout\LayoutProcessor as CheckoutLayoutProcessor;
use Magento\Framework\App\Config\ScopeConfigInterface;
use MageBig\FastCheckout\Helper\Data;
use Magento\Framework\Exception\NoSuchEntityException;

class LayoutProcessor
{
    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var Data
     */
    protected $helper;

    /**
     * LayoutProcessor construct
     *
     * @param ScopeConfigInterface $scopeConfig
     * @param Data $helper
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        Data $helper
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->helper = $helper;
    }

    /**
     * After progress layout
     *
     * @param CheckoutLayoutProcessor $subject
     * @param array $jsLayout
     *
     * @return array
     * @throws NoSuchEntityException
     */
    public function afterProcess(CheckoutLayoutProcessor $subject, array $jsLayout)
    {
        if (!$this->helper->getChecked()) {
            return $jsLayout;
        }

        $fullName = $this->helper->getFullName();
        $fullNameLabel = __($this->helper->getFullNameLabel());
        $sortAddress = $this->helper->getSortAddress();
        $streetLabel = __($this->helper->getStreetLabel());
        $isBilling = isset($jsLayout['components']['checkout']['children']['steps']['children']['billing-step']
            ['children']['payment']['children']['afterMethods']['children']);

        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']
        ['children']['shippingAddress']['children']['shipping-address-fieldset']
        ['children']['telephone']['config']['tooltip'] = false;

        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']
        ['children']['shippingAddress']['children']['shipping-address-fieldset']
        ['children']['street']['children']['0']['label'] = $streetLabel;

        if ($isBilling) {
            $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']
            ['payment']['children']['afterMethods']['children']['billing-address-form']['children']['form-fields']
            ['children']['telephone']['config']['tooltip'] = false;
        }

        if ($fullName) {
            $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
            ['shippingAddress']['children']['shipping-address-fieldset']['children']['full-name'] = [

                'component' => 'Magento_Ui/js/form/element/abstract',
                'config' => [
                    'customEntry' => null,
                    'template' => 'ui/form/field',
                    'elementTmpl' => 'ui/form/element/input',
                    'additionalClasses' => 'full-name',
                ],
                'dataScope' => 'shippingAddress.fullName',
                'provider' => 'checkoutProvider',
                'validation' => [
                    'required-entry' => true
                ],
                'filterBy' => null,
                'customEntry' => null,
                'visible' => true,
                'label' => $fullNameLabel,
                'placeholder' => ' '
            ];

            $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
            ['shippingAddress']['children']['shipping-address-fieldset']['children']['firstname']['visible'] = false;
            $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
            ['shippingAddress']['children']['shipping-address-fieldset']['children']['lastname']['visible'] = false;

            if ($isBilling) {
                $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']
                ['payment']['children']['afterMethods']['children']['billing-address-form']['children']['form-fields']
                ['children']['full-name'] = [

                    'component' => 'Magento_Ui/js/form/element/abstract',
                    'config' => [
                        'customEntry' => null,
                        'template' => 'ui/form/field',
                        'elementTmpl' => 'ui/form/element/input',
                        'additionalClasses' => 'full-name',
                    ],
                    'dataScope' => 'billingAddress.fullName',
                    'provider' => 'checkoutProvider',
                    'validation' => [
                        'required-entry' => true
                    ],
                    'filterBy' => null,
                    'customEntry' => null,
                    'visible' => true,
                    'label' => $fullNameLabel,
                    'sortOrder' => 41,
                    'placeholder' => ' '
                ];

                $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']
                ['payment']['children']['afterMethods']['children']['billing-address-form']['children']['form-fields']
                ['children']['firstname']['visible'] = false;
                $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']
                ['payment']['children']['afterMethods']['children']['billing-address-form']['children']['form-fields']
                ['children']['lastname']['visible'] = false;
            }
        }

        $position = 20;

        foreach ($sortAddress as $field) {
            $position += 10;
            $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']
            ['children']['shippingAddress']['children']['shipping-address-fieldset']
            ['children'][$field]['sortOrder'] = $position;

            if ($isBilling) {
                $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']
                ['payment']['children']['afterMethods']['children']['billing-address-form']['children']['form-fields']
                ['children'][$field]['sortOrder'] = $position;
            }

            if ($field == 'region') {
                $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']
                ['children']['shippingAddress']['children']['shipping-address-fieldset']
                ['children'][$field]['sortOrder'] = $position + 1;

                if ($isBilling) {
                    $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']
                    ['payment']['children']['afterMethods']['children']['billing-address-form']['children']
                    ['form-fields']['children'][$field]['sortOrder'] = $position + 1;
                }
            }
        }

        if ($fullName) {
            $posFirstName = $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']
            ['children']['shippingAddress']['children']['shipping-address-fieldset']
            ['children']['firstname']['sortOrder'];
            $posLastName = $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']
            ['children']['shippingAddress']['children']['shipping-address-fieldset']
            ['children']['lastname']['sortOrder'];

            $posFullName = $posFirstName > $posLastName ? $posFirstName + 1 : $posLastName + 1;

            $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
            ['shippingAddress']['children']['shipping-address-fieldset']['children']
            ['full-name']['sortOrder'] = $posFullName;

            if ($isBilling) {
                $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']
                ['payment']['children']['afterMethods']['children']['billing-address-form']['children']['form-fields']
                ['children']['full-name']['sortOrder'] = $posFullName;
            }
        }

        $showBilling = $this->scopeConfig->getValue('fastcheckout/general/show_billing_address');

        if (!$showBilling) {
            if (isset($jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']
                ['payment']['children']['afterMethods']['children']['billing-address-form'])) {
                unset($jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']
                    ['payment']['children']['afterMethods']['children']['billing-address-form']);
            }
        }

        // Remove unused Magento captcha
        $isCaptcha = $this->scopeConfig->getValue('customer/captcha/enable');
        $formCaptcha = explode(',', $this->scopeConfig->getValue('customer/captcha/forms'));

        if (!$isCaptcha || !in_array('payment_processing_request', $formCaptcha)) {
            if (isset($jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']
                ['payment']['children']['place-order-captcha'])) {
                unset($jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']
                    ['payment']['children']['place-order-captcha']);
            }
        }

        if (!$isCaptcha || !in_array('co-payment-form', $formCaptcha)) {
            if (isset($jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']
                ['payment']['children']['payments-list']['children']['paypal-captcha']['children']['captcha'])) {
                unset($jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']
                    ['payment']['children']['payments-list']['children']['paypal-captcha']['children']['captcha']);
            }
        }

        if (!$isCaptcha || !in_array('user_login', $formCaptcha)) {
            if (isset($jsLayout['components']['checkout']['children']['authentication']['children']['captcha'])) {
                unset($jsLayout['components']['checkout']['children']['authentication']['children']['captcha']);
            }

            if (isset($jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
                ['shippingAddress']['children']['customer-email']['children']
                ['additional-login-form-fields']['children']['captcha'])) {
                unset($jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
                    ['shippingAddress']['children']['customer-email']['children']
                    ['additional-login-form-fields']['children']['captcha']);
            }
        }

        return $jsLayout;
    }
}
