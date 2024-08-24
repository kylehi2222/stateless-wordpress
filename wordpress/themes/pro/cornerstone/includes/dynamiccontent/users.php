<?php

/**
 * Author preview grab with URL as the value
 * for choices in a select
 */
cs_dynamic_content_register_dynamic_option('author_preview', [
  'type' => 'select',
  'label' => __('Author Preview', CS_LOCALIZE),
  'filter' => function($results, $args) {
    $users = get_users();
    $out = [];

    foreach ($users as $user) {
      // Setup select value
      $authorPreview = [
        'value' => get_author_posts_url($user->ID),
        'label' => $user->display_name . ' (' . $user->user_login . ')',
      ];

      $out[] = $authorPreview;
    }

    return $out;
  },
]);
