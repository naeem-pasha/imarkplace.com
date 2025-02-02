/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
/*browser:true*/
/*global define*/
define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/payment/additional-validators',
        'Paypro_PayproCheckout/js/model/isPhoneValid'
    ],
    function (Component, additionalValidators, gmailValidation) {
        'use strict';
        additionalValidators.registerValidator(gmailValidation);
        return Component.extend({});
    }
);
