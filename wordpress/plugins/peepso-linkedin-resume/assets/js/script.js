jQuery(document).ready(function($) {
    $('#add-work-experience').on('click', function() {
        $('#work-experience-form').toggle();
    });

    $('.edit-work').on('click', function() {
        var id = $(this).data('id');
        $.ajax({
            url: ajax_object.ajax_url,
            type: 'POST',
            data: {
                action: 'get_work_experience',
                id: id
            },
            success: function(response) {
                if (response.success) {
                    var data = response.data;
                    $('#work-id').val(data.id);
                    $('#position').val(data.position);
                    $('#company').val(data.company);
                    $('#start_date').val(data.start_date);
                    $('#end_date').val(data.end_date);
                    $('#description').val(data.description);
                    $('#work-experience-form').show();
                }
            }
        });
    });

    $('#save-work').on('click', function() {
        var formData = $('#work-experience').serialize();
        $.ajax({
            url: ajax_object.ajax_url,
            type: 'POST',
            data: formData + '&action=save_work_experience',
            success: function(response) {
                if (response.success) {
                    alert('Work experience saved!');
                    location.reload(); // Reload the page to see the changes
                } else {
                    alert('Error saving work experience.');
                }
            },
            error: function() {
                alert('Failed to process the request.');
            }
        });
    });
});
