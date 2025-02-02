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
//               template:'Jazz_JazzCashS/payment/form'
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
//                template: 'Jazz_JazzCashS/payment/form',
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
//                return 'jazz_cashs';
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
//                return _.map(window.checkoutConfig.payment.jazz_cashs.transactionResults, function(value, key) {
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
	         template: 'Jazz_JazzCashS/payment/form'
	     },
	//modulename/mypaymentmethod/generatepaymentrequest
	     afterPlaceOrder: function () {
	          redirectOnSuccessAction.redirectUrl = url.build('jazzcashs/index/');
	    	 	//redirectOnSuccessAction.redirectUrl = 'https://www.google.com';
	           // this.redirectAfterPlaceOrder = true;
	    	 
	    	// window.location.replace(' http://119.160.80.70/PayAxisCustomerPortal/transactionmanagement/merchantform');
	    	// redirectOnSuccessAction.redirectUrl = 'http://119.160.80.70/PayAxisCustomerPortal/transactionmanagement/merchantform';
	    	 this.redirectAfterPlaceOrder = true;
	      },
	    });
	});