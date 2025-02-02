//JS CODE FOR FOOTER

require([
    'jquery'
], function ($) {
    document.addEventListener('DOMContentLoaded', function () {
        console.log("DOM Content Loaded and JS is running");
        const collapseMobileDivs = document.querySelectorAll('.collapse2-mobile');
        collapseMobileDivs.forEach(function (div) {
            div.addEventListener('click', function () {
                console.log("Btn Clicked");
                // Toggle the 'toggle-visible' class on the clicked div
                div.classList.toggle('toggle-visible');
                // Select the child div with the class 'mobile-toggle-content'
                const mobileToggleContent = div.querySelector('.mobile-toggle-content');
                mobileToggleContent.classList.toggle('visible');
            });
        });
    });
});

