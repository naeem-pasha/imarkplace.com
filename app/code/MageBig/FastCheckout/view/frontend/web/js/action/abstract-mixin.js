define([
    'jquery',
    'mage/utils/wrapper',
    'mage/translate'
], function ($, wrapper, $t) {
    'use strict';

    return function (targetModule) {
        if (!window.checkoutConfig.fastCheckout.isEnable) {
            return targetModule;
        }

        let getDescriptionId = targetModule.prototype.getDescriptionId,
            init = targetModule.prototype.initialize,
            timer,
            checkoutConfig = window.checkoutConfig.fastCheckout;

        targetModule.prototype.initialize = wrapper.wrap(init, function (original) {
            original();

            if (this.input_type === 'input') {
                if (this.source && this.source.index === 'checkoutProvider') {
                    this.placeholder = ' ';
                }
            }

            if (this.inputName === 'country_id') {
                if (!checkoutConfig.showCountryBilling && this.dataScope === 'billingAddressshared.country_id') {
                    this.visible(false);
                }

                if (!checkoutConfig.showCountry && this.dataScope === 'shippingAddress.country_id') {
                    this.visible(false);
                }
            }

            if (this.inputName === 'street[0]' && this.dataScope === 'billingAddressshared.street.0') {
                this.label = $t('Street Address');
            }
        });

        targetModule.prototype.getDescriptionId = wrapper.wrap(getDescriptionId, function (original) {
            let self = this,
                id = original();

            if (id) {
                if (timer) {
                    clearTimeout(timer);
                }

                timer = setTimeout(function () {
                    $('#' + id).fadeOut();
                }, 5000)

                $(self.uid).on('focus', function () {
                    timer = setTimeout(function () {
                        $('div.mage-error').fadeOut();
                        $('.field-error').fadeOut();
                    }, 5000)
                })
            }

            return id;
        });

        return targetModule;
    };
});
