define(['ko'], function (ko) {
    'use strict';

    var mixin = {
        defaults: {
            template: 'MageBig_FastCheckout/shipping-address/address-renderer/default'
        }
    };

    return function (target) {
        if (!window.checkoutConfig.fastCheckout.isEnable) {
            return target;
        }

        return target.extend(mixin);
    };
});
