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
    $('.course-bar').each(function(i){
        let percentage = $(this).attr('data-course-percentage');
        $(this).css('width', percentage + '%');
    });
});