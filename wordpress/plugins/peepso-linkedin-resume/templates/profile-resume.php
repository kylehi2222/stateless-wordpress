<?php
if (isset($view_user_id)) {
    // Include the PeepSo profile header correctly
    $header = PeepSoTemplate::exec_template('profile', 'header', array('view_user_id' => $view_user_id), true);
    if (!$header) {
        error_log('Failed to load PeepSo profile header template.');
        echo '<div class="resume-content"><h2>Failed to load profile header.</h2></div>';
    } else {
        echo $header;
    }

    ?>
    <div class="resume-content">
        <h2>Resume for User ID: <?php echo $view_user_id; ?></h2>

        <!-- Work Experience Section -->
        <div class="resume-section">
            <h3>Work Experience</h3>
            <button id="add-work-experience" class="button">Add Work Experience</button>
            <div id="work-experience-form" style="display:none;">
                <h4>Add/Edit Work Experience</h4>
                <form id="work-experience">
                    <input type="hidden" id="work-id" name="id" value="">
                    <label for="position">Position:</label>
                    <input type="text" id="position" name="position">
                    <label for="company">Company:</label>
                    <input type="text" id="company" name="company">
                    <label for="start_date">Start Date:</label>
                    <input type="date" id="start_date" name="start_date">
                    <label for="end_date">End Date:</label>
                    <input type="date" id="end_date" name="end_date">
                    <label for="description">Description:</label>
                    <textarea id="description" name="description"></textarea>
                    <button type="button" id="save-work">Save</button>
                </form>
            </div>
            <div id="work-experience-list">
                <?php
                global $wpdb;
                $table_name = $wpdb->prefix . 'peepso_resumes';
                $work_experience = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name WHERE user_id = %d", $view_user_id));
                if ($work_experience) {
                    foreach ($work_experience as $experience) {
                        echo '<div class="resume-item">';
                        echo '<h4>' . esc_html($experience->position) . ' at ' . esc_html($experience->company) . '</h4>';
                        echo '<p>' . esc_html($experience->start_date) . ' to ' . (empty($experience->end_date) ? 'Present' : esc_html($experience->end_date)) . '</p>';
                        echo '<p>' . esc_html($experience->description) . '</p>';
                        echo '<button class="edit-work" data-id="' . $experience->id . '">Edit</button>';
                        echo '</div>';
                    }
                } else {
                    echo '<p>No work experience available.</p>';
                }
                ?>
            </div>
        </div>

        <!-- Similar sections for Education and Skills -->

    </div>

    <script>
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
    </script>
    <?php
} else {
    echo '<div class="resume-content"><h2>User ID not available.</h2></div>';
}
?>
