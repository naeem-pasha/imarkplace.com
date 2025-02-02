define(["jquery"], function ($) {
    "use strict";
    return function (target) {
        $.validator.addMethod(
            "required-https",
            function (value) {
                return value.match(
                    /(https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/
                );
            },
            $.mage.__("Only https allowed.")
        );
        return target;
    };
});
