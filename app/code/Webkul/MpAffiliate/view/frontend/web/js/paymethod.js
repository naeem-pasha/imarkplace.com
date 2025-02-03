define(
    [
    "jquery",
    "mage/translate",
    'mage/template',
    "Magento_Ui/js/modal/modal"
    ],
    function ($, $t, mageTemplate, modal) {
        "use strict";
        $.widget(
            'mpaffiliate.paymethod',
            {
                options: {},
                _create: function () {
                  var self = this;
                  var importOpts = this.options;
                  var modalHtml = "";
                  var transSaveAction = self.options.url;
                  var paypalinfo = self.options.paypalinfo;
                  
                    $('body').delegate(
                        '.payment',
                        'click',
                        function () {
                            var clickedButton =this;
                            var paymentMethod = $(clickedButton).data("payment");
                            switch (paymentMethod['payment_method']) {
                                case 'banktransfer':
                                    modalHtml = mageTemplate(
                                        "#banktransfer-template",
                                        {
                                            formaction: transSaveAction,
                                            affid: $(clickedButton).data("affid"),
                                            balance: $(clickedButton).data("balance"),
                                            accountHolder: paymentMethod['account_data']['account_holder'],
                                            bankName:paymentMethod['account_data']['bank_name'],
                                            accountNumber:paymentMethod['account_data']['account_number'],
                                            bankAddress:paymentMethod['account_data']['bank_address'],
                                            code:paymentMethod['account_data']['code'],
                                        }
                                    );
                                    var options = {
                                        type: 'popup',
                                        responsive: true,
                                        innerScroll: true,
                                        width:'200px',
                                        title: $t('PAYMENT METHOD DETAILS'),
                                        buttons: [{
                                            text: $.mage.__('SAVE TRANSACTION DETAILS'),
                                            class: 'transaction-button',
                                            click: function () {
                                                $('.transaction_amount').removeClass('mage-error');
                                                var transbutton = this._getElem($('.transaction-button'));
                                                transbutton.prevAll('.message').remove();
                                                if(this._getElem($('.transaction_amount')).val() > 0){
                                                    $('body').trigger('processStart');
                                                    // console.log(this._getElem($('.transaction_amount')).val());return false;
                                                    jQuery.ajax(
                                                        {
                                                            url: transSaveAction,
                                                            data: {
                                                                form_key: window.FORM_KEY,
                                                                aff_customer_id:$(clickedButton).data("affid"),
                                                                transaction_amount : this._getElem($('.transaction_amount')).val(),
                                                                payment_method : paymentMethod['payment_method'],
                                                                transaction_id : this._getElem($('.transaction_id')).val(),
                                                                transaction_data: JSON.stringify(paymentMethod['account_data']),
                                                            },
                                                            type: 'POST',
                                                            success: function (responsedata) {
                                                                $('body').trigger('processStop');
                                                                if (responsedata['status']) {
                                                                    transbutton.before(
                                                                        $('<span />', {'class':'message-success success message wk-mpaffiliate'})
                                                                        .append($('<span />').text(responsedata['msg']))
                                                                    );
                                                                    location.reload();
                                                                } else {
                                                                    transbutton.before(
                                                                        $('<span />', {'class':'message-error error message wk-mpaffiliate'})
                                                                        .append($('<span />').text(responsedata['msg']))
                                                                    );
                                                                }
                                                            },
                                                            error: function (error) {
                                                                $('body').trigger('processStop');
                                                                transbutton.before(
                                                                    $('<span />', {'class':'message-error error message wk-mpaffiliate'})
                                                                    .append($('<span />').text(error))
                                                                );
                                                            }
                                                        }
                                                    ).done(
                                                        function () {
                                                            transbutton.prev('.loader').remove();
                                                        }
                                                    );
                                                } else {
                                                    $('.transaction_amount').addClass('mage-error');
                                                    transbutton.before(
                                                        $('<span />', {'class':'message-error error message wk-mpaffiliate'})
                                                        .append($('<span />').text($t('Amount is not greater then zero')))
                                                    );
                                                    return false;
                                                }
                                            }
                                        }]
                                    };
                                    var cont = modalHtml;
                                    cont = $('<div />').append(cont);
                                    modal(options, cont);
                                    cont.modal('openModal');
                                    break;
                                case 'checkmo':
                                    modalHtml = mageTemplate(
                                        "#checkmo-template",
                                        {
                                            formaction: self.options.url,
                                            affid: $(clickedButton).data("affid"),
                                            balance: $(clickedButton).data("balance"),
                                            payableTo: paymentMethod['account_data']['payable_to'],
                                        }
                                    );
                                    var options = {
                                        type: 'popup',
                                        responsive: true,
                                        innerScroll: true,
                                        width:'200px',
                                        title: $t('PAYMENT METHOD DETAILS'),
                                        buttons: [{
                                            text: $.mage.__('SAVE TRANSACTION DETAILS'),
                                            class: 'transaction-button',
                                            click: function () {
                                                $('.transaction_amount').removeClass('mage-error');
                                                var transbutton = this._getElem($('.transaction-button'));
                                                transbutton.prevAll('.message').remove();
                                                if(this._getElem($('.transaction_amount')).val() > 0){
                                                    $('body').trigger('processStart');
                                                    jQuery.ajax(
                                                        {
                                                            url: transSaveAction,
                                                            data: {
                                                                form_key: window.FORM_KEY,
                                                                aff_customer_id:$(clickedButton).data("affid"),
                                                                transaction_id : this._getElem($('.transaction_id')).val(),
                                                                transaction_amount : this._getElem($('.transaction_amount')).val(),
                                                                payment_method : paymentMethod['payment_method'],
                                                                transaction_data: JSON.stringify(paymentMethod['account_data']),
                                                            },
                                                            type: 'POST',
                                                            success: function (responsedata) {
                                                                $('body').trigger('processStop');
                                                                if (responsedata['status']) {
                                                                    transbutton.before(
                                                                        $('<span />', {'class':'message-success success message wk-mpaffiliate'})
                                                                        .append($('<span />').text(responsedata['msg']))
                                                                    );
                                                                    location.reload();
                                                                } else {
                                                                    transbutton.before(
                                                                        $('<span />', {'class':'message-error error message wk-mpaffiliate'})
                                                                        .append($('<span />').text(responsedata['msg']))
                                                                    );
                                                                }
                                                            },
                                                            error: function (error) {
                                                                $('body').trigger('processStop');
                                                                transbutton.before(
                                                                    $('<span />', {'class':'message-error error message wk-mpaffiliate'})
                                                                    .append($('<span />').text(error))
                                                                );
                                                            }
                                                        }
                                                    ).done(
                                                        function () {
                                                            transbutton.prev('.loader').remove();
                                                        }
                                                    );
                                                }else{
                                                    $('.transaction_amount').addClass('mage-error');
                                                    transbutton.before(
                                                        $('<span />', {'class':'message-error error message wk-mpaffiliate'})
                                                        .append($('<span />').text($t('Amount is not greater then zero')))
                                                    );
                                                    return false;
                                                }
                                            }
                                        }]
                                    };
                                    var cont = modalHtml;
                                    cont = $('<div />').append(cont);
                                    modal(options, cont);
                                    cont.modal('openModal');
                                    break;
                                case 'paypal_standard':
                                    modalHtml = mageTemplate(
                                        "#paypal_standard-template",
                                        {
                                            urlFix : paypalinfo["urlFix"],
                                            currentCurrencyCode : paypalinfo["currentCurrencyCode"],
                                            returnurl: paypalinfo["returnurl"],
                                            cancelReturn: paypalinfo["cancelReturn"],
                                            ipnnotify: paypalinfo["ipnnotify"],
                                            affEmail: paymentMethod['account_data']['paypal_email'],
                                            affFirstName : $(clickedButton).data("afffirstname"),
                                            affLastName : $(clickedButton).data("afflastname"),
                                            affid : $(clickedButton).data("affid"),
                                            ammounttopay : $(clickedButton).data("balance").substr(1),
                                            balance : $(clickedButton).data("formatted-balance"),
                                            invoice : Math.floor((Math.random() * 10000000) + 1)+'-'+$(clickedButton).data("affid")
                                        }
                                    );
                                    var options = {
                                        type: 'popup',
                                        responsive: true,
                                        innerScroll: true,
                                        width:'200px',
                                        title: $t('PAYMENT METHOD DETAILS'),
                                        buttons: []
                                    }
                                    var cont = modalHtml;
                                    cont = $('<div />').append(cont);
                                    modal(options, cont);
                                    cont.modal('openModal');
                                    break;
                                default :
                                    alert($.mage.__('Payment method is not set by user'));
                            }
                        }
                    );
                }
            }
        );
        return $.mpaffiliate.paymethod;
    }
);
