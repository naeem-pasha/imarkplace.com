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
    'accordion'
], function ($) {
    $(document).ready(function(){
        $('.product-info-price').after($('.course-info'));
    
        let courseLevel = $('#course-level').attr('data-courselevel');

        if(courseLevel ==1 ) {
            $('#first-level').css('background','#10b33b');
        } else if(courseLevel == 2) {
            $('#first-level').css('background','#10b33b');
            $('#second-level').css('background','#10b33b');
        } else if( courseLevel == 3) {
            $('#first-level').css('background','#10b33b');
            $('#second-level').css('background','#10b33b');
            $('#third-level').css('background','#10b33b');
        }
        
        if($('.course-info').length != 0) {
            $('.field.qty').css('display','none');
        }

        $('#course-content-accordion .section').click(function(){
            $('.section').each(function(index) {
                $(this).find('.section-collapsible-icon').css('transform','rotate(180deg)');
            });
            $('.section.active').each(function(index) {
                $(this).find('.section-collapsible-icon').css('transform','rotate(0deg)');
            });
        });
        $('.content.unlocked').click(function(){
            let url = $(this).attr('data-url');
            window.location = url;
        });
    });
});