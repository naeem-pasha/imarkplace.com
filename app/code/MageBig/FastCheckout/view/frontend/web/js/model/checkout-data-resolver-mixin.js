define([
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/action/select-shipping-method',
    'Magento_Checkout/js/model/payment-service',
    'Magento_Checkout/js/checkout-data',
    'Magento_Checkout/js/action/select-payment-method',
    'mage/utils/wrapper'
], function (
    quote,
    selectShippingMethodAction,
    paymentService,
    checkoutData,
    selectPaymentMethodAction,
    wrapper
) {
    'use strict';

    let checkoutConfig = window.checkoutConfig.fastCheckout;

    return function (checkoutDataResolver) {
        if (!checkoutConfig.isEnable) {
            return checkoutDataResolver;
        }

        checkoutDataResolver.resolvePaymentMethod = function () {
            let availablePaymentMethods = paymentService.getAvailablePaymentMethods(),
                selectedPaymentMethod = checkoutData.getSelectedPaymentMethod(),
                defaultMethod = checkoutConfig.paymentMethod,
                paymentMethod = selectedPaymentMethod ? selectedPaymentMethod : defaultMethod;

            if (availablePaymentMethods.length > 0) {
                availablePaymentMethods.some(function (payment) {
                    if (payment.method === paymentMethod) {
                        selectPaymentMethodAction(payment);
                    }
                });
            }
        };

        checkoutDataResolver.resolveShippingRates = wrapper.wrapSuper(
            checkoutDataResolver.resolveShippingRates,
            function (ratesData) {
                let selectedShippingRate = checkoutData.getSelectedShippingRate(),
                    defaultMethod = checkoutConfig.shippingMethod,
                    availableRate = false;

                if (ratesData.length > 0 && !quote.shippingMethod()) {
                    selectShippingMethodAction(ratesData[0]);
                } else if (!selectedShippingRate && defaultMethod) {
                    availableRate = _.find(ratesData, function (rate) {
                        return rate['carrier_code'] + '_' + rate['method_code'] === defaultMethod;
                    });

                    if (availableRate) {
                        selectShippingMethodAction(availableRate);
                    } else {
                        this._super(ratesData);
                    }
                } else {
                    this._super(ratesData);
                }
            })

        return checkoutDataResolver;
    };
});
