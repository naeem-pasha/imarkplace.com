define([
    'underscore',
    './column'
], function (_, Column) {
    'use strict';

    return Column.extend({
        defaults: {
            fieldClass: {
                'wk-mp-grid-attributeset-cell': true
            }
        },
        /*eslint-disable eqeqeq*/
        /**
         * Retrieves label associated with a provided value.
         *
         * @returns {String}
         */
        getLabel: function () {
            var options = this.options || [],
                values = this._super(),
                label = [];

            if (_.isString(values)) {
                values = values.split(',');
            }

            if (!Array.isArray(values)) {
                values = [values];
            }

            values = values.map(function (value) {
                return value + '';
            });

            options.forEach(function (item) {
                if (_.contains(values, item.value + '')) {
                    label.push(item.label);
                }
            });

            return label.join(', ');
        }

        /*eslint-enable eqeqeq*/
    });
});
