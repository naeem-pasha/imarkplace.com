define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/payment/renderer-list'
    ],
    function (
        Component,
        rendererList
    ) {
        'use strict';
        rendererList.push(
            {
                type: 'meezan_bank',
                component: 'Meezan_MeezanBank/js/view/payment/method-renderer/meezan_bank'
            }
        );
        return Component.extend({});
    }
);