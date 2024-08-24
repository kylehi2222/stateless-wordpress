<?php


/**
 * Add condition controls
 */
add_filter("cs_assignment_contexts", function($contexts) {
  // Controls for post password
  // is and is not required
  $controls = [
    'key'    => 'single:post-password-required',
    'label'  => __('Post Password', 'cornerstone'),
    'toggle' => [
      'type'   => 'boolean',
      'labels' => [
        sprintf(csi18n('app.conditions.is-condition'), csi18n('app.conditions.required') ),
        sprintf(csi18n('app.conditions.is-not-condition'), csi18n('app.conditions.required') )
      ]
    ],
  ];

  // Add to single assignment group
  // This will get auto added to elements using that
  $contexts['controls']['single'][] = $controls;

  // Add to WooCommerce too
  if (isset($contexts['controls']['single-wc'])) {
    $contexts['controls']['single-wc'][] = $controls;
  }

  return $contexts;
}, 100);


/**
 * Condition to check single:post-password-required
 */
add_filter("cs_condition_rule_single_post_password_required", function() {
  return post_password_required();
});
