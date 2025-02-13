/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'mage/validation'
], function ($) {
    'use strict';

    var checkoutConfig = window.checkoutConfig,
        agreementsConfig = checkoutConfig ? checkoutConfig.checkoutAgreements : {},
        agreementsInputPath = 'div.checkout-agreements input';

    if (!window.checkoutConfig.fastCheckout.isEnable) {
        agreementsInputPath = '.payment-method._active div.checkout-agreements input';
    }

    return {
        /**
         * Validate checkout agreements
         *
         * @returns {Boolean}
         */
        validate: function (hideError) {
            let isValid = true;

            if (!agreementsConfig.isEnabled || $(agreementsInputPath).length === 0) {
                return true;
            }

            $(agreementsInputPath).each(function (index, element) {
                if (!$.validator.validateSingleElement(element, {
                    errorElement: 'div',
                    hideError: hideError || false
                })) {
                    isValid = false;
                }
            });

            if (!isValid && window.checkoutConfig.fastCheckout.isEnable) {
                let elm = $('.checkout-agreements'),
                    $win = $(window),
                    windowHeight = $win.innerHeight(),
                    pos = elm.offset().top,
                    winScroll = $win.scrollTop();

                if (pos > (windowHeight + winScroll) || winScroll > pos - 80) {
                    $('html, body').stop().animate({
                        scrollTop: elm.offset().top - 120
                    });
                }
            }

            return isValid;
        }
    };
});
