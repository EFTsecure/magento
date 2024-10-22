define(['jquery', 'mage/url'], function($, urlBuilder) {
    'use strict';

    return {
        getOrder: function(quoteId) {
            // Create the service URL to fetch the order ID by quote ID
            let serviceUrl = urlBuilder.build('rest/all/V1/orders/quote/' + quoteId);
            console.log("Url is", serviceUrl);
            return $.ajax({
                url: serviceUrl,
                type: 'GET',
                contentType: 'application/json',
                success: function(response) {
                    console.log('Success fetching Order:', response);
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching order ID: ', error);
                }
            });
        }
    };
});
