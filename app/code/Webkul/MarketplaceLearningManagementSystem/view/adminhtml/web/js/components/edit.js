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
            registry.get('course_content_edit_form.course_content_edit_form.general.type', function (input) {
                if (parseInt(input.value()) == 2) {
                    registry.get('course_content_edit_form.course_content_edit_form.general.import_file2', function (parent2) {
                        parent2.setVisible(true);
                    });
                    registry.get('course_content_edit_form.course_content_edit_form.general.preview', function (preview) {
                        preview.setVisible(false);
                    });
                } else {
                    registry.get('course_content_edit_form.course_content_edit_form.general.import_file2', function (parent2) {
                        parent2.setVisible(false);
                    });
                    registry.get('course_content_edit_form.course_content_edit_form.general.preview', function (preview) {
                        preview.setVisible(true);
                    });
                }
                input.value.subscribe(function (newValue) {
                    if (parseInt(newValue) == 2) {
                        registry.get('course_content_edit_form.course_content_edit_form.general.import_file2', function (parent2) {
                            parent2.setVisible(true);
                        });
                        registry.get('course_content_edit_form.course_content_edit_form.general.preview', function (preview) {
                            preview.setVisible(false);
                        });
                    } else {
                        registry.get('course_content_edit_form.course_content_edit_form.general.import_file2', function (parent2) {
                            parent2.setVisible(false);
                        });
                        registry.get('course_content_edit_form.course_content_edit_form.general.preview', function (preview) {
                            preview.setVisible(true);
                        });
                    }
                })
            });
            this._super();
        }
    });
});
