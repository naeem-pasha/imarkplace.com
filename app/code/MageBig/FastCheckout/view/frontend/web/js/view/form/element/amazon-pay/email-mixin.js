define(['jquery', 'underscore'], function ($, _) {
    'use strict';

    var mixin = {
        defaults: {
            template: 'MageBig_FastCheckout/form/element/amazon-pay/email'
        },

        initialize: function () {
            this._super();
        }
    };

    return function (target) {
        if (!window.checkoutConfig.fastCheckout.isEnable) {
            return target;
        }

        return target.extend(mixin);
    };
});
