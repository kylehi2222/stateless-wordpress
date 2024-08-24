<?php

/**
 * Filter to add typography controls
 */
add_filter("cs_theme_options_portfolio_group", function() {
  $condition_portfolio_enable = [ 'x_portfolio_enable' => true ];

  return [
    'type'  => 'group-sub-module',
    'label' => __( 'Portfolio', '__x__' ),
    'options' => [ 'tag' => 'portfolio', 'name' => 'x-theme-options:portfolio' ],
    'controls' => [
      [
        'type'        => 'group',
        // 'label'       => $labels['setup'],
        'description' => __( 'Setting your custom portfolio slug allows you to create a unique URL structure for your archive pages that suits your needs. When you update it, remember to save your Permalinks again to avoid any potential errors.', '__x__' ),
        'controls'    => [
          [
            'key'     => 'x_portfolio_enable',
            'type'    => 'toggle',
            'label'   => __( 'Enable Portfolio', '__x__' ),
            'options' => cs_recall( 'options_group_toggle_off_on_bool_string' ),
          ],
          [
            'key'   => 'x_custom_portfolio_slug',
            'type'  => 'text',
            'label' => __( 'Custom URL Slug', '__x__' ),
            'conditions' => [ $condition_portfolio_enable ]
          ],
          [
            'key'     => 'x_portfolio_enable_cropped_thumbs',
            'type'    => 'toggle',
            'label'   => __( 'Crop Featured', '__x__' ),
            'conditions' => [ $condition_portfolio_enable ],
            'options' => cs_recall( 'options_group_toggle_off_on_bool_string' ),
          ],
          [
            'key'   => 'x_portfolio_enable_post_meta',
            'type'  => 'toggle',
            'label' => __( 'Post Meta', '__x__' ),
            'conditions' => [ $condition_portfolio_enable ]
          ],
        ],
      ], [
        'type' => 'group',
        'labels' => __( 'Labels', '__x__' ),
        'description' => __( 'Set the titles and labels for various parts of the portfolio here.', '__x__' ),
        'conditions' => [ $condition_portfolio_enable ],
        'controls'    => [
          [
            'key'   => 'x_portfolio_tag_title',
            'type'  => 'text',
            'label' => __( 'Tag List Title', '__x__' ),
          ],
          [
            'key'   => 'x_portfolio_launch_project_title',
            'type'  => 'text',
            'label' => __( 'Launch Title', '__x__' ),
          ],
          [
            'key'   => 'x_portfolio_launch_project_button_text',
            'type'  => 'text',
            'label' => __( 'Launch Button', '__x__' ),
          ],
          [
            'key'   => 'x_portfolio_share_project_title',
            'type'  => 'text',
            'label' => __( 'Share Title', '__x__' ),
          ],
        ],
      ], [
        'type' => 'group',
        'label' => __( 'Social', '__x__' ),
        'description' => __( 'Enable various social sharing options for your portfolio items here.', '__x__' ),
        'conditions'  => [ $condition_portfolio_enable ],
        'key'         => 'x_portfolio_enable_social',
        'options'     => cs_recall( 'options_group_toggle_off_on_bool_string' ),
        'controls'    => [
          [
            'key'     => 'x_portfolio_enable_facebook_sharing',
            'type'    => 'toggle',
            'label'   => __( 'Facebook', '__x__' ),
            'options' => cs_recall( 'options_group_toggle_off_on_bool_string' ),
          ],
          [
            'key'     => 'x_portfolio_enable_twitter_sharing',
            'type'    => 'toggle',
            'label'   => __( 'Twitter', '__x__' ),
            'options' => cs_recall( 'options_group_toggle_off_on_bool_string' ),
          ],
          [
            'key'     => 'x_portfolio_enable_linkedin_sharing',
            'type'    => 'toggle',
            'label'   => __( 'LinkedIn', '__x__' ),
            'options' => cs_recall( 'options_group_toggle_off_on_bool_string' ),
          ],
          [
            'key'     => 'x_portfolio_enable_pinterest_sharing',
            'type'    => 'toggle',
            'label'   => __( 'Pinterest', '__x__' ),
            'options' => cs_recall( 'options_group_toggle_off_on_bool_string' ),
          ],
          [
            'key'     => 'x_portfolio_enable_reddit_sharing',
            'type'    => 'toggle',
            'label'   => __( 'Reddit', '__x__' ),
            'options' => cs_recall( 'options_group_toggle_off_on_bool_string' ),
          ],
          [
            'key'     => 'x_portfolio_enable_email_sharing',
            'type'    => 'toggle',
            'label'   => __( 'Email', '__x__' ),
            'options' => cs_recall( 'options_group_toggle_off_on_bool_string' ),
          ],
        ],
      ]
    ]
  ];
});
