define([
    'jquery',
    'ko',
    'Magento_Checkout/js/checkout-data',
    'Magento_Checkout/js/model/quote',
    'Magento_Customer/js/action/check-email-availability'
], function ($, ko, checkoutData, quote, checkEmailAvailability) {
    'use strict';

    var mixin = {
        defaults: {
            template: 'MageBig_FastCheckout/form/element/email',
            showLoginForm: parseInt(window.checkoutConfig.fastCheckout.showLoginForm)
        },

        showLoginPassword: function () {
            if (this.showLoginForm === 3) {
                $('.action-auth-toggle').trigger('click');
            } else {
                if (this.isPasswordVisible()) {
                    this.isPasswordVisible(false);
                } else {
                    this.isPasswordVisible(true);
                }
            }
        },

        resolveInitialPasswordVisibility: function () {
            if (this.showLoginForm === 0 || this.showLoginForm === 3) {
                return false;
            }

            if (this.showLoginForm === 1) {
                return true;
            }

            if (checkoutData.getInputFieldEmailValue() !== '' && checkoutData.getCheckedEmailValue() !== '') {
                return true;
            }

            if (checkoutData.getInputFieldEmailValue() !== '') {
                return checkoutData.getInputFieldEmailValue() === checkoutData.getCheckedEmailValue();
            }

            return false;
        },

        isEmailAvailable: function () {
            return checkoutData.getCheckedEmailValue();
        },

        emailHasChanged: function () {
            let self = this,
                isValidated = checkoutData.getCheckedEmailValue() && self.email() &&
                    checkoutData.getCheckedEmailValue() === self.email();

            if (!isValidated) {
                clearTimeout(this.emailCheckTimeout);

                if (self.validateEmail()) {
                    quote.guestEmail = self.email();
                    checkoutData.setValidatedEmailValue(self.email());
                }
                this.emailCheckTimeout = setTimeout(function () {
                    if (self.validateEmail()) {
                        self.checkEmailAvailability();
                    } else {
                        if (self.showLoginForm === 2) {
                            self.isPasswordVisible(false);
                        }
                    }
                }, self.checkDelay);
            } else {
                if (self.showLoginForm === 2) {
                    self.isPasswordVisible(true);
                }
            }

            checkoutData.setInputFieldEmailValue(self.email());
        },

        /**
         * Check email existing.
         */
        checkEmailAvailability: function () {
            this.validateRequest();
            this.isEmailCheckComplete = $.Deferred();
            $('#customer-email-fieldset').addClass('isLoading');
            this.checkRequest = checkEmailAvailability(this.isEmailCheckComplete, this.email());

            $.when(this.isEmailCheckComplete).done(function () {
                if (self.showLoginForm === 2) {
                    this.isPasswordVisible(false);
                }
                checkoutData.setCheckedEmailValue('');
            }.bind(this)).fail(function () {
                if (self.showLoginForm === 2) {
                    this.isPasswordVisible(true);
                }
                checkoutData.setCheckedEmailValue(this.email());
            }.bind(this)).always(function () {
                $('#customer-email-fieldset').removeClass('isLoading');
            }.bind(this));
        }
    };

    return function (target) {
        if (!window.checkoutConfig.fastCheckout.isEnable) {
            return target;
        }

        return target.extend(mixin);
    };
});
