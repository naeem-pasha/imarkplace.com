/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
/*browser:true*/
/*global define*/
define(
        [
            'Magento_Checkout/js/view/payment/default',
            'Magento_Checkout/js/action/place-order',
            'mage/url',
        ],
        function (Component, placeOrderAction, url) {
            'use strict';
            console.log(Component);
            return Component.extend({
                defaults: {
                    template: 'Apps_Payfast/payment/payfastpaymentmethod'
                },
                afterPlaceOrder: function () {
                    window.location.replace(url.build('payfast/redirect/'));
                },
                /** Returns send check to info */
                getMailingAddress: function () {
                    return window.checkoutConfig.payment.checkmo.mailingAddress;
                }

            });
        }
);
