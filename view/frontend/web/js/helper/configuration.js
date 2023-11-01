/*jshint browser:true jquery:true*/
/*global alert*/
define([
    'Endereco_Addressvalidation/js/helper/logger',
	'jquery'
], function (logger, $) {
    'use strict';

    return {
        isModuleEnabled: function() {
            return window.checkoutConfig.cccc.addressvalidation.endereco.enabled;
        },

        isAddressValidationEnabled: function() {
            return this.isModuleEnabled() && window.checkoutConfig.cccc.addressvalidation.endereco.check.shipping_enabled;
        },

        isAddressValidationEnabledBilling: function() {
            return this.isModuleEnabled() && window.checkoutConfig.cccc.addressvalidation.endereco.check.billing_enabled;
        },

        getCountryId: function() {
            return window.checkoutConfig.cccc.addressvalidation.endereco.countryId;
        },

        isFirstnameToUppercaseEnabled: function() {
            return this.isUpperCaseConversion(this.getFirstnameConversion());
        },

        isLastnameToUppercaseEnabled: function() {
            return this.isUpperCaseConversion(this.getLastnameConversion());
        },

        getFirstnameConversion: function() {
            return window.checkoutConfig.cccc.addressvalidation.endereco.transformation.convert_firstname;
        },

        getLastnameConversion: function() {
            return window.checkoutConfig.cccc.addressvalidation.endereco.transformation.convert_lastname;
        },

        isUpperCaseConversion: function(val) {
            return val == 'uppercase';
        },

        isLowerCaseConversion: function(val) {
            return val == 'lowercase';
        },

        isUcFirstConversion: function(val) {
            return val == 'ucfirst';
        },

        ccccGetAdressDataFieldselector: function(field, fallback) {
            var fieldSelector = window.checkoutConfig.cccc.addressvalidation.endereco.mapping && window.checkoutConfig.cccc.addressvalidation.endereco.mapping[field]
                ? window.checkoutConfig.cccc.addressvalidation.endereco.mapping[field] : fallback;

            logger.logData(
                "shipping-mixin/ccccGetAdressDataFieldselector: Determined field selector for "+field+" (fallback: "+fallback+") => result: "+fieldSelector
            );

            return fieldSelector;
        },

        ccccGetAddressDataByFieldSelector: function(field, fallback) {
            var fieldSelector = this.ccccGetAdressDataFieldselector(field, fallback);
			if ($('.form-address-edit .field [name="'+fieldSelector+'"]').length) {
				return fieldSelector;
			}
            var val = fieldSelector.replace(/\[([0-9]+)\]/, ".$1");
            logger.logData(
                "shipping-mixin/ccccGetAddressDataByFieldSelector: Determined field selector for "+field+" (fallback: "+fallback+") => normalized result: "+val
            );
            return val;
        },

        useStreetFull: function() {
            return window.checkoutConfig.cccc.addressvalidation.endereco.mapping && window.checkoutConfig.cccc.addressvalidation.endereco.mapping.useStreetFull
        }
    };
});
