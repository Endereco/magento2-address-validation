define([
    'uiComponent',
    'underscore',
    'Magento_Customer/js/customer-data',
	'Endereco_Addressvalidation/js/helper/configuration'
], function (Component, _, customerData, configurationHelper) {
    'use strict';
	var countryData = customerData.get('directory-data');
	
    return Component.extend({
        defaults: {
            template: 'Endereco_Addressvalidation/shipping-information/address-renderer/default'
        },

		/**
         * @param {*} countryId
         * @return {String}
         */
        getCountryName: function (countryId) {
            return countryData()[countryId] != undefined ? countryData()[countryId].name : ''; //eslint-disable-line
        },

        /**
         * Get customer attribute label
         *
         * @param {*} attribute
         * @returns {*}
         */
        getCustomAttributeLabel: function (attribute) {
            var label;

            if (typeof attribute === 'string') {
                return attribute;
            }

            if (attribute.label) {
                return attribute.label;
            }

            if (_.isArray(attribute.value)) {
                label = _.map(attribute.value, function (value) {
                    return this.getCustomAttributeOptionLabel(attribute['attribute_code'], value) || value;
                }, this).join(', ');
            } else if (typeof attribute.value === 'object') {
                label = _.map(Object.values(attribute.value)).join(', ');
            } else {
                label = this.getCustomAttributeOptionLabel(attribute['attribute_code'], attribute.value);
            }

            return label || attribute.value;
        },

        /**
         * Get option label for given attribute code and option ID
         *
         * @param {String} attributeCode
         * @param {String} value
         * @returns {String|null}
         */
        getCustomAttributeOptionLabel: function (attributeCode, value) {
            var option,
                label,
                options = this.source.get('customAttributes') || {};

            if (options[attributeCode]) {
                option = _.findWhere(options[attributeCode], {
                    value: value
                });

                if (option) {
                    label = option.label;
                }
            } else if (value.file !== null) {
                label = value.file;
            }

            return label;
        },
        /**
         * Override default method to format street address
         *
         * @param {Object} address
         * @return {String}
         */
        getFormattedStreet: function (address) {
            if (!address || !address.street) {
                return '';
            }
            const street = _.compact(address.street);
			if (!configurationHelper.useStreetFull()) {
				if (street.length == 2) {
					var countryId = address.countryId.toLowerCase();
					var fullStreetTemplate = configurationHelper.getFullStreetTemplate(countryId);
					if (fullStreetTemplate === '{{{buildingNumber}}} {{{streetName}}}') {
						return `${street[1]} ${street[0]}`;	
					} else {
						return `${street[0]} ${street[1]}`;	
					}
				}							
			} 
			return street.join(', ');
        }
    });
});
