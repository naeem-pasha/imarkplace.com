define(
    [
        'Magento_Checkout/js/view/payment/default',
        'Magento_Checkout/js/action/place-order',
        'mage/url',
    ],
    function (Component,placeOrderAction,url) {
        'use strict';
        return Component.extend(
            {
                defaults: {
                    template: 'Magento_Payriff/payment/payriffpaymentmethod-iframe'
                },
                getPaymentAcceptanceMarkSrc: function () {
                    return window.checkoutConfig.payment.payriff.logo_url;
                },
                isLogoVisible: function () {
                    return window.checkoutConfig.payment.payriff.logo_visible;
                },
                afterPlaceOrder: function () {
                    window.location.replace(url.build('payriff/redirect/'));
                },
                getMailingAddress: function () {
                    return window.checkoutConfig.payment.checkmo.mailingAddress;
                },
            }
        );
    }
);
