<?php

// =============================================================================
// FUNCTIONS.PHP
// -----------------------------------------------------------------------------
// Overwrite or add your own custom functions to Pro in this file.
// =============================================================================

// =============================================================================
// TABLE OF CONTENTS
// -----------------------------------------------------------------------------
//   01. Enqueue Parent Stylesheet
//   02. Additional Functions
// =============================================================================

// Enqueue Parent Stylesheet
// =============================================================================

add_filter( 'x_enqueue_parent_stylesheet', '__return_true' );



// Additional Functions
// =============================================================================


function create_booking_host($user_id, $user_display_name) {
    global $wpdb;

    // Correct the table name by removing the extra '6h_'
    $table_name = $wpdb->prefix . 'fcal_calendars';
    $meta_table_name = $wpdb->prefix . 'fcal_meta';
    $events_table_name = $wpdb->prefix . 'fcal_calendar_events';

    // Check if the user already has a record
    $existing_record = $wpdb->get_var($wpdb->prepare(
        "SELECT id FROM $table_name WHERE user_id = %d",
        $user_id
    ));

    if ($existing_record) {
        error_log('User already has a booking host record. Skipping creation.');
        return false;
    }

    // Generate a unique MD5 hash
    $hash = md5(uniqid(rand(), true));

    // Set default timezone to UTC
    $timezone = 'UTC';

    // Prepare the data for fcal_calendars table
    $data = array(
        'hash' => $hash,
        'user_id' => $user_id,
        'title' => $user_display_name,
        'slug' => $user_id,
        'status' => 'active',
        'type' => 'simple',
        'event_type' => 'scheduling',
        'account_type' => 'free',
        'visibility' => 'public',
        'author_timezone' => $timezone,
        'max_book_per_slot' => 1,
        'created_at' => current_time('mysql'),
        'updated_at' => current_time('mysql')
    );

    // Log the data for debugging
    error_log('Data to be inserted into fcal_calendars: ' . print_r($data, true));

    // Insert the data into fcal_calendars table
    $inserted = $wpdb->insert($table_name, $data);

    // Check for errors
    if ($wpdb->last_error) {
        error_log('Error creating booking host: ' . $wpdb->last_error);
        return new WP_Error('db_insert_error', $wpdb->last_error);
    }

    // Log the result of the insertion
    if ($inserted) {
        $calendar_id = $wpdb->insert_id;
        error_log('Booking host created with ID: ' . $calendar_id);

        // Store the calendar_id in the user's meta
        update_user_meta($user_id, 'booking_calendar_id', $calendar_id);

        // Insert additional rows into wp_6h_fcal_meta
        $meta_data_1 = array(
            'object_type' => 'user_meta',
            'object_id' => $user_id,
            'key' => '_access_permissions',
            'value' => serialize(array(0 => 'manage_own_calendar')),
            'created_at' => current_time('mysql'),
            'updated_at' => current_time('mysql')
        );

        $availability_value = serialize(array(
            'default' => true,
            'timezone' => $timezone,
            'date_overrides' => array(),
            'weekly_schedules' => array(
                'sun' => array('enabled' => false, 'slots' => array()),
                'mon' => array('enabled' => true, 'slots' => array(array('start' => '09:00', 'end' => '17:00'))),
                'tue' => array('enabled' => true, 'slots' => array(array('start' => '09:00', 'end' => '17:00'))),
                'wed' => array('enabled' => true, 'slots' => array(array('start' => '09:00', 'end' => '17:00'))),
                'thu' => array('enabled' => true, 'slots' => array(array('start' => '09:00', 'end' => '17:00'))),
                'fri' => array('enabled' => true, 'slots' => array(array('start' => '09:00', 'end' => '17:00'))),
                'sat' => array('enabled' => false, 'slots' => array())
            )
        ));
        
        $meta_data_2 = array(
            'object_type' => 'availability',
            'object_id' => $user_id,
            'key' => 'Working Hours',
            'value' => $availability_value,
            'created_at' => current_time('mysql'),
            'updated_at' => current_time('mysql')
        );

        // Insert meta data 1
        $wpdb->insert($meta_table_name, $meta_data_1);
        if ($wpdb->last_error) {
            error_log('Error inserting meta data 1: ' . $wpdb->last_error);
        } else {
            error_log('Meta data 1 inserted with ID: ' . $wpdb->insert_id);
        }

        // Insert meta data 2
        $wpdb->insert($meta_table_name, $meta_data_2);
        if ($wpdb->last_error) {
            error_log('Error inserting meta data 2: ' . $wpdb->last_error);
        } else {
            $availability_id = $wpdb->insert_id;
            error_log('Meta data 2 inserted with ID: ' . $availability_id);

            // Insert data into wp_6h_fcal_calendar_events
            $event_hash = md5(uniqid(rand(), true));
            $event_data = array(
                'hash' => $event_hash,
                'user_id' => $user_id,
                'calendar_id' => $calendar_id,
                'duration' => 15,
                'title' => 'FREE 15 Minute Consultation',
                'slug' => '15min',
                'description' => 'Have an introductory call to find out if we are a good fit for each other.',
                'settings' => serialize(array(
                    'schedule_type' => 'weekly_schedules',
                    'weekly_schedules' => array(
                        'sun' => array('enabled' => false, 'slots' => array()),
                        'mon' => array('enabled' => true, 'slots' => array(array('start' => '09:00', 'end' => '17:00'))),
                        'tue' => array('enabled' => true, 'slots' => array(array('start' => '09:00', 'end' => '17:00'))),
                        'wed' => array('enabled' => true, 'slots' => array(array('start' => '09:00', 'end' => '17:00'))),
                        'thu' => array('enabled' => true, 'slots' => array(array('start' => '09:00', 'end' => '17:00'))),
                        'fri' => array('enabled' => true, 'slots' => array(array('start' => '09:00', 'end' => '17:00'))),
                        'sat' => array('enabled' => false, 'slots' => array())
                    ),
                    'date_overrides' => array(),
                    'range_type' => 'range_days',
                    'range_days' => 60,
                    'range_date_between' => array('', ''),
                    'schedule_conditions' => array('value' => 4, 'unit' => 'hours'),
                    'buffer_time_before' => '0',
                    'buffer_time_after' => '0',
                    'slot_interval' => '',
                    'team_members' => array()
                )),
                'availability_type' => 'existing_schedule',
                'availability_id' => $availability_id,
                'status' => 'active',
                'type' => 'free',
                'color_schema' => '#0099ff',
                'location_type' => 'single',
                'location_settings' => serialize(array(
                    array(
                        'type' => 'online_meeting',
                        'title' => 'Online Meeting',
                        'display_on_booking' => '',
                        'meeting_link' => 'https://zoom.com'
                    )
                )),
                'event_type' => 'single',
                'is_display_spots' => 0,
                'max_book_per_slot' => 1,
                'created_at' => current_time('mysql'),
                'updated_at' => current_time('mysql')
            );

            $wpdb->insert($events_table_name, $event_data);
            if ($wpdb->last_error) {
                error_log('Error inserting calendar event: ' . $wpdb->last_error);
            } else {
                error_log('Calendar event inserted with ID: ' . $wpdb->insert_id);
            }
        }

        // Purge LiteSpeed cache for the user
        if (function_exists('litespeed_purge_user')) {
            litespeed_purge_user($user_id);
            error_log('LiteSpeed cache purged for user ID: ' . $user_id);
        } else {
            error_log('LiteSpeed cache purge function not available.');
        }

        return $calendar_id;
    } else {
        error_log('Booking host not created.');
        return false;
    }
}


function my_login_redirect( $redirect_to, $request, $user ) {
    //is there a user to check?
    if (isset($user->roles) && is_array($user->roles)) {
        //check for subscribers
        if (!in_array('administrator', $user->roles)) {
            // redirect them to another URL, in this case, the homepage 
            $redirect_to =  home_url();
        }
    }

    return $redirect_to;
}

add_filter( 'login_redirect', 'my_login_redirect', 10, 3 );

// Add this function to your theme's functions.php or in a custom plugin

// Function to include the username in the menu links
function add_username_to_menu($items, $args) {
    // Check if the user is logged in
    if (is_user_logged_in()) {
        // Get the current user information
        $current_user = wp_get_current_user();
        $username = $current_user->user_login;

        // Loop through each menu item
        foreach ($items as $item) {
            // Check if the menu item URL contains a specific placeholder (e.g., %username%)
            if (strpos($item->url, '%username%') !== false) {
                // Replace the placeholder with the actual username
                $item->url = str_replace('%username%', $username, $item->url);
            }
        }
    }

    return $items;
}
add_filter('wp_nav_menu_objects', 'add_username_to_menu', 10, 2);

function restrict_admin_access() {
    // Check if the current user is trying to access the wp-admin area
    if (is_admin() && !defined('DOING_AJAX') && !current_user_can('administrator') && !current_user_can('dev')) {
        // If the user is not an administrator or dev, redirect them
        wp_redirect(home_url()); // Redirect to homepage, or use another URL
        exit;
    }
}
add_action('admin_init', 'restrict_admin_access');

function member_since_shortcode() {
    if ( is_user_logged_in() ) {
        $user_id = get_current_user_id();
        $user_data = get_userdata( $user_id );
        $user_registered = $user_data->user_registered;
        $registration_date = new DateTime( $user_registered );
        $member_since = $registration_date->format( 'M j, Y' );
        return $member_since;
    }
}
add_shortcode( 'member_since', 'member_since_shortcode' );


add_action('cred_save_data', 'save_youtube_video_details',10,2);

function save_youtube_video_details($post_id, $form_data)
{
    // Check if the form is the correct one
    if ($form_data['id'] == 156738) {

        // Get the URL from the submitted data
        $youtube_url = get_post_meta($post_id, 'wpcf-video-url', true);

        // Check if URL is a valid YouTube URL
        if (!preg_match('~^(?:https?://)?(?:www[.])?(?:youtube[.]com/watch[?]v=|youtu[.]be/)([^&]{11})~', $youtube_url, $matches)) {
            return;  // Not a valid YouTube URL, exit the function
        }

        // Extract the video ID from the URL
        $video_id = $matches[1];  // Video ID is now in $matches[1]

        // Use the YouTube Data API to get the video details
        $api_key = 'AIzaSyAuyeFdqsRI4VBjq253yDVQwomVIOsMRZI';
        $video_details = file_get_contents("https://www.googleapis.com/youtube/v3/videos?id=$video_id&key=$api_key&part=snippet,contentDetails");

        // Decode the JSON response
        $video_details = json_decode($video_details, true);

        // Update the post title and content
        $post_data = array(
            'ID' => $post_id,
            'post_title' => $video_details['items'][0]['snippet']['title'],
            'post_content' => $video_details['items'][0]['snippet']['description'],
        );
        wp_update_post($post_data);

        // Save the details as custom fields
        update_post_meta($post_id, 'VideoDuration', convert_duration($video_details['items'][0]['contentDetails']['duration']));
        update_post_meta($post_id, 'VideoID', $video_id);

        // Save the thumbnail as the featured image
        $image_url = $video_details['items'][0]['snippet']['thumbnails']['high']['url'];
        update_post_meta($post_id, 'VideoImage', $image_url);
        $upload_dir = wp_upload_dir();
        $image_data = file_get_contents($image_url);
        $filename = time() . '-' . basename($image_url);  // Add timestamp to filename
        if(wp_mkdir_p($upload_dir['path']))
            $file = $upload_dir['path'] . '/' . $filename;
        else
            $file = $upload_dir['basedir'] . '/' . $filename;
        file_put_contents($file, $image_data);

        $wp_filetype = wp_check_filetype($filename, null );
        $attachment = array(
            'post_mime_type' => $wp_filetype['type'],
            'post_title' => sanitize_file_name($filename),
            'post_content' => '',
            'post_status' => 'inherit'
        );
        $attach_id = wp_insert_attachment( $attachment, $file, $post_id );
        require_once(ABSPATH . 'wp-admin/includes/image.php');
        $attach_data = wp_generate_attachment_metadata( $attach_id, $file );
        wp_update_attachment_metadata( $attach_id, $attach_data );
        set_post_thumbnail( $post_id, $attach_id );

        // Get current user's username and save it to the custom field
        $current_user = wp_get_current_user();
                update_post_meta($post_id, 'affwp_landing_page_user_name', $current_user->user_login);
        
        // Redirect to the specified URL with the new post ID
        $redirect_url = add_query_arg('post_id', $post_id, 'https://humandesign.ai/videos/publish/edit/');
        wp_redirect($redirect_url);
        exit;
    }
}

function convert_duration($duration) {
    $start = new DateTime('@0'); // Unix epoch
    $start->add(new DateInterval($duration));
    return $start->format($start->format('H') > 0 ? 'H:i:s' : 'i:s');
}

function token_balance_shortcode($atts) {
    // Extract shortcode attributes
    $attributes = shortcode_atts(array(
        'user_id' => ''
    ), $atts);

    // Retrieve user_id from shortcode attributes
    $user_id = $attributes['user_id'];

    // Perform database query
    global $wpdb;
    $table_name = $wpdb->prefix . 'wpaicg_chattokens';
    $most_recent_row = $wpdb->get_row($wpdb->prepare(
        "SELECT tokens FROM $table_name WHERE user_id = %d ORDER BY created_at DESC LIMIT 1",
        $user_id
    ));

    // Check if a row was found
    if ($most_recent_row) {
        $tokens = $most_recent_row->tokens;
        // Return the tokens or any desired output
        return $tokens;
    }

    // Return a fallback value if no row was found
    return '0';
}
add_shortcode('token_balance', 'token_balance_shortcode');


function wpaicg_tokens_shortcode($atts) {
    $atts = shortcode_atts(
        array('module' => 'default_module'),
        $atts,
        'show_tokens'
    );

    if (is_user_logged_in()) {
        $user_id = get_current_user_id();
        $user_meta_key = 'wpaicg_' . $atts['module'] . '_tokens';
        $user_tokens = get_user_meta($user_id, $user_meta_key, true);
        $user_info = get_userdata($user_id);
        $user_roles = $user_info->roles;

        // Check if user tokens are empty
        if (empty($user_tokens)) {
            // Check for specific roles
            if (in_array('lifetime', $user_roles) || in_array('professional', $user_roles)) {
                return '∞'; // Infinity symbol for specific roles
            } else {
                return '0'; // Show 0 for other cases
            }
        }

        // Format the token amount with commas
        $formatted_tokens = number_format($user_tokens);

        return $formatted_tokens;
    }

    return 'Please login to view tokens.';
}
add_shortcode('show_tokens', 'wpaicg_tokens_shortcode'); 

add_filter( 'login_display_language_dropdown', '__return_false' );

function remove_private_prefix($title) {
	$title = str_replace('Private: ', '', $title);
	return $title;
}
add_filter('the_title', 'remove_private_prefix');


	
add_filter('wpv_filter_query', 'show_featured', 10, 3);
function show_featured($query, $view_settings,$view_id ) {
    if($view_id == 1382)
    {
        $query['meta_key'] = '_thumbnail_id';
    }
    return $query;
 }
function check_post_type_shortcode($atts, $content = null) {
    // Extract the attributes passed to the shortcode
    $a = shortcode_atts(array(
        'type' => '', // Default value if no type is specified
    ), $atts);

    // Get the current post type
    $current_post_type = get_post_type();

    // Convert the 'type' attribute into an array, splitting by comma
    $types = explode(',', $a['type']);

    // Trim whitespace around the types
    $types = array_map('trim', $types);

    // Check if the current post type is in the specified types
    if (in_array($current_post_type, $types)) {
        // If match, return the content
        return do_shortcode($content);
    }

    // If there is no match, return nothing
    return '';
}

// Add the shortcode to WordPress
add_shortcode('if_post_type', 'check_post_type_shortcode');

function enqueue_custom_scripts() {
    // Get the file modification time as the version number for ajax-search.js
    $ajax_search_version = filemtime(get_stylesheet_directory() . '/js/ajax-search.js');
    
    // Get the file modification time as the version number for token-refresh.js
    $token_refresh_version = filemtime(get_stylesheet_directory() . '/js/token-refresh.js');

    // Enqueue and localize the search script with version parameter
    wp_enqueue_script('ajax-search-script', get_stylesheet_directory_uri() . '/js/ajax-search.js', array('jquery'), $ajax_search_version, true);
    wp_localize_script('ajax-search-script', 'ajax_search_params', array(
        'ajax_url' => admin_url('admin-ajax.php')
    ));

    // Enqueue and localize the token refresh script with version parameter
    if (is_user_logged_in()) {
        wp_enqueue_script('token-refresh-script', get_stylesheet_directory_uri() . '/js/token-refresh.js', array('jquery'), $token_refresh_version, true);
        wp_localize_script('token-refresh-script', 'token_refresh_params', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce'    => wp_create_nonce('token_refresh_nonce')
        ));
    }
}
add_action('wp_enqueue_scripts', 'enqueue_custom_scripts');

function ajax_search_birth_locations() {
    $search_query = isset($_POST['query']) ? sanitize_text_field($_POST['query']) : '';

    add_filter('posts_where', 'search_by_title_start', 10, 2);

    $args = array(
        'post_type' => 'birth-location',
        'posts_per_page' => 10,
        's' => $search_query
    );

    $query = new WP_Query($args);

    remove_filter('posts_where', 'search_by_title_start', 10, 2);

    $results = array();
    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            $results[] = array(
                'id' => get_the_ID(),
                'title' => get_the_title(),
            );
        }
    }

    wp_send_json_success($results);
}
add_action('wp_ajax_nopriv_search_birth_locations', 'ajax_search_birth_locations');
add_action('wp_ajax_search_birth_locations', 'ajax_search_birth_locations');

function search_by_title_start($where, $wp_query) {
    global $wpdb;
    if ($search_term = $wp_query->get('s')) {
        $where .= $wpdb->prepare(" AND {$wpdb->posts}.post_title LIKE %s", $wpdb->esc_like($search_term) . '%');
    }
    return $where;
}

// AJAX handler for token refresh
function refresh_token_counter() {
    check_ajax_referer('token_refresh_nonce', 'nonce');

    $response = array();

    // Check if the shortcode [show_tokens] exists and is accessible
    if (shortcode_exists('show_tokens')) {
        $response['show_tokens'] = do_shortcode('[show_tokens module="chat"]');
    } else {
        $response['show_tokens'] = 'Token counter is not available.';
    }

    echo json_encode($response);
    wp_die(); // This is required to terminate immediately and return a proper response
}
add_action('wp_ajax_refresh_token_counter', 'refresh_token_counter');

function get_user_views_shortcode($atts) {
    global $wpdb;

    // Get the current logged-in user's ID
    $current_user_id = get_current_user_id();

    // Check if a user is logged in
    if ($current_user_id == 0) {
        return 'User not logged in';
    }

    // Use the correct table prefix
    $table_name = $wpdb->prefix . 'peepso_users';

    // Prepare the SQL query to fetch the usr_views value
    $query = $wpdb->prepare("SELECT usr_views FROM $table_name WHERE usr_id = %d", $current_user_id);

    // Execute the query
    $usr_views = $wpdb->get_var($query);

    // Check if the value is found
    if ($usr_views !== null) {
        return $usr_views;
    } else {
        return 'Views not found';
    }
}

// Register the shortcode
add_shortcode('user_views', 'get_user_views_shortcode');

function get_user_location_shortcode($atts) {
    // Define shortcode attributes
    $atts = shortcode_atts(array(
        'user_id' => 0
    ), $atts);

    // Get the user ID from the attributes or use the current logged-in user ID
    $user_id = $atts['user_id'] ? $atts['user_id'] : get_current_user_id();

    // Check if a user is logged in or user ID is provided
    if ($user_id == 0) {
        return 'User not logged in';
    }

    // Get the user location meta
    $user_location_meta = get_user_meta($user_id, 'peepso_user_field_location', true);

    // Check if the location is set
    if ($user_location_meta) {
        // Unserialize the meta value to get the location data
        $location_data = maybe_unserialize($user_location_meta);

        // Check if the location name is set
        if (isset($location_data['name']) && !empty($location_data['name'])) {
            return $location_data['name'];
        }
    }

    // If the location is not set, return "Not Yet Set"
    return 'Not Yet Set';
}

// Register the shortcode
add_shortcode('user_location', 'get_user_location_shortcode');

function count_peepso_friends_cache() {
    // Ensure the user is logged in
    if ( !is_user_logged_in() ) {
        return 'You need to be logged in to see this data.';
    }

    // Get the current user's ID
    $user_id = get_current_user_id();

    // Get the global $wpdb object
    global $wpdb;

    // Prepare the table name (you might need to adjust this if you have a table prefix)
    $table_name = $wpdb->prefix . 'peepso_friends_cache';

    // Query the database to count the rows where user_id matches the current user
    $count = $wpdb->get_var( $wpdb->prepare(
        "SELECT COUNT(*) FROM $table_name WHERE user_id = %d",
        $user_id
    ) );

    // Return the count to display in the shortcode
    return $count;
}

// Register the shortcode
add_shortcode( 'count_friends', 'count_peepso_friends_cache' );

function peepso_display_user_id() {
    $view_user_id = PeepSoProfileShortcode::get_instance()->get_view_user_id();
    return $view_user_id;
}
add_shortcode('peepso_user_id', 'peepso_display_user_id');

function display_star_rating($atts) {
    // Extract shortcode attributes
    $atts = shortcode_atts(array(
        'post_id' => get_the_ID(),
    ), $atts);

    // Retrieve the post ID from the attributes
    $post_id = $atts['post_id'];

    // Retrieve the meta values
    $knowledge = get_post_meta($post_id, 'testimonial_hd_knowledge', true);
    $communication = get_post_meta($post_id, 'testimonial_communication_skills', true);
    $personality = get_post_meta($post_id, 'testimonial_personality', true);

    // Ensure they are numbers and not empty
    $knowledge = is_numeric($knowledge) ? $knowledge : 0;
    $communication = is_numeric($communication) ? $communication : 0;
    $personality = is_numeric($personality) ? $personality : 0;

    // Calculate the average
    $average = ($knowledge + $communication + $personality) / 3;

    // Round to the nearest half
    $average = round($average * 2) / 2;

    // Generate star rating HTML
    $star_rating = '<div class="star-rating">';
    for ($i = 1; $i <= 5; $i++) {
        if ($average >= $i) {
            $star_rating .= '<span class="star full-star">&#9733;</span>'; // Full star
        } elseif ($average >= $i - 0.5) {
            $star_rating .= '<span class="star half-star">&#9733;</span>'; // Half star
        } else {
            $star_rating .= '<span class="star empty-star">&#9734;</span>'; // Empty star
        }
    }
    $star_rating .= '</div>';

    return $star_rating;
}

add_shortcode('star_rating', 'display_star_rating');

function humandesign_profile_button_shortcode($atts) {
    // Extract shortcode attributes
    $atts = shortcode_atts(array(
        'user_id' => 0,
    ), $atts);

    // Get user info
    $user_id = intval($atts['user_id']);
    $user_info = get_userdata($user_id);

    if ($user_info) {
        $user_nicename = $user_info->user_nicename;
        $profile_url = "https://app.humandesign.ai/profile/" . $user_nicename;

        // Generate the button HTML
        $button_html = '<a href="' . esc_url($profile_url) . '" class="humandesign-profile-button" target="_blank">View Profile</a>';

        return $button_html;
    }

    return '';
}

add_shortcode('humandesign_profile_button', 'humandesign_profile_button_shortcode');


function wp_nicename_shortcode($atts) {
    // Extract shortcode attributes
    $atts = shortcode_atts(array(
        'user_id' => 0,
    ), $atts);

    // Get the user ID
    $user_id = intval($atts['user_id']);

    // Get user info
    $user_info = get_userdata($user_id);

    if ($user_info) {
        // Return the user nicename
        return esc_html($user_info->user_nicename);
    }

    return '';
}

add_shortcode('wp_nicename', 'wp_nicename_shortcode');

function set_default_featured_image($post_id) {
    // Check if the post is a product and if it has a featured image
    if (get_post_type($post_id) == 'product' && !has_post_thumbnail($post_id)) {
        // Set the ID of the default image
        $default_image_id = 340055; // Replace with your default image ID

        // Set the default featured image
        set_post_thumbnail($post_id, $default_image_id);
    }
}
add_action('save_post', 'set_default_featured_image');
add_action('wp_insert_post', 'set_default_featured_image');

function post_status_shortcode($atts) {
    $atts = shortcode_atts(
        array(
            'id' => get_the_ID(), // Default to current post ID
        ), 
        $atts, 
        'post_status'
    );

    $post_id = intval($atts['id']);
    $post_status = get_post_status($post_id);
    
    switch ($post_status) {
        case 'publish':
            $status_display = '<i class="gcis gci-globe"></i> Public';
            break;
        case 'private':
            $status_display = '<i class="gcis gci-lock"></i> Private';
            break;
        case 'password':
            $status_display = '<i class="gcis gci-lock"></i> Password Protected';
            break;
        default:
            $status_display = '<i class="fas fa-question-circle"></i> Unknown Status';
            break;
    }

    return $status_display;
}
add_shortcode('post_status', 'post_status_shortcode');



// Change the login logo
function custom_login_logo() {
    ?>
    <style type="text/css">
        #login h1 a, .login h1 a {
            background-image: url('https://cdn.humandesign.ai/media/2023/10/human-design-logo.png');
            height: 65px;
            width: 65px;
            background-size: contain;
            background-repeat: no-repeat;
            padding-bottom: 30px;
        }
    </style>
    <?php
}
add_action('login_enqueue_scripts', 'custom_login_logo');

// Change the login logo URL
function custom_login_logo_url() {
    return 'https://humandesign.ai';
}
add_filter('login_headerurl', 'custom_login_logo_url');

// Change the login logo URL title
function custom_login_logo_url_title() {
    return 'Human Design';
}
add_filter('login_headertitle', 'custom_login_logo_url_title');

add_filter( 'pmpro_login_redirect', '__return_false' );
 
function my_pmpro_default_registration_level( $user_id ) {
 
	// Give all members who register membership level 1
	pmpro_changeMembershipLevel( 1, $user_id );
 
}
add_action( 'user_register', 'my_pmpro_default_registration_level' );

// Add a custom dynamic content tag for nicename
add_action('cornerstone_dynamic_content_setup', function() {
    cornerstone_dynamic_content_register_source(array(
        'name' => 'custom_user_data', // Source name
        'label' => __('Custom User Data', 'textdomain'), // Display name
        'priority' => 10,
        'fields' => array(
            'nicename' => __('User Nicename', 'textdomain')
        )
    ));
});

// Provide the data for the custom dynamic content tag
add_filter('cornerstone_dynamic_content_custom_user_data', function($result, $field, $args) {
    if ($field === 'nicename') {
        $current_user = wp_get_current_user();
        if ($current_user->exists()) {
            $result = $current_user->user_nicename;
        }
    }
    return $result;
}, 10, 3);

// Disable Gutenberg editor
add_filter('use_block_editor_for_post', '__return_false', 10);

// Disable Gutenberg editor for widgets
add_filter('use_widgets_block_editor', '__return_false');

function app_purchase_shortcode($atts) {
    // Attributes
    $atts = shortcode_atts(
        array(
            'type' => 'subscription', // Default to subscription, can be 'consumable'
            'product_identifier' => 'personalmonthly',
            'length_of_time' => '0',
            'level' => 'personal',
            'token_amount' => '50000'
        ),
        $atts,
        'app_purchase'
    );

    // Subscription button URL
    $subscription_url = esc_url("https://app.humandesign.ai/app-payments/buy_subscription.php?product_identifier=" . $atts['product_identifier'] . "&length_of_time=" . $atts['length_of_time'] . "&level=" . $atts['level']);
    
    // Token purchase button URL
    $token_url = esc_url("https://app.humandesign.ai/app-payments/buy_consumable.php?product_identifier=" . $atts['product_identifier'] . "&token_amount=" . $atts['token_amount']);

    // HTML for the button based on type
    if ($atts['type'] == 'subscription') {
        $html = '<div style="margin-bottom: 10px; width: 100%;">
                    <a href="' . $subscription_url . '" style="color: #ffffff; background-color: green; font-size: 16px; display: block; width: 100%; border-radius: 5px; text-decoration: none; font-weight: normal; font-style: normal; padding: 0.8rem 1rem; border-color: transparent; text-align: center;">Start Your Membership</a>
                 </div>';
    } else if ($atts['type'] == 'consumable') {
        $html = '<div style="width: 100%;">
                    <a href="' . $token_url . '" style="color: #ffffff; background-color: green; font-size: 16px; display: block; width: 100%; border-radius: 5px; text-decoration: none; font-weight: normal; font-style: normal; padding: 0.8rem 1rem; border-color: transparent; text-align: center;">Purchase ' . number_format($atts['token_amount']) . ' Tokens</a>
                 </div>';
    } else {
        $html = '<div>Invalid type specified. Please use "subscription" or "consumable".</div>';
    }

    return $html;
}
add_shortcode('app_purchase', 'app_purchase_shortcode');

function display_user_role() {
    if (is_user_logged_in()) {
        $current_user = wp_get_current_user();
        $roles = $current_user->roles;
        if (!empty($roles)) {
            // Check if the user has the 'subscriber' role and display 'Free Member' instead
            $roles = array_map(function($role) {
                return ($role === 'subscriber') ? 'Free Member' : ucfirst($role);
            }, $roles);
            return implode(', ', $roles);
        } else {
            return 'No role assigned.';
        }
    } else {
        return 'User not logged in.';
    }
}
add_shortcode('user_role', 'display_user_role');

// Function to check if a string is JSON
function is_json($string) {
    if (!is_string($string)) {
        return false;
    }
    json_decode($string);
    return (json_last_error() == JSON_ERROR_NONE);
}

// Function to pretty print JSON data recursively in a flat format
function pretty_print_flat_recursive($data, $prefix = '') {
    $string = '';

    foreach ($data as $key => $value) {
        if (is_object($value) || is_array($value)) {
            $string .= pretty_print_flat_recursive($value, $prefix . $key . '.');
        } else {
            $string .= $prefix . $key . ': ' . $value . ', ';
        }
    }

    return rtrim($string, ', ');
}

// Function to get bodygraph data for the current user in a flat format
function get_bgc_data_flat() {
    $user_id = get_current_user_id(); // Gets the ID of the current user
    $meta_key = '_bgc_data'; // The meta key where your data is stored

    $data = get_user_meta($user_id, $meta_key, true); // Retrieves the data
    error_log('Raw user meta data: ' . print_r($data, true)); // Debugging line

    // Check if the data is JSON and decode it
    if (is_json($data)) {
        $data = json_decode($data, true);
        error_log('Decoded JSON user meta data: ' . print_r($data, true)); // Debugging line
    }

    return pretty_print_flat_recursive($data); // Converts the object to a flat human-readable string
}

// Function to get bodygraph data for a specific post in a flat format
function get_bgc_data_post_flat($post_id = null) {
    if (!$post_id) {
        global $post;
        $post_id = isset($post->ID) ? $post->ID : 0;
    }
    
    $meta_key = '_bgc_data';
    $data = get_post_meta($post_id, $meta_key, true);
    error_log('Raw post meta data: ' . print_r($data, true)); // Debugging line

    // Check if the data is JSON and decode it
    if (is_json($data)) {
        $data = json_decode($data, true);
        error_log('Decoded JSON post meta data: ' . print_r($data, true)); // Debugging line
    }

    return pretty_print_flat_recursive($data);
}

// Add shortcodes for bodygraph data
add_shortcode('bgc_data_flat', 'get_bgc_data_flat');
add_shortcode('bgc_data_post_flat', 'get_bgc_data_post_flat');

// Function to get and sanitize the post title
function get_sanitized_post_title($post_id) {
    $post_title = get_the_title($post_id);
    return sanitize_text_field($post_title);
}

// Function to get and sanitize the post content
function get_sanitized_post_content($post_id) {
    $post_content = get_post_field('post_content', $post_id);
    return sanitize_text_field(wp_strip_all_tags($post_content));
}

// FACETWP

add_filter( 'facetwp_indexer_query_args', function( $args ) {
  $args['post_status'] = [ 'publish', 'inherit', 'private', 'draft', 'pending' ];
  return $args;
});
add_filter( 'facetwp_facet_dropdown_show_counts', '__return_false' );

//FLUENTFORMS

// Add the custom shortcode to Fluent Forms editor
add_filter('fluentform/editor_shortcodes', function ($smartCodes) {
$smartCodes[0]['shortcodes']['{post_status}'] = 'Embedded Post Status';
return $smartCodes;
});

// Register the custom smart code handler
add_filter('fluentform/editor_shortcode_callback_post_status', function ($value, $form) {
global $post;
if ($post) {
$value = get_post_status($post->ID);
} else {
$value = 'No Post Found';
}
return $value;
}, 10, 2);

// Add the smartcode to the list
add_filter('fluentform/editor_shortcodes', function ($smartCodes) {
    $smartCodes[0]['shortcodes']['{peepso_user_id}'] = 'PeepSo User ID';
    return $smartCodes;
});

// Define the smart code to fetch the PeepSo user profile ID
add_filter('fluentform/editor_shortcode_callback_peepso_user_id', function ($value, $form) {
    // Get the PeepSo user profile ID
    $view_user_id = PeepSoProfileShortcode::get_instance()->get_view_user_id();
    if ($view_user_id) {
        return $view_user_id;
    }
    return 'No User Found';
}, 10, 2);


add_action('fluentform/before_insert_submission', function ($insertData, $data, $form) {

    if($form->id != 32) { // 23 is your form id. Change the 23 with your own login for ID
        return;
    }

    $redirectUrl = home_url(); // You can change the redirect url after successful login

    // if you have a field as refer_url as hidden field and value is: {http_referer} then
    // We can use that as a redirect URL. We will redirect if it's the same domain
    // If you want to redirect to a fixed URL then remove the next 3 lines
    if(!empty($data['refer_url']) && strpos($data['refer_url'], site_url()) !== false) {
        $redirectUrl = $data['refer_url'];
    }

    if (get_current_user_id()) { // user already registered
        wp_send_json_success([
            'result' => [
                'redirectUrl' => $redirectUrl,
                'message' => 'Your are already logged in. Redirecting now...'
            ]
        ]);
    }

    $email = \FluentForm\Framework\Helpers\ArrayHelper::get($data, 'email'); // your form should have email field
    $password = \FluentForm\Framework\Helpers\ArrayHelper::get($data, 'password'); // your form should have password field

    if(!$email || !$password) {
        wp_send_json_error([
            'errors' => ['Please provide email and password']
        ], 423);
    }

    $user = get_user_by_email($email);
    if($user && wp_check_password($password, $user->user_pass, $user->ID)) {
        wp_clear_auth_cookie();
        wp_set_current_user($user->ID);
        wp_set_auth_cookie($user->ID);
        /* user is not logged in.
        * If you use wp_send_json_success the the submission will not be recorded
        * If you remove the wp_send_json_success then it will record the data in fluentform
        * in that case you should redirect the user on form submission settings
        */
        wp_send_json_success([
            'result' => [
                'redirectUrl' => $redirectUrl,
                'message' => 'You are logged in, please wait a moment..'
            ]
        ]);
    } else {
        // password or user don't match
        wp_send_json_error([
            'errors' => ['Email / password is not correct']
        ], 423);
    }
}, 10, 3);

add_action('fluentform/subscription_payment_canceled_stripe', function($subscription, $submission, $oldStatus) {
    $vendorCustomerId = $subscription->vendor_customer_id;

    // Prepare data for the webhook
    $webhook_url = 'https://app.humandesign.ai/wp-json/uap/v2/uap-399488-399489';
    $data = [
        'User ID' => $vendorCustomerId
    ];

    // Convert the data to JSON
    $json_data = json_encode($data);
    error_log("Webhook data for cancellation: " . print_r($data, true));

    // Initialize cURL session
    $ch = curl_init($webhook_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);

    // Execute cURL session and capture response
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    // Check for cURL errors
    if (curl_errno($ch)) {
        error_log('cURL error: ' . curl_error($ch));
    }

    // Check for HTTP response code
    if ($http_code != 200) {
        error_log("Webhook failed with HTTP code $http_code. Response: $response");
    } else {
        // Log successful webhook
        error_log("Webhook successful. Response: $response");
    }

    // Close cURL session
    curl_close($ch);
}, 10, 3);

//PEEPSO

add_filter('peepso_navigation_profile', function($links) {
    if(isset($links['photos'])) { unset($links['photos']); }
    return $links;
},9999);

add_filter('peepso_navigation_profile', function($links) {
    if(isset($links['media'])) { unset($links['media']); }
    return $links;
},9999);

add_filter('peepso_navigation_profile', function($links) {
    if(isset($links['files'])) { unset($links['files']); }
    return $links;
},9999);

// Remove a tab
add_filter('peepso_navigation_profile', function($links) {
    if(isset($links['about'])) { unset($links['about']); }
    return $links;
},9999);

add_filter('peepso_navigation_profile', function($links) {
    if(isset($links['blogposts'])) { $links['blogposts']['label'] = 'Articles'; }
    return $links;
},9999);

//PAIDMEMBERSHIPPRO

function update_user_meta_with_membership_level($user_id, $level_id) {
    if ($level_id > 0) {
        // User has a membership, update the user meta table with the membership level
        update_user_meta($user_id, 'active_membership_level', $level_id);
    } else {
        // User cancels their membership, set to level 1
        update_user_meta($user_id, 'active_membership_level', 1);
    }
}

// Hook into the membership change action in Paid Memberships Pro
add_action('pmpro_after_change_membership_level', 'update_user_meta_with_membership_level', 10, 2);

function ensure_cancelled_membership_to_level_1($user_id, $level_id) {
    if ($level_id == 0) {
        // User cancels their membership, set to level 1
        update_user_meta($user_id, 'active_membership_level', 1);
    }
}

// Hook into the membership cancellation action in Paid Memberships Pro
add_action('pmpro_after_change_membership_level', 'ensure_cancelled_membership_to_level_1', 10, 2);

function allow_svg_uploads($mimes) {
    // Add SVG to allowed mime types
    $mimes['svg'] = 'image/svg+xml';
    return $mimes;
}
add_filter('upload_mimes', 'allow_svg_uploads');

add_filter( 'mwai_ai_context', function ( $context ) {
  // Process the [bgc_data_flat] shortcode
  $chart = do_shortcode('[bgc_data_flat]');
  
  // Replace {CHART} with the processed shortcode content
  return str_replace( '{CHART}', $chart, $context );
}, 10, 1 );

add_filter( 'mwai_stats_credits', function ( $credits, $userId ) {
  $user = get_userdata( $userId );
  if ( !empty( $user->roles) && is_array( $user->roles ) ) {
    foreach ( $user->roles as $role) {
      if ( $role === 'subscriber' ) {
        return 3;
      }
      if ( $role === 'personal' ) {
        return 5;
      }
      if ( $role === 'professional' ) {
        return 30;
      }
      if ( $role === 'vip' ) {
        return 50;
      }
    }
  }
  // This will be basically the default value set in the plugin settings
  // for logged-in users.
  return $credits;
}, 10, 2);

add_action( 'wp_footer', function() {
  ?>
    <script>
      document.addEventListener('facetwp-loaded', function() {
        if (! FWP.loaded) { // initial pageload
          FWP.hooks.addFilter('facetwp/ajax_settings', function(settings) {
            settings.headers = { 'X-WP-Nonce': FWP_JSON.nonce };
            return settings;
          });
        }
      });
    </script>
  <?php
}, 100 );
