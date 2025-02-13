define([
    'jquery'
], function ($) {
    'use strict';

    let timer,
        timerForm;

    let validationCheckoutMixin = {
        options: {
            errorPlacement: function (error, element) {
                var errorPlacement = element,
                    fieldWrapper;

                // logic for date-picker error placement
                if (element.hasClass('_has-datepicker')) {
                    errorPlacement = element.siblings('button');
                }
                // logic for field wrapper
                fieldWrapper = element.closest('.addon');

                if (fieldWrapper.length) {
                    errorPlacement = fieldWrapper.after(error);
                }
                //logic for checkboxes/radio
                if (element.is(':checkbox') || element.is(':radio')) {
                    errorPlacement = element.parents('.control').children().last();

                    //fallback if group does not have .control parent
                    if (!errorPlacement.length) {
                        errorPlacement = element.siblings('label').last();
                    }
                }
                //logic for control with tooltip
                if (element.siblings('.tooltip').length) {
                    errorPlacement = element.siblings('.tooltip');
                }
                //logic for select with tooltip in after element
                if (element.next().find('.tooltip').length) {
                    errorPlacement = element.next();
                }
                errorPlacement.after(error);

                if (timer) {
                    clearTimeout(timer);
                }

                timer = setTimeout(function () {
                    $(error).fadeOut();
                }, 5000)
            }
        },
        _listenFormValidate: function () {
            this._super();

            $('form').on('invalid-form.validate', function (event, validation) {
                if (timerForm) {
                    clearTimeout(timerForm);
                }

                timerForm = setTimeout(function () {
                    $('div.mage-error').fadeOut();
                }, 5000)
            });
        }
    };

    return function () {
        $.widget('mage.validation', $.mage.validation, validationCheckoutMixin);

        return $.mage.validation;
    };
});
