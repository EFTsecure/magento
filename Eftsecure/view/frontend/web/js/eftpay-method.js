define(['jquery', 'mage/url'], function($, urlBuilder) {
    'use strict';

    return {
        callEftSecure: function(orderId) {
            const serviceUrl = urlBuilder.build('rest/all/V1/eft/secure');
            console.log("Url is", serviceUrl);
            return $.ajax({
                url: serviceUrl,
                type: 'POST',
                data: JSON.stringify({orderId: orderId}),
                contentType: 'application/json',
                dataType: 'json',
                success: function(response) {
                    console.log('Success:', response);
                },
                error: function(xhr, status, error) {
                    console.error('Error:', status, error);
                }
            });
        }
    };
});
