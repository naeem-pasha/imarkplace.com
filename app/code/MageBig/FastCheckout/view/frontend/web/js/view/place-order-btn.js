define([
    'ko',
    'jquery',
    'uiComponent',
    'Magento_Ui/js/lib/view/utils/dom-observer',
    'Magento_Checkout/js/model/totals',
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/action/select-shipping-address',
    'Magento_Checkout/js/action/set-shipping-information',
    'Magento_Checkout/js/model/checkout-data-resolver',
    'Magento_Checkout/js/checkout-data',
    'uiRegistry',
    'Magento_Checkout/js/model/address-converter',
    'Magento_Customer/js/model/customer',
    'Magento_Checkout/js/model/shipping-service',
    'MageBig_FastCheckout/js/model/agreement/agreement-validator',
    'Magento_Checkout/js/model/payment-service',
    'Magento_Customer/js/model/address-list',
    'mage/translate'
], function (
    ko,
    $,
    Component,
    dom,
    totals,
    quote,
    selectShippingAddress,
    setShippingInformationAction,
    checkoutDataResolver,
    checkoutData,
    registry,
    addressConverter,
    customer,
    shippingService,
    agreementValidator,
    paymentService,
    addressList,
    $t
) {
    'use strict';

    let timer,
        checkoutConfig = window.checkoutConfig.fastCheckout;

    return Component.extend({
        defaults: {
            template: 'MageBig_FastCheckout/view/place-order-btn'
        },

        isLoading: totals.isLoading,

        initialize: function () {
            let self = this;
            this._super();

            let fields = ['input', 'select', 'textarea'];

            fields.forEach(function (item) {
                dom.get(item, function (elem) {
                    let $field = $(elem).parents('.field').first(),
                        $label = $field.find('label');

                    if (item === 'textarea') {
                        $(elem).attr('placeholder', ' ');
                    } else if (item === 'input') {
                        $(elem).attr('placeholder', ' ');
                        if ($(elem).attr('name') === 'payment[method]') {
                            $(elem).parents('.payment-method').addClass('payment-' + $(elem)[0].id);
                        } else if ($(elem).attr('type') === 'radio') {
                            let name = $(elem).attr('name');

                            if (!$(elem).attr('id')) {
                                $(elem).attr('id', name);
                            }
                            if (!$(elem).parent('.field').length) {
                                $(elem).wrap('<div class="field"></div>');
                                $(elem).parent('.field').append('<label for"' + name + '"> </label>');
                            }
                        } else if ($(elem).attr('name') === 'fullName') {
                            let $address = $(elem).parents('.address'),
                                defaultFirst = $address.find('[name=firstname]'),
                                defaultLast = $address.find('[name=lastname]'),
                                name;

                            if (!$(elem).val() && (defaultFirst.val() || defaultLast.val())) {
                                if (checkoutConfig.fullName === 2) {
                                    name = defaultFirst.val() + ' ' + defaultLast.val();
                                } else {
                                    name = defaultLast.val() + ' ' + defaultFirst.val();
                                }

                                $(elem).val(name);
                            }

                            $(elem).on('focusout', function () {
                                let fullName = $(this).val().trim(),
                                    nameData = fullName.split(' '),
                                    firstName, lastName;

                                if (checkoutConfig.fullName === 2) {
                                    firstName = nameData.shift();
                                } else {
                                    firstName = nameData.pop();
                                }

                                lastName = nameData.join(' ').trim();

                                if (fullName) {
                                    lastName = lastName ? lastName : $t('User');
                                }

                                $address.find('[name=firstname]').val(firstName).trigger('change');
                                $address.find('[name=lastname]').val(lastName).trigger('change');
                            });
                        } else if ($(elem).attr('name') === 'city' && $(elem).parents('[name="shippingAddress.city"]').length) {
                            $(elem).on('change', function () {
                                self.updateShipping();
                            });
                        }
                    } else if (item === 'select') {
                        if ($(elem).attr('name') === 'region_id') {
                            $(elem).find("option[value='']").text('');
                        }

                        if (!$(elem).find('option:selected').text()) {
                            $label.addClass('no-value');
                        } else {
                            $label.removeClass('no-value');
                        }

                        $(elem).on('change', function () {
                            if ($(elem).attr('name') === 'region_id' && $(elem).parents('[name="shippingAddress.region_id"]').length) {
                                self.updateShipping();
                            }

                            if (!$(this).find('option:selected').text()) {
                                $label.addClass('no-value');
                            } else {
                                $label.removeClass('no-value');
                            }
                        });
                    }

                    if ($label.length && !$(elem).hasClass('discount-code')) {
                        $label.insertAfter($(elem));
                    }
                });
            });

            dom.get('.payment-method', function (elem) {
                $(elem).on('click', function (event) {
                    event.stopPropagation();
                    let $input = $(this).find('input[name="payment[method]"]');

                    if (!$input.prop("checked")) {
                        $input.trigger('click');
                    }
                })
            });

            dom.get('.shipping-address-item', function (elem) {
                $(elem).on('click', function (event) {
                    event.stopPropagation();
                    let $btn = $(this).find('.action-select-shipping-item');

                    if ($(elem).hasClass('not-selected-item')) {
                        $btn.trigger('click');
                    }
                })
            });

            dom.get('.payment-icon', function (elem) {
                $(elem).parents('.payment-method').addClass('has-payment-icon');
            });

            dom.get('.checkout-billing-address', function (elem) {
                dom.get('#checkout-step-shipping', function (co) {
                    $(elem).insertAfter($(co));
                });
            });
        },

        initPlaceOrder: function () {
            let self = this,
                $placeWrap = $('.place-order-wrap');

            $placeWrap.on('click', 'button', function () {
                let attr = $(this).attr('data-btn'),
                    $active = $('.payment-method._active');

                let isValid = self.validateShippingInformation();

                self.hideMessage();

                if (isValid && !quote.isVirtual()) {
                    quote.billingAddress(null);
                    checkoutDataResolver.resolveBillingAddress();
                    registry.async('checkoutProvider')(function (checkoutProvider) {
                        let shippingAddressData = checkoutData.getShippingAddressFromData();
                        if (shippingAddressData) {
                            checkoutProvider.set(
                                'shippingAddress',
                                $.extend(true, {}, checkoutProvider.get('shippingAddress'), shippingAddressData)
                            );
                        }
                    });
                    setShippingInformationAction().done(
                        function () {
                            $active.find('button[data-btn=' + attr + ']').trigger('click');
                        }
                    );
                } else {
                    $active.find('button[data-btn=' + attr + ']').trigger('click');
                }
            });
        },

        updateShipping: function () {
            if (addressList().length === 0) {

                let shippingAddress,
                    addressData,
                    field,
                    shippingComponent = registry.get('checkout.steps.shipping-step.shippingAddress');

                shippingAddress = quote.shippingAddress();
                addressData = addressConverter.formAddressDataToQuoteAddress(
                    shippingComponent.source.get('shippingAddress')
                );

                //Copy form data to quote shipping address object
                for (field in addressData) {
                    if (addressData.hasOwnProperty(field) &&
                        shippingAddress.hasOwnProperty(field) &&
                        typeof addressData[field] != 'function' &&
                        _.isEqual(shippingAddress[field], addressData[field])
                    ) {
                        shippingAddress[field] = addressData[field];
                    } else if (typeof addressData[field] != 'function' &&
                        !_.isEqual(shippingAddress[field], addressData[field])) {
                        shippingAddress = addressData;
                        break;
                    }
                }

                if (customer.isLoggedIn()) {
                    shippingAddress['save_in_address_book'] = 1;
                }

                selectShippingAddress(shippingAddress);
            }
        },

        /**
         * @return {Boolean}
         */
        validateShippingInformation: function () {
            let loginFormSelector = 'form[data-role=email-with-possible-login]',
                emailValidationResult = customer.isLoggedIn();

            if (!customer.isLoggedIn()) {
                $(loginFormSelector).validation();
                emailValidationResult = Boolean($(loginFormSelector + ' input[name=username]').valid());
            }

            let shippingComponent = registry.get('checkout.steps.shipping-step.shippingAddress'),
                field = registry.get('checkout.steps.shipping-step.shippingAddress.shipping-address-fieldset'),
                i = 0, j = 0, isValid = true;

            if (!customer.isLoggedIn()) {
                _.each(field._elems, function (data) {
                    if (data) {
                        if (typeof data === 'object') {
                            if (data.validation !== undefined) {
                                if (!data.validate(false).valid) {
                                    if (i === 0) {
                                        isValid = false;
                                    }
                                    i++;
                                }
                            } else {
                                if (data._elems.length) {
                                    _.each(data._elems, function (item) {
                                        if (item.validation !== undefined) {
                                            if (!item.validate(false).valid) {
                                                if (j === 0) {
                                                    isValid = false;
                                                }
                                                j++;
                                            }
                                        }
                                    })
                                }
                            }
                        }
                    }
                })

                if (!isValid) {
                    shippingComponent.focusInvalid();
                }
            }

            if (!emailValidationResult) {
                $(loginFormSelector + ' input[name=username]').focus();

                return false;
            }

            if (isValid && !quote.isVirtual()) {
                isValid = this.validateShippingMethod();
            }

            if (isValid) {
                isValid = this.validatePaymentMethod();
            }

            if (isValid) {
                isValid = agreementValidator.validate(false);
            }

            if (isValid) {
                isValid = shippingComponent.validateShippingInformation();
            }

            return isValid;
        },

        validateShippingMethod: function () {
            if (quote.isVirtual()) {
                return true;
            }

            let messageContainer = registry.get('checkout.errors').messageContainer;

            if (!quote.shippingMethod()) {
                this.scrollToForm($('#opc-shipping_method'));
                messageContainer.addErrorMessage({
                    message: $t('The shipping method is missing. Select the shipping method and try again.')
                });

                return false;
            }

            return true;
        },

        validatePaymentMethod: function () {
            let messageContainer = registry.get('checkout.errors').messageContainer;

            if (!quote.paymentMethod()) {
                this.scrollToForm($('.opc-payment'));
                messageContainer.addErrorMessage({
                    message: $t('Please choose a payment method.')
                });

                return false;
            }

            return true;
        },

        scrollToForm: function (elm) {
            let $win = $(window),
                windowHeight = $win.innerHeight();

            if ($win.scrollTop() > elm.offset().top) {
                $('html, body').stop().animate({
                    scrollTop: elm.offset().top - 130
                });
            }
        },

        hideMessage: function () {
            if (timer) {
                clearTimeout(timer);
            }

            timer = setTimeout(function () {
                $('div.mage-error, .field-error').fadeOut();
            }, 5000)
        }
    });
});
