define([
    'jquery',
    'uiComponent',
    'underscore',
    'MageBig_FastCheckout/js/action/update-cart-item',
    'Magento_Checkout/js/model/totals'
], function ($, Component, _, updateQuoteItemAction, totals) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'MageBig_FastCheckout/view/summary/item/qty-increment'
        },
        defaultQty: null,
        isEditing: false,
        timer: null,

        /**
         * On increment quantity event handler
         *
         * @param {Object} item
         */
        qtyIncrement: function (item, event) {
            let self = this,
                defaultQty = item.qty;

            if (item.extension_attributes && item.extension_attributes.mb_qty_increment) {
                item.qty += item.extension_attributes.mb_qty_increment;
            } else {
                item.qty++;
            }

            $(event.currentTarget).prev().val(item.qty);

            this._performUpdateQtyAction(item, defaultQty);
        },

        /**
         * On decrement quantity click event handler
         *
         * @param {Object} item
         */
        qtyDecrement: function (item, event) {
            let qty = item.qty,
                defaultQty = qty;

            if (item.extension_attributes && item.extension_attributes.mb_qty_increment) {
                qty -= item.extension_attributes.mb_qty_increment;
            } else {
                qty--;
            }
            if (qty > 0) {
                item.qty = qty;
                $(event.currentTarget).next().val(item.qty);
                this._performUpdateQtyAction(item, defaultQty);
            }
        },

        /**
         * On quantity input focusin event handler
         *
         * @param {Object} item
         */
        onQtyFocusIn: function (item) {
            this.defaultQty = item.qty;
            this.isEditing = true;
        },

        /**
         * On quantity input focusout event handler
         *
         * @param {Object} item
         */
        onQtyFocusOut: function (item) {
            this.updateQtyValue(item);
        },

        /**
         * On quantity input key up event handler
         *
         * @param {Object} item
         * @param {Object} event
         */
        onQtyKeyUp: function (item, event) {
            let keyCode = (event.which ? event.which : event.keyCode);

            if (keyCode === 13) {
                this.updateQtyValue(item);
            }
        },

        /**
         * Update qty value
         *
         * @param {Object} item
         */
        updateQtyValue: function (item) {
            let qty = item.qty,
                self = this;

            if (this._isManuallyUpdateAllowed(qty)) {
                self._performUpdateQtyAction(item, this.defaultQty);
            } else if (!this._isValidQty(qty)) {
                self._restore(item, this.defaultQty);
            }
            this.isEditing = false;
        },

        /**
         * Check if manually quantity update is allowed
         *
         * @param {string} qty
         * @returns {boolean}
         */
        _isManuallyUpdateAllowed: function (qty) {
            let isChanged = (this.defaultQty != qty);

            return this.isEditing && isChanged && this._isValidQty(qty);
        },

        /**
         * Check if quantity is valid
         *
         * @param {string|int}qty
         * @returns {boolean}
         */
        _isValidQty: function (qty) {
            qty = parseInt(qty);
            return qty
                && (qty - 0 == qty)
                && (qty - 0 > 0);
        },

        /**
         * Perform update quantity action
         *
         * @param {Object} item
         * @param {Number} originalQty
         */
        _performUpdateQtyAction: function (item, originalQty) {
            let self = this;

            if (self.timer) {
                clearTimeout(self.timer);
                self.timer = null;
            }
            self.timer = setTimeout(function () {
                updateQuoteItemAction(item).fail(function () {
                    self._restore(item, originalQty);
                });
            }, 1000);
        },

        /**
         * Restore original quantity value
         *
         * @param {Object} item
         * @param {Number} originalQty
         */
        _restore: function (item, originalQty) {
            let items = totals.getItems(),
                itemsDataUpdate = [];

            _.each(items(), function (itemData) {
                let dataToUpdate = _.clone(itemData);

                if (dataToUpdate.item_id === item.item_id) {
                    dataToUpdate.qty = originalQty;
                }
                itemsDataUpdate.push(dataToUpdate);
            });
            items(itemsDataUpdate);
        }
    });
});
