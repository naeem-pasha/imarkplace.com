/**
 * Webkul mpaffiliate js.
 * @category Webkul
 * @package Webkul_MpAffiliate
 * @author Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license https://store.webkul.com/license.html
 */
/*jshint jquery:true*/
 define([
 "jquery",
 'mage/translate',
 'Magento_Ui/js/modal/alert',
 'Magento_Ui/js/modal/modal',
 "jquery/ui",
 ], function ($,$t,alert,modal) {
    'use strict';
    $.widget('mage.mpaffiliateTextBanner', {
        options: {},
        _create: function () {
            var self = this;
            var dataForm = $('#form-customer-attr-new');
            dataForm.mage('validation', {});
            $(document).ready(function () {
                $('.save-data').click(function () {
                    $('body').trigger('processStart');
                    $(this.getAttribute('id')).text('Approve');
                    var selfButton = this;
                    $.post(
                        self.options.save_banner,
                        {
                            actiontype : this.getAttribute('data-value'),
                            affiliateid : this.getAttribute('data-affiliate-id'),
                            requestid : this.getAttribute('id')
                        }
                    ).done(function () {
                        $('body').trigger('processStop');
                        var id = $(selfButton).parents().parents().attr('id');
                        if ($(selfButton).attr('data-value') == 1) {
                            $('#'+$(selfButton).attr("id")+" button.appunapp span span").text('Unapprove');
                            $('#status'+id).text('Approved')
                            $(selfButton).attr("data-value",0);
                        } else if ($(selfButton).attr('data-value') == 0) {
                            $('#'+$(selfButton).attr("id")+" button.appunapp span span").text('Approve');
                            $('#status'+id).text('Unapproved');
                            $(selfButton).attr("data-value",1);
                        } else if ($(selfButton).attr('data-value') == 2) {
                            $(selfButton).closest("tr").remove();
                            if ($("#banner-table tr").size()==0) {
                                $("#all-banner").remove();
                            }
                        }
                        
                    });
                });
                $('.prevspc').on('click',function () {
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
                    var id = ($(this).parents()).parents().attr('id');
                    console.log(id);
                    var cont = $(this).parents('.wk-row-view').find('#diw'+id).val();
                    console.log(cont);
                    cont = $('<div />').append(cont);
                    modal(options, cont);
                    cont.modal('openModal');
                });

            });
          }

        });
        return $.mage.mpaffiliateTextBanner;
    });
