define([
        'ko',
        'jquery',
        'uiComponent',
        'Magento_Checkout/js/model/quote'
    ], function (
        ko,
        $,
        Component,
        quote
    ) {
        'use strict';

        var mixin = {
            isChecked: ko.computed(function () {
                let paymentMethod = quote.paymentMethod() ? quote.paymentMethod().method : null;

                if (paymentMethod) {
                    let timerId = setInterval(function () {
                        let $action = $('.payment-method._active'),
                            $actionToolbar = $action.find('.actions-toolbar'),
                            $actionBtn = $actionToolbar.find('button');

                        if (!$actionToolbar.length) {
                            $actionBtn = $action.find('button');
                        }

                        if ($actionBtn.length) {
                            $actionBtn.each(function (index) {
                                $(this).attr('data-btn', 'place-order-' + index);
                            })

                            let $placeWrap = $('.place-order-wrap'),
                                $btn = $actionToolbar.length ? $actionToolbar.clone() : $actionBtn.clone();

                            $placeWrap.html($btn);

                            clearInterval(timerId);
                        }
                    }, 200);
                }

                return paymentMethod;
            })
        };

        return function (target) {
            if (!window.checkoutConfig.fastCheckout.isEnable) {
                return target;
            }

            return target.extend(mixin);
        };
    }
);
