define([
    "jquery",
    "Magento_Ui/js/modal/modal",
    "mage/url",
    "mage/validation",
], function ($) {
    "use strict";
    return {
        validate: function () {
            var value;
            var customurl = window.checkoutConfig.payment.meezan_bank.url;
            $.ajax({
                url: customurl,
                type: "GET",
                dataType: "json",
                async: false, //This is required so that it wait for the response
                success: function (response) {
                    value = response;
                },
                error: function (xhr, status, errorThrown) {
                    console.log(errorThrown);
                    console.log("Invalid Configuration");
                },
            });
            if (value != null) {
                if (value.errorCode == 0) {
                    return true;
                } else {
                    alert(value.errorMessage);
                    return false;
                }
            } else {
                alert("Invalid Configuration");
                return false;
            }
        },
    };
});
