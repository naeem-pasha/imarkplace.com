define(
    [
    "jquery",
    "mage/translate",
    "Magento_Ui/js/modal/modal",
    ],
    function ($, $t, modal) {
        "use strict";
        $.widget(
            'affiliate.preference',
            {
                _create: function () {
                    var methodOpts = this.options;
                    $('body').delegate(
                        '#payment_method',
                        'change',
                        function () {
                            $('#payment-data').html($('#'+$(this).val()+'-template').clone().html());
                            $.each(
                                methodOpts['account_data'],
                                function (index, value) {
                                    $('#'+index).val(value);
                                }
                            );
                        }
                    );
                    $('#payment_method').val(methodOpts['payment_method']).trigger('change');
                }
            }
        );
        return $.affiliate.preference;
    }
);