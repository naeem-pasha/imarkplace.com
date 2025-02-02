/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
/*browser:true*/
/*global define*/
define(
    [
		'jquery',
        'Magento_Checkout/js/view/payment/default',
        'mage/url',
        'Magento_Customer/js/customer-data'
    ],
    function ($, Component, url, customerData) {
        'use strict';
        return Component.extend({
			redirectAfterPlaceOrder: false,
            defaults: {
                template: 'Paypro_PayproCheckout/payment/payprocheckout'
            },
			afterPlaceOrder: function () {
				$.mage.redirect(url.build('payprocheckout/index/index'));
            }
        });
    }
);
