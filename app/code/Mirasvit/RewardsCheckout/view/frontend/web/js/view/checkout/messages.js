define([
    'Magento_Ui/js/view/messages',
    'Mirasvit_RewardsCheckout/js/model/messages'
], function (Component, messageContainer) {
    'use strict';

    return Component.extend({


        initialize: function (config) {
            return this._super(config, messageContainer);
        }
    });
});