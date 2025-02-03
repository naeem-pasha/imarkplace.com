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
 "jquery/ui",
 ], function ($, $t , alert) {
    'use strict';
    $.widget('mage.mpaffiliateRequest', {
        _create: function () {
            var self = this;
            var mpaffiliateReqForm = $('#form-request-massaction');
            mpaffiliateReqForm.mage('validation', {});
            $('#selectall').click(function (event) {
                if (this.checked) {
                    $('[name="mass_ids[]"]').each(function () {
                        this.checked = true;
                    });
                } else {
                    $('[name="mass_ids[]"]').each(function () {
                        this.checked = false;
                    });
                }
            });

            $('body').delegate('#mass-delete-butn','click', function (e) {
                var flag =0;
                $('[name="mass_ids[]"]').each(function () {
                    if (this.checked === true) {
                        flag =1;
                    }
                });
                var selectedVal = $('.mass-action-option').val();
                if (selectedVal=="") {
                    alert({content : $t(' No Action is selected')});
                    return false;
                }
                if (flag === 0) {
                    alert({content : $t(' No Checkbox is checked ')});
                    return false;
                } else {
                    var dicisionapp=confirm($t(" Are you sure to perform this action ? "));
                    if (dicisionapp === true) {
                        $('#form-request-massaction').submit();
                    } else {
                        return false;
                    }
                }
            });
        }
    });
    return $.mage.mpaffiliateRequest;
});
