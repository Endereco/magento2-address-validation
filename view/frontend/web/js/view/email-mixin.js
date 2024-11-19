/*jshint browser:true jquery:true*/
/*global alert*/
define([
    'jquery',
    'Magento_Checkout/js/model/full-screen-loader',
    'Endereco_Addressvalidation/js/endereco-setup'
], function ($, fullScreenLoader, enderecosdk) {
    'use strict';

    var mixin = {
        default: {
            emailInitialized: false
        },
		
        initialize: function () {
            this._super();

            if (window.checkoutConfig.cccc.addressvalidation.endereco.email_check) {
                this.emailInitialized = true;
                enderecosdk.startEmailServices(
                    "",
                    {
                        postfixCollection:
                            {
                                email: ".checkout-shipping-address #customer-email"
                            }, name: 'customer_email'
                    }
                );
            }

            return this;
        },

        /**
         * Local email validation.
         *
         * @param {Boolean} focused - input focus.
         * @returns {Boolean} - validation result.
         */
        validateEmail: async function (focused) {
			var self = this;
            if (!this._super()) {
                return false;
            }

            if (!window.checkoutConfig.cccc.addressvalidation.endereco.email_check) {
                return true;
            }

            var loginFormSelector = 'form[data-role=email-with-possible-login]',
                usernameSelector = loginFormSelector + ' input[name=username]',
                emailField = $(usernameSelector);
			// Render status messages based on the response from the emailCheck method called in endereco.min.js
            var observer = new MutationObserver(function(mutations) {
				mutations.forEach(function(mutation) {
					if (mutation.attributeName === "class") {
						try {
							window.EnderecoIntegrator.integratedObjects.customer_email_emailservices.util.renderStatusMessages();
						} catch (error) {

						}
					}
				});
			});
			observer.observe(emailField[0], { attributes: true });

            return true;
        }
    }

    return function (email) {
        return email.extend(mixin);
    };
});
