/*jshint browser:true jquery:true*/
/*global alert*/
var config = {
    config: {
        mixins: {
            'Magento_Checkout/js/view/shipping': {
                'Endereco_Addressvalidation/js/view/shipping-mixin': true
            },
            'Magento_Checkout/js/view/billing-address': {
                'Endereco_Addressvalidation/js/view/billing-address-mixin': true
            },
            'Magento_Checkout/js/view/form/element/email': {
                'Endereco_Addressvalidation/js/view/email-mixin': true
            },
            addressValidation: {
                'Endereco_Addressvalidation/js/view/customer-address-validation-mixin': true
            },
            'Magento_Customer/js/addressValidation': {
                'Endereco_Addressvalidation/js/view/customer-address-validation-mixin': true
            }
        }
    }
};
