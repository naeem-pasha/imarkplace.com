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
    'underscore',
    'Magento_Ui/js/form/form',
    'jquery',
    'Magento_Ui/js/modal/modal',
    'uiRegistry',
], function (_, Form, $, modal, registry) {
    'use strict';
    return Form.extend({
        /** @inheritdoc */
        initialize: function () {
            this._super();
            return this;
        },
        /** @inheritdoc */
        save: function (redirect, data) {
            this._super(redirect, data);
           
            let listing = registry.get('mp_course_content_listing.mp_course_content_listing_data_source');
            listing.set('params.t ', Date.now());

            let title = registry.get('mp_course_content_form.mp_course_content_form.general.title').value();
            let section =  registry.get('mp_course_content_form.mp_course_content_form.general.section').value();
            let type = registry.get('mp_course_content_form.mp_course_content_form.general.type').value();
            let preview = registry.get('mp_course_content_form.mp_course_content_form.general.preview').value();
            let description = registry.get('mp_course_content_form.mp_course_content_form.general.description').value();

       if(title.length > 0 && description.length > 0 && section > 0 && type > 0 && preview > 0 ){
            registry.get('mp_course_content_form.mp_course_content_form.general.title').reset();
            registry.get('mp_course_content_form.mp_course_content_form.general.section').reset();
            registry.get('mp_course_content_form.mp_course_content_form.general.type').reset();
            registry.get('mp_course_content_form.mp_course_content_form.general.preview').reset();
            registry.get('mp_course_content_form.mp_course_content_form.general.description').reset();
            registry.get('mp_course_content_form.mp_course_content_form.general.file_uploader').reset();

            $('#course-content-form-model').modal('closeModal');
            $('#importContent').val('');
            $(".progress-bar").width(0 + '%');
            $(".progress-bar").html('<div id="progress-status">' + 0 +' %</div>'); 
       } 
            
        },
    });
});