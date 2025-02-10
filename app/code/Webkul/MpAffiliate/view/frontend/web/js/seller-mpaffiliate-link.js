/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_MpAffiliate
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
/*jshint jquery:true*/
define([
        "jquery",
        'mage/translate',
        "mage/template",
        "mage/mage",
    ], function ($, $t,mageTemplate, alert) {
        'use strict';
        $.widget('mage.sellerAffiliateLink', {
            options: {
                mainMenu : '.main-menu.mpaffiliate-marketplace-navigation'
            },
            _create: function () {
                var self = this;
                $('body').on('mouseover', self.options.mainMenu, function () {
                    $('#mpaffiliate_block').show();
                });
                $('body').on('mouseleave', self.options.mainMenu, function () {
                    $('#mpaffiliate_block').hide();
                });
            },
        });
        return $.mage.sellerAffiliateLink;
    });
