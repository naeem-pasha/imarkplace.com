define(['jquery'], function ($) {
    'use strict';

    var mixin = {
        defaults: {
            template: 'MageBig_FastCheckout/payment/discount'
        }
    };

    return function (target) {
        if (!window.checkoutConfig.fastCheckout.isEnable) {
            return target;
        }

        return target.extend(mixin);
    };
});
