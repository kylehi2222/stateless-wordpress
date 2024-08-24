<?php

/**
 * Admin Max extensions rows
 */
add_action("cs_max_output_admin_extensions", function() {
  // Disabled
  if(!apply_filters('cs_max_enabled', true)) {
    return;
  }

  include(__DIR__ . "/views/page-home-box-max.php");
});

// Add Max site imports to the Sites section
// of the themeco section
// This is only setup setup for personify
add_filter('cs_app_remote_assets', function($cached) {
  $courses = cs_max_get_courses();

  // Loop courses
  foreach ($courses['data'] as $courseData) {
    // Not purchased
    if (empty($courseData['purchased'])) {
      continue;
    }

    // Loop course items
    foreach ($courseData['course'] as $course) {
      // Not a site import
      if ($course['tcoFileType'] !== 'site') {
        continue;
      }

      $sanitized = sanitize_title($course['title']);

      $cached['templates']['sites'][] = [
        'id' => $course['tcoFile'],
        'title' => $course['title'],
        'type' => 'site',
        'preview' => 'https://theme.co/app/uploads/personify/tiles/' . $sanitized . '.png',
        'demo_url' => 'https://theme.co/personify/' . $sanitized,
        'isRemote' => true,
      ];
    }
  }

  return $cached;
});
