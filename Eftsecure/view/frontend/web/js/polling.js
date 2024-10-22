define(['jquery', 'mage/url'], function($, urlBuilder) {
    'use strict';

    return {
        checkTransactionStatus: function(transactionId) {
            console.log("Transaction is", transactionId);
            const serviceUrl = urlBuilder.build('eft/transaction/status');
            $.ajax({
                url: serviceUrl, // Adjust the URL based on your module and controller
                method: 'GET',
                data: { transactionId: transactionId },
                success: function(response) {
                    console.log("Status Response", response);
                    if (response.status == 'complete') {
                        closeModalAndIframe();
                        let successUrl = window.BASE_URL + 'checkout/onepage/success';
                        window.location.href = successUrl;
                    } else if(response.status == 'canceled') {
                        closeModalAndIframe();
                        let failureUrl = window.BASE_URL + 'checkout/onepage/failure';
                        window.location.href = failureUrl;
                    }
                }
            });
        }
    }

    function closeModalAndIframe() {
        $('#eftxModal').hide(); // Hide the modal
        $('#externalIframe').attr('src', ''); // Clear the iframe src
    }
});
