/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
/*browser:true*/
/*global define*/



//define(
//    [
//        'Magento_Checkout/js/view/payment/default',
//        'mage/url'
//    ],
//    function (quote, Component, urlBuilder) {
//        'use strict';
//
//        return Component.extend({
//            defaults: {
//               template:'Jazz_JazzCashC/payment/form'
//            },
//
//            startPayment: function(){
//                var url = urlBuilder.build("http://www.google.com");
//                window.location.href = url;
//            },
//        });
//    }
//);



//define(
//    [           
//        'jquery',
//        'Magento_Checkout/js/view/payment/default'
//       
//    ],
//    function (Component) {
//        'use strict';
//
//        return Component.extend({
//            defaults: {
//                template: 'Jazz_JazzCashC/payment/form',
//                transactionResult: ''
//            },          
//            initObservable: function () {
//
//                this._super()
//                    .observe([
//                        'transactionResult'
//                    ]);
//                return this;
//            },
//
//            getCode: function() {
//                return 'jazz_cashc';
//            },
//
//            getData: function() {
//                return {
//                    'method': this.item.method,
//                    'additional_data': {
//                        'transaction_result': this.transactionResult()
//                    }
//                };
//            },
//
//            getTransactionResults: function() {
//                return _.map(window.checkoutConfig.payment.jazz_cashc.transactionResults, function(value, key) {
//                    return {
//                        'value': key,
//                        'transaction_result': value
//                    }
//                });
//            } 
//        });
//    }
//);




define([
    'Magento_Checkout/js/view/payment/default',
    'Magento_Checkout/js/action/redirect-on-success',
    'mage/url'
	], function (Component, redirectOnSuccessAction, url) {
	    'use strict';
	
	     return Component.extend({
	     defaults: {
	         template: 'Jazz_JazzCashC/payment/form'
	     },
	//modulename/mypaymentmethod/generatepaymentrequest
	     afterPlaceOrder: function () {
	          redirectOnSuccessAction.redirectUrl = url.build('jazzcash/index/');	    	 
	    	 this.redirectAfterPlaceOrder = true;
	      },
	    });
	});