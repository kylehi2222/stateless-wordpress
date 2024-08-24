<?php

/**
 * Filter to add Font Awesome controls
 */

add_filter("cs_theme_options_social_group", function() {

  return [
    'type'  => 'group-sub-module',
    'label' => __( 'Social', '__x__' ),
    'options' => [ 'tag' => 'social', 'name' => 'x-theme-options:social' ],
    'controls' => [
      [
        'type'        => 'group',
        // 'label'       => $labels['setup'],
        'description' => __( 'Set the URLs for your social media profiles here to be used in the topbar and bottom footer. Adding in a link will make its respective icon show up without needing to do anything else. Keep in mind that these sections are not necessarily intended for a lot of items, so adding all icons could create some layout issues. It is typically best to keep your selections here to a minimum for structural purposes and for usability purposes so you do not overwhelm your visitors.', '__x__' ),
        'controls'    => [
          [
            'key'   => 'x_social_facebook',
            'type'  => 'text',
            'label' => __( 'Facebook', '__x__' ),
          ],
          [
            'key'   => 'x_social_twitter',
            'type'  => 'text',
            'label' => __( 'Twitter', '__x__' ),
          ],
          [
            'key'   => 'x_social_linkedin',
            'type'  => 'text',
            'label' => __( 'LinkedIn', '__x__' ),
          ],
          [
            'key'   => 'x_social_xing',
            'type'  => 'text',
            'label' => __( 'XING', '__x__' ),
          ],
          [
            'key'   => 'x_social_foursquare',
            'type'  => 'text',
            'label' => __( 'Foursquare', '__x__' ),
          ],
          [
            'key'   => 'x_social_youtube',
            'type'  => 'text',
            'label' => __( 'YouTube', '__x__' ),
          ],
          [
            'key'   => 'x_social_vimeo',
            'type'  => 'text',
            'label' => __( 'Vimeo', '__x__' ),
          ],
          [
            'key'   => 'x_social_instagram',
            'type'  => 'text',
            'label' => __( 'Instagram', '__x__' ),
          ],
          [
            'key'   => 'x_social_pinterest',
            'type'  => 'text',
            'label' => __( 'Pinterest', '__x__' ),
          ],
          [
            'key'   => 'x_social_dribbble',
            'type'  => 'text',
            'label' => __( 'Dribbble', '__x__' ),
          ],
          [
            'key'   => 'x_social_flickr',
            'type'  => 'text',
            'label' => __( 'Flickr', '__x__' ),
          ],
          [
            'key'   => 'x_social_github',
            'type'  => 'text',
            'label' => __( 'GitHub', '__x__' ),
          ],
          [
            'key'   => 'x_social_behance',
            'type'  => 'text',
            'label' => __( 'Behance', '__x__' ),
          ],
          [
            'key'   => 'x_social_tumblr',
            'type'  => 'text',
            'label' => __( 'Tumblr', '__x__' ),
          ],
          [
            'key'   => 'x_social_whatsapp',
            'type'  => 'text',
            'label' => __( 'Whatsapp', '__x__' ),
          ],
          [
            'key'   => 'x_social_soundcloud',
            'type'  => 'text',
            'label' => __( 'SoundCloud', '__x__' ),
          ],
          [
            'key'   => 'x_social_rss',
            'type'  => 'text',
            'label' => __( 'RSS Feed', '__x__' ),
          ],
        ],
      ], [
        'type' => 'group',
        'label' => __( 'Open Graph', '__x__' ),
        'description' => __( 'X outputs standard Open Graph tags for your content. If you are employing another solution for this, you can disable X\'s Open Graph tag output here.', '__x__' ),
        'controls'    => [
          [
            'key'   => 'x_social_open_graph',
            'type'  => 'toggle',
            'label' => __( 'Open Graph', '__x__' ),
          ],
          [
            'key'   => 'x_social_fallback_image',
            'type'  => 'image',
            'label' => __( 'Social Fallback', '__x__' ),
          ],
        ],
      ]
    ]
  ];
});
