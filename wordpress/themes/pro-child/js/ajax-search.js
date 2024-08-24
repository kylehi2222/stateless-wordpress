jQuery(document).ready(function($) {
    console.log('Document ready');

    function disableAutofill() {
        $('.location_search_input').each(function() {
            $(this).attr('autocomplete', 'off');

            // Set up a workaround to prevent autofill without using readonly
            $(this).attr('inputmode', 'text');
        });
    }

    function attachInputListener() {
        console.log('Attaching input listener');
        $('.location_search_input').each(function() {
            var $inputField = $(this);
            var $spinner = $inputField.closest('.ff-el-group').find('#spinner');
            var $resultsContainer = $inputField.closest('.ff-el-group').next().find('#search_results_container');
            var $generateChartButton = $('.generate-chart-button');

            // Initially disable the button
            $generateChartButton.prop('disabled', true);

            $inputField.on('input', function() {
                var query = $inputField.val();
                console.log('Input changed:', query);

                if (query.length > 2) {
                    // Show spinner
                    $spinner.show();

                    $.ajax({
                        url: ajax_search_params.ajax_url,
                        type: 'POST',
                        data: {
                            action: 'search_birth_locations',
                            query: query
                        },
                        success: function(response) {
                            console.log('AJAX response:', response);
                            // Hide spinner
                            $spinner.hide();

                            $resultsContainer.empty();

                            if (response.success) {
                                var results = response.data;

                                if (results.length > 0) {
                                    $resultsContainer.show(); // Show container if there are results
                                    results.forEach(function(post) {
                                        $resultsContainer.append('<div class="search-result-item" data-post-id="' + post.id + '">' + post.title + '</div>');
                                    });
                                } else {
                                    $resultsContainer.hide(); // Hide container if no results
                                }
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('AJAX error:', status, error);
                            // Hide spinner
                            $spinner.hide();
                        }
                    });
                } else {
                    $resultsContainer.empty().hide();
                    // Disable the button if the input is cleared
                    $generateChartButton.prop('disabled', true);
                }
            });

            // Delegate click event for dynamically added items
            $resultsContainer.on('click', '.search-result-item', function() {
                var selectedTitle = $(this).text();
                var selectedPostId = $(this).data('post-id');
                console.log('Selected:', selectedTitle, selectedPostId);

                // Set the input value to the selected result
                $inputField.val(selectedTitle);

                // Locate and update the hidden field with the selected ID
                var $hiddenField = $inputField.closest('form').find('input[name="selected_birth_location_id"]');
                if ($hiddenField.length) {
                    $hiddenField.val(selectedPostId);
                    console.log('Hidden field updated:', $hiddenField.val());

                    // Enable the button now that a location has been selected
                    $generateChartButton.prop('disabled', false);
                } else {
                    console.error('Hidden field not found.');
                }

                // Clear the search results
                $resultsContainer.empty().hide();
            });
        });
    }

    // Disable autofill
    disableAutofill();

    // Attach the input listener directly
    attachInputListener();

    // Also attach it when FluentForm is loaded
    $(document).on('fluentform_loaded', function() {
        console.log('FluentForm loaded');
        disableAutofill(); // Disable autofill again if form reloads
        attachInputListener(); // Re-attach listeners if the form reloads
    });
});
