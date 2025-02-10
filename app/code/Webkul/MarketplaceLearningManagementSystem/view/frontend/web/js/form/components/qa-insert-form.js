/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_MarketplaceLearningManagementSystem
 * @auther    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
define([
    'Magento_Ui/js/form/components/insert-form',
    'uiRegistry'
], function (Insert, registry) {
    'use strict';

    return Insert.extend({
        defaults: {
            listens: {
                responseData: 'onResponse'
            },
            modules: {
                listing: '${ $.listingProvider }',
                popupModal: '${ $.modalProvider }'
            }
        },

        /**
         * Close modal, reload customer address listing and save customer address
         *
         * @param {Object} responseData
         */
        onResponse: function (responseData) {
            if (!responseData.error) {
                this.popupModal().closeModal();
                this.listing().reload({
                    refresh: true
                });
                this.popupModal().state(false);
            }
        },
    });
});
