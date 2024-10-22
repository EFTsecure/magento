/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
/*browser:true*/
/*global define*/
define(
[
    'jquery',
    'Magento_Checkout/js/view/payment/default',
    'Magento_Checkout/js/action/place-order',
    'Magento_Checkout/js/action/select-payment-method',
    'Magento_Customer/js/model/customer',
    'Magento_Checkout/js/checkout-data',
    'Magento_Checkout/js/model/payment/additional-validators',
    'mage/url',
    'Magento_Ui/js/modal/modal',
    'Magento_Checkout/js/model/quote',
    'Callpay_Eftsecure/js/eftpay-method',
    'Callpay_Eftsecure/js/polling',
    'Callpay_Eftsecure/js/order-by-quote-id'
],
function (
    $,
    Component,
    placeOrderAction,
    selectPaymentMethodAction,
    customer,
    checkoutData,
    additionalValidators,
    url,
    modal,
    quote,
    eftPay,
    polling,
    orderByQuoteId
) {
        'use strict';

        return Component.extend({
            defaults: {
                template: 'Callpay_Eftsecure/payment/eftpayment'
            },
            placeOrder: function (data, event) {
                if (event) {
                    event.preventDefault();
                }
                var self = this,
                    placeOrder,
                    emailValidationResult = customer.isLoggedIn(),
                    loginFormSelector = 'form[data-role=email-with-possible-login]';

                if (!customer.isLoggedIn()) {
                    $(loginFormSelector).validation();
                    emailValidationResult = Boolean($(loginFormSelector + ' input[name=username]').valid());
                }
                if (emailValidationResult && this.validate() && additionalValidators.validate()) {
                    this.isPlaceOrderActionAllowed(false);
                    placeOrder = placeOrderAction(this.getData(), false, this.messageContainer);
                    $.when(placeOrder).fail(function () {
                        self.isPlaceOrderActionAllowed(true);
                    }).done(this.afterPlaceOrder.bind(this));
                    return true;
                }
                return false;
            },

            selectPaymentMethod: function() {
                selectPaymentMethodAction(this.getData());
                checkoutData.setSelectedPaymentMethod(this.item.method);
                return true;
            },

            afterPlaceOrder: function () {
                let checkoutConfig = window.checkoutConfig;

                // Ensure that the payment configuration and eftpay method exist
                if (checkoutConfig && checkoutConfig.payment && checkoutConfig.payment.eftpay) {
                    let isRedirectEnabled = checkoutConfig.payment.eftpay.redirect;
                    if (parseInt(isRedirectEnabled) === 1)  {
                        window.location.replace(url.build('eft/checkout/redirect/'));
                    } else {
                        console.log('window.checkoutConfig.totalsData:', quote);
                        // Assuming the amount is stored in this.getData().totalAmount
                        let amount = window.checkoutConfig.totalsData.grand_total;
                        // Retrieve the currency symbol
                        let currency = window.checkoutConfig.totalsData.base_currency_code;
                        // Modal options
                        let options = {
                            type: 'popup',
                            responsive: true,
                            innerScroll: false,

                        };

                        // Initialize the modal (modal content is in the eftpay.phtml file)
                        let popup = modal(options, $('#eftxModal'));
                        // $('#modal-amount').text(currency + ' ' + amount.toFixed(2));
                        // Get the last order ID
                        let quoteId = quote.getQuoteId();
                        console.log('Quote ID is: ' + quoteId);

                        orderByQuoteId.getOrder(quoteId)
                            .done(function (response) {
                                let orderId  = response;
                                if (orderId) {
                                    let formattedOrderId = orderId.toString().padStart(9, '0');
                                    eftPay.callEftSecure(formattedOrderId)
                                        .done(function (response) {
                                            let decoded = JSON.parse(response);
                                            if (decoded && decoded.url) {
                                                let url = decoded.url;
                                                let cleanUrl = url.split('&')[0];
                                                $('#eftxModal').modal('openModal');
                                                console.log("Decoded", decoded);
                                                document.getElementById('eftxIframe').src = cleanUrl;

                                                setInterval(function() {
                                                    polling.checkTransactionStatus(formattedOrderId);
                                                }, 3000); // Poll every 3 seconds
                                            }
                                        })
                                        .fail(function (error) {
                                            console.error('Error calling EFT Secure:', error);
                                        });
                                }
                            })
                            .fail(function (error) {
                                console.error('Error Getting order:', error);
                            });
                    }
                } else {
                    console.error('eftsecure payment configuration is missing in window.checkoutConfig');
                }
            },
            /** Returns send check to info */
            getMailingAddress: function() {
                return window.checkoutConfig.payment.checkmo.mailingAddress;
            },
			/** Returns payment method instructions */
            getInstructions: function() {
                //return window.checkoutConfig.payment.instructions;
            }


        });
    }
);
