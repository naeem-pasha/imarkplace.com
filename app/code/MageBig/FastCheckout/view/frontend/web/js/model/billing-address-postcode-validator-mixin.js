define([
    'jquery',
    'mage/utils/wrapper'
], function(
    $,
    wrapper
) {
    'use strict';

    let warningTimer;

    return function(billingAddressPostcodeValidator) {
        if (!window.checkoutConfig.fastCheckout.isEnable) {
            return billingAddressPostcodeValidator;
        }

        billingAddressPostcodeValidator.postcodeValidation = wrapper.wrapSuper(billingAddressPostcodeValidator.postcodeValidation, function(postcodeElement) {
            this._super(postcodeElement);

            if (warningTimer) {
                clearTimeout(warningTimer);
            }

            warningTimer = setTimeout(function () {
                $('.message.warning').remove();
            }, 8000);
        })

        return billingAddressPostcodeValidator;
    };
});
