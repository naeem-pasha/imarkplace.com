define(['ko'], function (ko) {
    'use strict';

    var mixin = {
        defaults: {
            detailsTemplate: 'MageBig_FastCheckout/billing-address/details'
        },

        initialize: function () {
            this._super();

            this.isAddressSameAsShipping(true);
            this.isAddressDetailsVisible(true);
        }
    };

    return function (target) {
        if (!window.checkoutConfig.fastCheckout.isEnable) {
            return target;
        }

        return target.extend(mixin);
    };
});
