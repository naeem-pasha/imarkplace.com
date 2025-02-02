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
    $.widget('mage.userBanner', {
        options: {},
        _create: function () {
            var self = this;
            $(document).ready(function () {
              $('.preview').on('click',function () {
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
                    var cont = $(this).closest("td").prev("td").find(".htmlcode input").val();
                    cont = $('<div />').append(cont);
                    modal(options, cont);
                    cont.modal('openModal');
                });
                
                $(".htmlcode input").on("click",function () {
                    $(this).select();
                });
            });
          }

        });
        return $.mage.userBanner;
    });
