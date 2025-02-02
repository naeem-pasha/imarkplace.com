require(['jquery'], function ($) {
    $(document).ready(function () {
        function applyMaxWidth() {
            const headerElement = document.querySelector('.maximum-width');
            if (headerElement) {
                if (window.innerWidth >= 2100) {
                    headerElement.style.maxWidth = '1400px';
                } else {
                    headerElement.style.maxWidth = '2508px'; // Set back to default or whatever the initial value is
                }
            } else {
                console.warn("Element with class 'maximum-width' not found.");
            }
        }

        // Initial check when the page loads
        applyMaxWidth();

        // Add resize event listener to update on window resize
        window.addEventListener('resize', applyMaxWidth);
    });
});

console.log("PRODUCT PAGE JS WORKING");
