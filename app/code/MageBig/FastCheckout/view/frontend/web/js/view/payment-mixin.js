define(['jquery'], function ($) {
    'use strict';

    var mixin = {
        initialize: function () {
            this._super();
            this.isVisible = true;
        }
    };

    return function (target) {
        if (!window.checkoutConfig.fastCheckout.isEnable) {
            return target;
        }

        return target.extend(mixin);
    };
});
