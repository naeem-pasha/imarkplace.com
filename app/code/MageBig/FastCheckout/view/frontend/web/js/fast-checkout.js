define(['jquery',], function ($) {
    'use strict';

    $('.button-login').on('click', function () {
        $('.action-auth-toggle').trigger('click');
    })

    $('.btn-back-to-cart').on('click', function () {
        window.location.href = window.checkoutConfig.cartUrl;
    })
});
