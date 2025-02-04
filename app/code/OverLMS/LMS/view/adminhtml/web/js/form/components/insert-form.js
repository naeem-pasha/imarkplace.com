define([
    'uiRegistry',
    'Magento_Ui/js/form/components/insert-form',
    'jquery'
], function (registry, Insert, $) {
    'use strict'; 
    return Insert.extend({
        initialize: function () {
            this._super(); 
            $(document).on('click','.action-primary', function(){
                setTimeout(function() { 
                    let call = $('#save-course-section').parent().parent().addClass('page-main-actions'); 
                  }, 300);  
            });  
        },
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
            console.log(responseData);
            if (!responseData.error) {

               // this.popupModal().closeModal(); 
                this.listing().reload({
                    refresh: true
                });

                console.log(this.popupModal().provider);

                if(this.popupModal().provider == "product_form.course_section_data_source") {
                    registry.get('course_section_form.course_section_form.general.title').reset();
                    registry.get('course_section_form.course_section_form.general.sort_order').reset();
                    this.popupModal().state(false);
                    // location.reload();
                } else if(this.popupModal().provider == "product_form.course_content_data_source") {
                    registry.get('course_content_form.course_content_form.general.title').reset();
                    registry.get('course_content_form.course_content_form.general.section').reset();
                    registry.get('course_content_form.course_content_form.general.type').reset();
                    registry.get('course_content_form.course_content_form.general.preview').reset();
                    registry.get('course_content_form.course_content_form.general.import_file2').reset(); 
                    registry.get('course_content_form.course_content_form.general.description').reset();
                    $("#progress-bar").width('0%');
                    this.popupModal().state(false);
                    $('#importContent').val('');  
                }
                // this.popupModal().state(false);
                // location.reload();
            } else {
                $(".message-error").html(responseData.message);
            }
        },
    });
});
