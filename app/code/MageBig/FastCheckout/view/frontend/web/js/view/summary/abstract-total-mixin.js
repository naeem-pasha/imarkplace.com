define([
        'uiComponent'
    ], function (Component) {
        'use strict';

        var mixin = {
            isFullMode: function () {
                return true;
            }
        };

        return function (target) {
            if (!window.checkoutConfig.fastCheckout.isEnable) {
                return target;
            }

            return target.extend(mixin);
        };
    }
);
