jQuery(document).ready(function($) {
    function refreshTokenBalance() {
        $.ajax({
            url: token_refresh_params.ajax_url,
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'refresh_token_counter',
                nonce: token_refresh_params.nonce
            },
            success: function(response) {
                if (response && response.show_tokens !== undefined) {
                    $('.token-counter').html(response.show_tokens);
                }
            },
            error: function(xhr, status, error) {
                console.error('Failed to refresh token balance:', error);
            }
        });
    }

    // Call the function once on page load
    refreshTokenBalance();

    // Call the function periodically (e.g., every 30 seconds)
    setInterval(refreshTokenBalance, 30000);
});
