define([
    "Magento_Checkout/js/view/payment/default",
    "Magento_Checkout/js/action/redirect-on-success",
    "mage/url",
], function (Component, redirectOnSuccessAction, url) {
    "use strict";
    return Component.extend({
        defaults: {
            template: "Meezan_MeezanBank/payment/form",
        },
        getdescription: function () {
            return window.checkoutConfig.payment.meezan_bank.description;
        },
        afterPlaceOrder: function () {
            redirectOnSuccessAction.redirectUrl = url.build("meezan/index/");
            this.redirectAfterPlaceOrder = true;
        },
    });
});
