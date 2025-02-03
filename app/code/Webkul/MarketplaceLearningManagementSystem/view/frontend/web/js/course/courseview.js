/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_MarketplaceLearningManagementSystem
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
require([
    'jquery',
], function ($) {
    $('#description-tab').css('border-bottom','2px solid #006bb4').css('color','#006bb4');
    $('#course-overview-tab-data').hide();
    $('#course-qna-tab-data').hide();

    $('#description-tab').click(function(){
        $('#course-description-tab-data').show();
        $('#course-overview-tab-data').hide();
        $('#course-qna-tab-data').hide();
    });

    $('#overview-tab').click(function(){
        $('#course-description-tab-data').hide();
        $('#course-overview-tab-data').show();
        $('#course-qna-tab-data').hide();
    });

    $('#qna-tab').click(function(){
        $('#course-description-tab-data').hide();
        $('#course-overview-tab-data').hide();
        $('#course-qna-tab-data').show();
    });

    $('.details-tab').click(function(){
        $('.details-tab').each(function(){

            $('#qna-tab').css('border-bottom','none').css('color','#333333');
            $('#overview-tab').css('border-bottom','none').css('color','#333333');
            $('#description-tab').css('border-bottom','none').css('color','#333333');
        })
        $(this).css('border-bottom','2px solid #006bb4').css('color','#006bb4');
    });

    $('.content-item').click(function(){
        let url = $(this).attr('data-content-url');
        (url) ? window.location = url : ''; 
        
    });

    $('.section-column').each(function(index) {
        if(!index) {
            $(this).find('.section-collapsible-icon').css('transform','rotate(180deg)');
        }
    });

    $('#course-content-accordion .section-column').click(function(){
        $('.section-column').each(function(index) {
            $(this).find('.section-collapsible-icon').css('transform','rotate(180deg)');
        });
        $('.section-column.active').each(function(index) {
            $(this).find('.section-collapsible-icon').css('transform','rotate(0deg)');
        });
    });

    let video = document.getElementById("player");
    video.onended = function() {
        let courseId = $('#video-player-container').attr('data-courseId');
        let contentId = $('#video-player-container').attr('data-contentId');
        let url = $('#video-player-container').attr('data-baseurl') + "mplms/coursestatus/update";

        $.ajax({
            url : url,
            type: 'POST',
            dataType: 'json',
            data : {
                courseId: courseId, 
                contentId: contentId
            },
            complete: function (response) {
                return true;
            },
            error: function (response) {
                return true;
            }
        });

    };
});