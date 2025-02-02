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
    'uiRegistry',
    'Magento_Ui/js/form/element/abstract',
    'mage/url',
    'jquery',
    'Webkul_MarketplaceLearningManagementSystem/js/bin/jquery.form'
], function (_, uiRegistry, abstract, urlBuilder, $) {
    'use strict';

    return abstract.extend({
        initialize: function () {
            this._super();

            $(".content-type> .control > select").change(function(){
                let type = $(this).children("option:selected").val();
                let ext = ($('#importContent').val()).split(".");
                if (( type == 1 && $.inArray(ext[ext.length -1], ['pdf, zip, txt'])) ||
                    ( type == 2 && $.inArray(ext[ext.length -1], ['mp4, ogg']))
                ) {
                    $('.uploader-error').show();
                    $('.mplms-submit').attr('disabled', true);
                } else {
                    $('.uploader-error').hide();
                    $('.mplms-submit').attr('disabled', false);
                }
            });

            return this;
        },
        startUpload: function(e) {
            if($('#importContent').val()) {
                $('#fileuploadForm').ajaxSubmit({ 
                    beforeSubmit: function() {
                        $(".progress-bar").width('0%');
                    },
                    uploadProgress: function (event, position, total, percentComplete){	
                        $(".progress-bar").width(percentComplete + '%');
                        $(".progress-bar").html('<div id="progress-status">' + percentComplete +' %</div>')
                    },
                    success:function (response){
                        response = JSON.parse(response)  
                        console.log(response);

                        if(response.error == '0'){
                            response = JSON.stringify(response); 
                            $('.mplms-submit').attr("disabled", false);
                            $("input[name='import_file']").val(response).trigger('change');
                            $('.file-error').hide();
                        }else{
                            $('.mplms-submit').attr("disabled", true);
                            $('.file-error').show(); 
                            $(".progress-bar").width('0%');  
                            $(".progress-bar").html('<div id="progress-status">' + 0 +' %</div>')
                        }
                    },
                    resetForm: false 
                }); 
            }
        },
        getUrl: function () {
            return urlBuilder.build('mplms/content/upload');
        },
        validateExt: function() {
            if(!$('#importContent').val()) {
                $('.uploader-error').hide();
                return true;
            }
            let ext = ($('#importContent').val()).split(".");
            let contentType = $('#allowed-ext').attr('data-contenttype');
            if (contentType == 1) {
                if(ext[ext.length -1] != "mp4" && ext[ext.length -1] != "ogg") {
                    $('.uploader-error').show();
                    $('#startUpload').attr('disabled', true);
                } else {
                    $('.uploader-error').hide();
                    $('#startUpload').attr('disabled', false);
                }
            } else {
                if(ext[ext.length -1] != "pdf" && ext[ext.length -1] != "zip" && ext[ext.length -1] != "txt") {
                    $('.uploader-error').show();
                    $('#startUpload').attr('disabled', true);
                } else {
                    $('.uploader-error').hide();
                    $('#startUpload').attr('disabled', false);
                }
            }
        }
    });
});
