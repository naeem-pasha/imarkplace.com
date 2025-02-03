/**
 * Webkul mpaffiliate js.
 * @category Webkul
 * @package Webkul_MpAffiliate
 * @author Webkul
 * @license https://store.webkul.com/license.html
 */
/*jshint jquery:true*/
 define([
 "jquery",
 'mage/translate',
 "jquery/ui",
 ], function ($,$t) {
    'use strict';
    $.widget('mage.mpaffiliateCatcommission', {
        options: {},
        _create: function () {
            var self = this;
            // var dataForm = $('#form-customer-attr-new');
            // dataForm.mage('validation', {});
            $(document).ready(function () {
                $("#catcommissionform").on("submit", function () {
                    if ($("[name=category_id]").length!=0) {
                        return true;
                    } else {
                        var message = '<div for="category_id" generated="true" class="mage-error" id="category_id-error">Please select an Category.</div>'
                        $("#category-list").after(message);
                        return false;
                    }
                });
            });
          }

        });
        return $.mage.mpaffiliateCatcommission;
    });
