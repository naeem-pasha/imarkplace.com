// require(['jquery'], function ($) {
//     $(document).ready(function () {
//         if (window.location.href.includes("local.imark")) {
//             console.log("Localhost");
//         }
//         jQuery( ".magebig-mobile-menu ul.term-list.smartmenu.magebig-nav" ).addClass( "nav-mobile-accordion" );

//         setTimeout(function() {
//           //jQuery("#mb-collapsible").prepend('<li class="level0"><h3>Shop By Categories</h3></li>');
//           jQuery('#mb-collapsible ul.level-1, .show-sub-content ul.level-1').each(function() {
    
//             var LiN = jQuery(this).find('li.level0').length;
    
//             if (LiN > 5) {
//               jQuery('li.level0', this).eq(4).nextAll().hide().addClass('toggleable');
//               jQuery(this).append('<li class="more">See All</li>');
//             }
    
//             //console.log(LiN);
//           });  
    
//           jQuery('#mb-collapsible ul.level-1, .show-sub-content ul.level-1').on('click', '.more', function() {
    
//             if (jQuery(this).hasClass('less')) {
//               jQuery(this).text('See All').removeClass('less');
//             } else {
//               jQuery(this).text('See Less').addClass('less');
//             }
    
//             jQuery(this).siblings('li.toggleable').slideToggle();
    
//           });
//         }, 500);

//     });
// });
// require(['jquery'], function ($) {
//     $(document).ready(function () {
//         if (window.location.href.includes("local.imark")) {
//             console.log("Localhost");
//         }
//         jQuery( ".magebig-mobile-menu ul.term-list.smartmenu.magebig-nav" ).addClass( "nav-mobile-accordion" );
//         setTimeout(function() {
//           //jQuery("#mb-collapsible").prepend('<li class="level0"><h3>Shop By Categories</h3></li>');
//           jQuery('#mb-collapsible ul.level-1, .term-list .level-1:nth-child(2) .show-sub-content ul.level-1').each(function() {
//             var LiN = jQuery(this).find('li.level0').length;
//             if (LiN > 5) {
//               jQuery('li.level0', this).eq(4).nextAll().hide().addClass('toggleable');
//               jQuery(this).append('<li class="more">See All</li>');
//             }
//             //console.log(LiN);
//           });
//           jQuery('#mb-collapsible ul.level-1, .show-sub-content ul.level-1').on('click', '.more', function() {
//             if (jQuery(this).hasClass('less')) {
//               jQuery(this).text('See All').removeClass('less');
//             } else {
//               jQuery(this).text('See Less').addClass('less');
//             }
//             jQuery(this).siblings('li.toggleable').slideToggle();
//           });
//         }, 500);
//     });
// });


// function myFunction() {
//     document.getElementById("myDropdown").classList.toggle("show");
//   }
  
//   // Close the dropdown if the user clicks outside of it
//   window.onclick = function(event) {
//     if (!event.target.matches('.dropbtn')) {
//       var dropdowns = document.getElementsByClassName("dropdown-content");
//       var i;
//       for (i = 0; i < dropdowns.length; i++) {
//         var openDropdown = dropdowns[i];
//         if (openDropdown.classList.contains('show')) {
//           openDropdown.classList.remove('show');
//         }
//       }
//     }
//   }



var root = document.querySelector('.mb-navigation');
    var nav = root.querySelector('.magebig-nav').querySelector('.show-sub-content').firstElementChild;
    var navFirstChild = nav.firstElementChild;
    var navSecondChild = navFirstChild.nextElementSibling;

    function mouseEnter() {
        root.classList.add("active");
        navFirstChild.classList.add("active");
    }

    function mouseLeave() {
        root.classList.remove("active");
        navFirstChild.classList.remove("active");
    }


    function mouseEnterNav() {
        root.classList.remove("active");
        navFirstChild.classList.remove("active");
    }

    root.onmouseenter = function () { mouseEnter() };
    root.onmouseleave = function () { mouseLeave() };
    navFirstChild.onmouseenter = function () { mouseEnterNav() };


/* When the user clicks on the button, 
toggle between hiding and showing the dropdown content */


