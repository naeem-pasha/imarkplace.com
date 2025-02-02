define(
    [
    "jquery",
    "mage/translate",
    "Magento_Ui/js/modal/modal"
    ],
    function ($, $t, modal) {
        "use strict";
        $.widget(
            'mage.affRequest',
            {
                _create: function () {
                    // $('#form-affilait-status, .form-create-account').submit(function () {
                    //     $('#affiliate-confirmation').parent('.control').find('.aff-term').remove();
                    //     if (!$('#affiliate-confirmation').is(':checked')) {
                    //         var required = $('<div />', {'class':'mage-error aff-term', 'generated':'true'})
                    //                                     .text('This is a required field.');
                    //         $('#affiliate-confirmation').parent('.control').append(required);
                    //     }
                    // });
                    $('body').on(
                        'click',
                        '#aff_term_light',
                        function (e) {
                            e.preventDefault();
                            var options = {
                                type: 'popup',
                                responsive: true,
                                innerScroll: true,
                                width:'200px',
                                title: $t('Affiliate Terms'),
                                buttons: [{
                                    text: $.mage.__('Ok'),
                                    class: '',
                                    click: function () {
                                        this.closeModal();
                                    }
                                }]
                            };
                            var cont = $('#mpaff-terms').html();
                            cont = $('<div />').append(cont);
                            modal(options, cont);
                            cont.modal('openModal');
                        }
                    );
                }
            }
        );
        return $.mage.affRequest;
    }
);
