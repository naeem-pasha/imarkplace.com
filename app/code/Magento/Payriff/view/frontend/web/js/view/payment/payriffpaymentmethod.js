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
                type: 'payriff_iframe',
                component: 'Magento_Payriff/js/view/payment/method-renderer/payriffpaymentmethod-iframe'
            }
        );
        return Component.extend({});
    }
);
