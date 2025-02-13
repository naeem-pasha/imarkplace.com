define([
    'jquery',
    'mage/utils/wrapper'
], function(
    $,
    wrapper
) {
    'use strict';

    let warningTimer;

    return function(shippingRatesValidator) {
        if (!window.checkoutConfig.fastCheckout.isEnable) {
            return shippingRatesValidator;
        }

        shippingRatesValidator.postcodeValidation = wrapper.wrapSuper(shippingRatesValidator.postcodeValidation, function(postcodeElement) {
            this._super(postcodeElement);

            if (warningTimer) {
                clearTimeout(warningTimer);
            }

            warningTimer = setTimeout(function () {
                $('.message.warning').remove();
            }, 8000);
        })

        return shippingRatesValidator;
    };
});
