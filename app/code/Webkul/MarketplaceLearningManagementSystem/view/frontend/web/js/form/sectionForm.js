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
            let listing = registry.get('mp_course_section_listing.mp_course_section_listing_data_source');
             
            listing.set('params.t ', Date.now());
            let title = registry.get('mp_course_section_form.mp_course_section_form.general.title').value();  
            let sort_order = parseInt(registry.get('mp_course_section_form.mp_course_section_form.general.sort_order').value());  
            if(title.length > 0 && sort_order >= 0){   
                registry.get('mp_course_section_form.mp_course_section_form.general.title').reset();
                registry.get('mp_course_section_form.mp_course_section_form.general.sort_order').reset();
                $('#course-section-form-model').modal('closeModal'); 
                location.reload();    
            }  
        },
    });
});