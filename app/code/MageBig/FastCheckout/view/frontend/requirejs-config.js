var config = {
    map: {
        '*': {
            'Magento_Checkout/js/model/shipping-save-processor/default': 'MageBig_FastCheckout/js/model/shipping-save-processor/default',
            'Magento_Checkout/js/action/set-payment-information-extended': 'MageBig_FastCheckout/js/action/set-payment-information-extended',
            'Magento_CheckoutAgreements/js/model/agreement-validator': 'MageBig_FastCheckout/js/model/agreement/agreement-validator',
            'Magento_CheckoutAgreements/js/model/agreements-modal': 'MageBig_FastCheckout/js/model/agreement/agreements-modal',
            'Magento_CheckoutAgreements/js/model/agreements-assigner': 'MageBig_FastCheckout/js/model/agreement/agreements-assigner'
        }
    },
    config: {
        mixins: {
            'Magento_Checkout/js/view/form/element/email': {
                'MageBig_FastCheckout/js/view/form/element/email-mixin': true
            },
            'Amazon_Payment/js/view/form/element/email': {
                'MageBig_FastCheckout/js/view/form/element/email-mixin': true
            },
            'Magento_Checkout/js/view/shipping': {
                'MageBig_FastCheckout/js/view/shipping-mixin': true
            },
            'Magento_Checkout/js/model/shipping-rates-validator': {
                'MageBig_FastCheckout/js/model/shipping-rates-validator-mixin': true
            },
            'Magento_Checkout/js/model/billing-address-postcode-validator': {
                'MageBig_FastCheckout/js/model/billing-address-postcode-validator-mixin': true
            },
            'Magento_Checkout/js/view/billing-address': {
                'MageBig_FastCheckout/js/view/billing-address-mixin': true
            },
            'Magento_Checkout/js/view/payment': {
                'MageBig_FastCheckout/js/view/payment-mixin': true
            },
            'Magento_Checkout/js/view/payment/default': {
                'MageBig_FastCheckout/js/view/payment/default-mixin': true
            },
            'Magento_Checkout/js/view/summary/abstract-total': {
                'MageBig_FastCheckout/js/view/summary/abstract-total-mixin': true
            },
            'Magento_SalesRule/js/view/payment/discount': {
                'MageBig_FastCheckout/js/sales-rule/view/payment/discount-mixin': true
            },
            'Magento_Checkout/js/view/shipping-address/address-renderer/default': {
                'MageBig_FastCheckout/js/view/shipping-address/address-renderer/default-mixin': true
            },
            'Magento_Checkout/js/model/checkout-data-resolver': {
                'MageBig_FastCheckout/js/model/checkout-data-resolver-mixin': true
            },
            'mage/validation': {
                'MageBig_FastCheckout/js/action/validation-mixin': true
            },
            'Magento_Ui/js/form/element/abstract': {
                'MageBig_FastCheckout/js/action/abstract-mixin': true
            }
        }
    }
};
