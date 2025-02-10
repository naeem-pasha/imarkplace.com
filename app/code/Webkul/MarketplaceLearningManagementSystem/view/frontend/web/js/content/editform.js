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
    'Magento_Ui/js/form/element/select',
    'jquery'
], function (registry, Select, $) {
    'use strict';
    return Select.extend({
        initialize: function () {            
            var self = this;
            registry.get('mp_course_content_edit_form.mp_course_content_edit_form.general.type', function (input) {
                if (parseInt(input.value()) == 2) {
                    $(document).find('.allowed-ext').html('Allowed Extension: pdf, zip, txt');
                    $(document).find('#allowed-ext').attr('data-contenttype','2');
                    registry.get('mp_course_content_edit_form.mp_course_content_edit_form.general.preview', function (preview) {
                        preview.setVisible(false);
                    });
                } else {
                    $(document).find('.allowed-ext').html('Allowed Extension: mp4, ogg');
                    $(document).find('#allowed-ext').attr('data-contenttype','1');
                    registry.get('mp_course_content_edit_form.mp_course_content_edit_form.general.preview', function (preview) {
                        preview.setVisible(true);
                    });
                }
                input.value.subscribe(function (newValue) {
                    if($("input[name='import_file']").val() == '') {
                        $('.mplms-submit').attr("disabled", true);
                    }
                    if (parseInt(newValue) == 2) {
                        $(document).find('.allowed-ext').html('Allowed Extension: pdf, zip, txt');
                        $(document).find('#allowed-ext').attr('data-contenttype','2');
                        registry.get('mp_course_content_edit_form.mp_course_content_edit_form.general.preview', function (preview) {
                            preview.setVisible(false);
                        });
                    } else {
                        $(document).find('.allowed-ext').html('Allowed Extension: mp4, ogg');
                        $(document).find('#allowed-ext').attr('data-contenttype','1');
                        registry.get('mp_course_content_edit_form.mp_course_content_edit_form.general.preview', function (preview) {
                            preview.setVisible(true);
                        });
                    }
                })
            });
            this._super();
        }
    });
});
