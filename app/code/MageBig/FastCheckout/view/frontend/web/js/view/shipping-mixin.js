define([
    'jquery',
    'underscore',
    'Magento_Ui/js/form/form',
    'ko',
    'Magento_Customer/js/model/customer',
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/action/select-shipping-address',
    'Magento_Checkout/js/action/set-shipping-information',
    'Magento_Checkout/js/model/checkout-data-resolver',
    'Magento_Checkout/js/checkout-data',
    'uiRegistry'
], function (
    $,
    _,
    Component,
    ko,
    customer,
    quote,
    selectShippingAddress,
    setShippingInformationAction,
    checkoutDataResolver,
    checkoutData,
    registry
) {
    'use strict';

    let mixin = {
        isSelected: ko.computed(function () {
            let value = quote.shippingMethod() ?
                quote.shippingMethod()['carrier_code'] + '_' + quote.shippingMethod()['method_code'] :
                null;

            if (value && window.checkoutConfig.fastCheckout.isEnable) {
                let timerId = setInterval(function () {
                    let input = 'input[value="' + value + '"]';

                    if ($(input).length) {
                        $('.table-checkout-shipping-method tr').removeClass('active');
                        $(input).parents('tr').addClass('active');

                        quote.billingAddress(null);
                        checkoutDataResolver.resolveBillingAddress();
                        registry.async('checkoutProvider')(function (checkoutProvider) {
                            let shippingAddressData = checkoutData.getShippingAddressFromData();
                            if (shippingAddressData) {
                                checkoutProvider.set(
                                    'shippingAddress',
                                    $.extend(true, {}, checkoutProvider.get('shippingAddress'), shippingAddressData)
                                );
                            }
                        });
                        setShippingInformationAction();

                        clearInterval(timerId);
                    }
                }, 100);
            }

            return value;
        })
    };

    return function (target) {
        if (!window.checkoutConfig.fastCheckout.isEnable) {
            return target;
        }

        return target.extend(mixin);
    };
});
