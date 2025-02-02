<!--
/**
 * Webkul Affiliate banner list script
 * @category  Webkul
 * @package   Webkul_Affiliate
 * @author    Webkul
 * @copyright Copyright (c)  Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
-->
define(
    [
    "jquery",
    "mage/translate",
    "Magento_Ui/js/modal/modal"
    ],
    function ($, $t, modal) {
        "use strict";
        $.widget(
            'mpaffiliate.bannerlist',
            {
                _create: function () {
                    var importOpts = this.options;
                    $('body').delegate(
                        '.button.prev',
                        'click',
                        function () {
                          console.log('shishir');
                            var options = {
                                type: 'popup',
                                responsive: true,
                                innerScroll: true,
                                width:'200px',
                                title: $t('Banner Preview'),
                                buttons: [{
                                    text: $.mage.__('Ok'),
                                    class: '',
                                    click: function () {
                                        this.closeModal();
                                    }
                                }]
                            };
                            var cont = $(this).parents('.wk-row-view').find('.htmlcode').val();
                            cont = $('<div />').append(cont);

                            modal(options, cont);
                            cont.modal('openModal');
                        }
                    );
                }
            }
        );
        return $.mpaffiliate.bannerlist;
    }
);
