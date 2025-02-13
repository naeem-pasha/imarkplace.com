define([
    'jquery',
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/model/url-builder',
    'mage/storage',
    'Magento_Checkout/js/model/error-processor',
    'Magento_Checkout/js/model/full-screen-loader',
    'Magento_Customer/js/model/customer',
    'Magento_Checkout/js/model/shipping-rate-registry',
    'Magento_Checkout/js/action/get-payment-information',
    'Magento_Customer/js/customer-data',
], function (
    $,
    quote,
    urlBuilder,
    storage,
    errorProcessor,
    fullScreenLoader,
    customer,
    shippingRateRegistry,
    getPaymentInformationAction,
    customerData
) {
    'use strict';

    return function (item) {
        var serviceUrl,
            payload = {
                cartItem: {
                    item_id: item.item_id,
                    qty: item.qty,
                    quote_id: quote.getQuoteId()
                }
            };

        if (!customer.isLoggedIn()) {
            serviceUrl = urlBuilder.createUrl('/guest-carts/:cartId/items', {
                cartId: quote.getQuoteId()
            });
        } else {
            serviceUrl = urlBuilder.createUrl('/carts/mine/items', {});
        }

        fullScreenLoader.startLoader();

        return storage.post(
            serviceUrl,
            JSON.stringify(payload)
        ).done(
            function (response) {
                // Reload Shipping rate
                let address = quote.shippingAddress();

                shippingRateRegistry.set(address.getKey(), null);
                shippingRateRegistry.set(address.getCacheKey(), null);
                quote.shippingAddress(address);

                // Reload payment methods and totals
                let deferred = $.Deferred();

                getPaymentInformationAction(deferred);
                $.when(deferred).done(function() {
                    // update summary block heading quantity
                    let totalQty = quote.totals().items_qty;

                    // invalidate shopping cart
                    customerData.invalidate(['cart']);

                    fullScreenLoader.stopLoader();
                });
            }
        ).fail(
            function (response) {
                errorProcessor.process(response);
            }
        ).always(function () {
            fullScreenLoader.stopLoader();
        });
    };
});
