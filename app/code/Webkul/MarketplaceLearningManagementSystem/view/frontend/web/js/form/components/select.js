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
    'uiRegistry',
    'Magento_Ui/js/form/element/select'
], function (registry, Select) {
    'use strict';
    return Select.extend({
        initialize: function () {
            var self = this;
            registry.get('mp_course_content_form.mp_course_content_form.general.type', function (input) {
                if (parseInt(input.value()) == 2) {
                    registry.get('mp_course_content_form.mp_course_content_form.general.import_file2', function (parent2) {
                        parent2.setVisible(true);
                    });
                } else {
                    registry.get('mp_course_content_form.mp_course_content_form.general.import_file2', function (parent2) {
                        parent2.setVisible(false);
                    });
                }
                input.value.subscribe(function (newValue) {
                    if (parseInt(newValue) == 2) {
                        registry.get('mp_course_content_form.mp_course_content_form.general.import_file2', function (parent2) {
                            parent2.setVisible(true);
                        });
                    } else {
                        registry.get('mp_course_content_form.mp_course_content_form.general.import_file2', function (parent2) {
                            parent2.setVisible(false);
                        });
                    }
                })
            });
            this._super();
        }
    });
});
