/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
/*browser:true*/
/*global define*/
define(
    [
        'jquery',
        'mage/validation'
    ],
    function ($) {
        'use strict';

        return {

            /**
             * Validate checkout agreements
             *
             * @returns {Boolean}
             */
            validate: function () {
                var phoneValidationResult = false;
                var reg = /^[\+]?[(]?[0-9]{3}[)]?[-\s\.]?[0-9]{3}[-\s\.]?[0-9]{4,6}$/im;    
                //var telephone = $('#co-shipping-form input[name="telephone"]').val();
                var unSavedNo = document.querySelector('#co-shipping-form input[name="telephone"]').value;
                var savedNo = document.getElementsByClassName("shipping-address-item selected-item")[0]?.getElementsByTagName("a")[0]?.innerHTML
                var telephone = unSavedNo?unSavedNo:savedNo;

                console.log("unSavedNo",unSavedNo);
                console.log("savedNo",savedNo);
                console.log("telephone",telephone);
                console.log("reg",reg);

                if (reg.test(telephone)) {
                    // perform some task
                    console.log('Validation True');
                    phoneValidationResult = true;
                }else {

                    if(telephone == "" || telephone == null || telephone == undefined ){
                        phoneValidationResult = true;
                    }else{
                        jQuery(".opc-progress-bar li").first().find('span').trigger( "click" );
                        alert('Invalid Phone Number Entered');
                        console.log('Validation False');
                    }
                }
                return phoneValidationResult;
            }
        };
    }
);
