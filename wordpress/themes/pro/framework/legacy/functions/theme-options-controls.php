<?php

// =============================================================================
// THEME-OPTIONS.PHP
// -----------------------------------------------------------------------------
// Registers controls for the Theme Options page. Below is a table on how to
// setup conditions as needed.
// -----------------------------------------------------------------------------
// Standard                         | $condition = array(
//                                  |   'option' => 'x_stack',
//                                  |   'value'  => 'renew',
//                                  |   'op'     => '='
//                                  | )
// -----------------------------------------------------------------------------
// Simplified (assumes '=' as 'op') | $condition = array(
//                                  |   'x_stack' => 'renew'
//                                  | )
// -----------------------------------------------------------------------------
// Single                           | 'condition' => $condition
// -----------------------------------------------------------------------------
// Multiple                         | 'conditions' => array(
//                                  |   $condition,
//                                  |   $another_condition
//                                  | )
// =============================================================================

// =============================================================================
// TABLE OF CONTENTS
// -----------------------------------------------------------------------------
//   01. Setup
//   02. Helpers
//   03. Theme Options Map
//       a. Choices
//       b. Options
//       c. Conditions
//       d. Labels
//       e. Groups
//       f. Controls
//       g. Stacks
//       h. Integrity
//       i. Renew
//       j. Icon
//       k. Ethos
//       l. Layout and Design
//       m. Typography
//       n. Buttons
//       o. Headers
//       p. Footers
//       q. Blog
//       r. Portfolio
//       s. Social
//       t. Miscellaneous
//       u. bbPress
//       v. BuddyPress
//       w. WooCommerce
//       x. Output
//   04. Integration
// =============================================================================

// Helpers
// =============================================================================

function x_wrap_choices( $choices ) {
  return [ 'choices' => $choices ];
}



// Theme Options Map
// =============================================================================

function x_theme_options_register() {

//#region Choices
// -----------------


  $choices_light_dark = [
    [ 'value' => 'light', 'label' => __( 'Light', '__x__' ) ],
    [ 'value' => 'dark',  'label' => __( 'Dark', '__x__' )  ],
  ];

  $choices_section_layouts = [
    [ 'value' => 'sidebar',    'label' => __( 'Global', '__x__' )    ],
    [ 'value' => 'full-width', 'label' => __( 'Fullwidth', '__x__' ) ],
  ];

  $choices_renew_entry_icon_position = [
    [ 'value' => 'standard', 'label' => __( 'Standard', '__x__' ) ],
    [ 'value' => 'creative', 'label' => __( 'Creative', '__x__' ) ],
  ];

  $choices_ethos_post_carousel_and_slider_display = [
    [ 'value' => 'most-commented', 'label' => __( 'Most Commented', '__x__' ) ],
    [ 'value' => 'random',         'label' => __( 'Random', '__x__' )         ],
    [ 'value' => 'featured',       'label' => __( 'Featured', '__x__' )       ],
  ];

  $choices_widget_areas = [
    [ 'value' => '0', 'icon' => 'ui:none' ],
    [ 'value' => '1', 'label' => '1'      ],
    [ 'value' => '2', 'label' => '2'      ],
    [ 'value' => '3', 'label' => '3'      ],
    [ 'value' => '4', 'label' => '4'      ],
  ];

  $choices_blog_styles = [
    [ 'value' => 'standard', 'label' => __( 'Standard', '__x__' ) ],
    [ 'value' => 'masonry',  'label' => __( 'Masonry', '__x__' )  ],
  ];

  $choices_masonry_columns = [
    [ 'value' => '2', 'label' => '2' ],
    [ 'value' => '3', 'label' => '3' ],
  ];

  $choices_list_font_families = 'list:fonts';
  $choices_list_font_weights  = 'list:font-weights';

  $choices_button_style = [
    [ 'value' => 'real',        'label' => __( '3D', '__x__' )          ],
    [ 'value' => 'flat',        'label' => __( 'Flat', '__x__' )        ],
    [ 'value' => 'transparent', 'label' => __( 'Transparent', '__x__' ) ],
  ];

  $choices_button_shape = [
    [ 'value' => 'square',  'label' => __( 'Square', '__x__' )  ],
    [ 'value' => 'rounded', 'label' => __( 'Rounded', '__x__' ) ],
    [ 'value' => 'pill',    'label' => __( 'Pill', '__x__' )    ],
  ];

  $choices_button_size = [
    [ 'value' => 'mini',    'label' => __( 'Mini', '__x__' )        ],
    [ 'value' => 'small',   'label' => __( 'Small', '__x__' )       ],
    [ 'value' => 'regular', 'label' => __( 'Regular', '__x__' )     ],
    [ 'value' => 'large',   'label' => __( 'Large', '__x__' )       ],
    [ 'value' => 'x-large', 'label' => __( 'Extra Large', '__x__' ) ],
    [ 'value' => 'jumbo',   'label' => __( 'Jumbo', '__x__' )       ],
  ];

  $choices_navbar_position = [
    [ 'value' => 'static-top',  'label' => __( 'Static Top', '__x__' )  ],
    [ 'value' => 'fixed-top',   'label' => __( 'Fixed Top', '__x__' )   ],
    [ 'value' => 'fixed-left',  'label' => __( 'Fixed Left', '__x__' )  ],
    [ 'value' => 'fixed-right', 'label' => __( 'Fixed Right', '__x__' ) ],
  ];

  $choices_navbar_scrolling = [
    [ 'value' => 'overflow-scroll',  'label' => __( 'On (no submenu support)', '__x__' ) ],
    [ 'value' => 'overflow-visible', 'label' => __( 'Off', '__x__' )                     ],
  ];

  $choices_inline_or_stacked = [
    [ 'value' => 'inline',  'label' => __( 'Inline', '__x__' )  ],
    [ 'value' => 'stacked', 'label' => __( 'Stacked', '__x__' ) ],
  ];


//#endregion

//#region Options
// -----------------


  $options_renew_entry_icon_horizontal_alignment = [
    'available_units' => [ '%' ],
    'fallback_value'  => '18%',
    'ranges'          => [
      '%' => [ 'min' => 0, 'max' => 50, 'step' => 0.5 ],
    ],
  ];

  $options_renew_entry_icon_vertical_alignment = [
    'available_units' => [ 'px' ],
    'fallback_value'  => '25px',
    'ranges'          => [
      'px' => [ 'min' => 0, 'max' => 50, 'step' => 1 ],
    ],
  ];

  $options_icon_color_labels = [
    'label'        => __( 'Text', '__x__' ),
    'alt_label'    => __( 'Background', '__x__' ),
    'swatch_label' => __( 'Select', '__x__' ),
  ];

  $options_ethos_posts_per_page = [
    'unit_mode' => 'unitless',
    'min'       => 1,
    'max'       => 10,
    'step'      => 1,
  ];

  $options_ethos_posts_display = [
    'unit_mode' => 'unitless',
    'min'       => 1,
    'max'       => 5,
    'step'      => 1,
  ];

  $options_ethos_post_slider_height = [
    'available_units' => [ 'px' ],
    'fallback_value'  => '425px',
    'ranges'          => [
      'px' => [ 'min' => 0, 'max' => 600, 'step' => 5 ],
    ],
  ];

  $options_letter_spacing_compressed = [
    'available_units' => [ 'em' ],
    'fallback_value'  => '-0.015em',
    'ranges'          => [
      'em' => [ 'min' => -0.05, 'max' => 0.25, 'step' => 0.005 ],
    ],
  ];

  $options_navbar_top_height = [
    'available_units' => [ 'px' ],
    'fallback_value'  => '90px',
    'ranges'          => [
      'px' => [ 'min' => 50, 'max' => 150, 'step' => 5 ],
    ],
  ];

  $options_navbar_side_width = [
    'available_units' => [ 'px' ],
    'fallback_value'  => '235px',
    'ranges'          => [
      'px' => [ 'min' => 200, 'max' => 300, 'step' => 5 ],
    ],
  ];

  $options_logobar_adjust_spacing = [
    'available_units' => [ 'px' ],
    'fallback_value'  => '15px',
    'ranges'          => [
      'px' => [ 'min' => 0, 'max' => 50, 'step' => 1 ],
    ],
  ];

  $options_logo_font_size = [
    'available_units' => [ 'px' ],
    'fallback_value'  => '42px',
    'ranges'          => [
      'px' => [ 'min' => 10, 'max' => 100, 'step' => 1 ],
    ],
  ];

  $options_navbar_font_size = [
    'available_units' => [ 'px' ],
    'fallback_value'  => '13px',
    'ranges'          => [
      'px' => [ 'min' => 10, 'max' => 24, 'step' => 1 ],
    ],
  ];

  $options_navbar_logo_and_links_adjust_top = [
    'available_units' => [ 'px' ],
    'fallback_value'  => '22px',
    'ranges'          => [
      'px' => [ 'min' => 0, 'max' => 100, 'step' => 1 ],
    ],
  ];

  $options_navbar_logo_and_links_adjust_side = [
    'available_units' => [ 'px' ],
    'fallback_value'  => '30px',
    'ranges'          => [
      'px' => [ 'min' => 0, 'max' => 100, 'step' => 1 ],
    ],
  ];

  $options_navbar_links_top_spacing = [
    'available_units' => [ 'px' ],
    'fallback_value'  => '30px',
    'ranges'          => [
      'px' => [ 'min' => 0, 'max' => 100, 'step' => 1 ],
    ],
  ];

  $options_navbar_button_size = [
    'available_units' => [ 'px' ],
    'fallback_value'  => '24px',
    'ranges'          => [
      'px' => [ 'min' => 20, 'max' => 50, 'step' => 1 ],
    ],
  ];

  $options_navbar_button_spacing = [
    'available_units' => [ 'px' ],
    'fallback_value'  => '20px',
    'ranges'          => [
      'px' => [ 'min' => 0, 'max' => 100, 'step' => 1 ],
    ],
  ];

  $options_topbar_content = [
    'height' => 4,
  ];

  $options_blog_excerpt_length = [
    'unit_mode' => 'unitless',
    'min'       => 10,
    'max'       => 200,
    'step'      => 5,
  ];

//#endregion

//#region Conditions
// -----------------

  $condition_classic_headers_enabled              = [ 'virtual:classic_headers' => true ];
  $condition_classic_footers_enabled              = [ 'virtual:classic_footers' => true ];

  $condition_stack_is_integrity                   = [ 'x_stack' => 'integrity' ];
  $condition_stack_is_renew                       = [ 'x_stack' => 'renew' ];
  $condition_stack_is_icon                        = [ 'x_stack' => 'icon' ];
  $condition_stack_is_ethos                       = [ 'x_stack' => 'ethos' ];

  $condition_integrity_blog_header_enable         = [ 'x_integrity_blog_header_enable' => true ];
  $condition_integrity_shop_header_enable         = [ 'x_integrity_shop_header_enable' => true ];

  $condition_renew_entry_icon_creative            = [ 'x_renew_entry_icon_position' => 'creative' ];

  $condition_ethos_post_carousel_enable           = [ 'x_ethos_post_carousel_enable' => true ];
  $condition_ethos_post_slider_blog_enable        = [ 'x_ethos_post_slider_blog_enable' => true ];
  $condition_ethos_post_slider_archive_enable     = [ 'x_ethos_post_slider_archive_enable' => true ];
  $condition_ethos_filterable_index_enable        = [ 'x_ethos_filterable_index_enable' => true ];

  $condition_font_manager_disabled                = [ 'x_enable_font_manager' => false ];
  $condition_font_manager_enabled                 = [ 'x_enable_font_manager' => true ];

  $condition_button_background_color              = [ 'option' => 'x_button_style', 'value' => 'transparent', 'op' => '!=' ];
  $condition_button_bottom_color                  = [ 'option' => 'x_button_style', 'value' => [ 'flat', 'transparent' ], 'op' => 'NOT IN' ];

  $condition_navbar_fixed_left_or_right           = [ 'option' => 'x_navbar_positioning', 'value' => [ 'fixed-right', 'fixed-left' ], 'op' => 'IN' ];
  $condition_logo_navigation_stacked              = [ 'x_logo_navigation_layout' => 'stacked' ];
  $condition_header_has_widget_areas              = [ 'option' => 'x_header_widget_areas', 'value' => '0', 'op' => '!=' ];
  $condition_topbar_enabled                       = [ 'x_topbar_display' => true ];

  $condition_footer_bottom_display_enabled        = [ 'x_footer_bottom_display' => true ];
  $condition_footer_content_enabled               = [ 'x_footer_content_display' => true ];

  $condition_blog_style_masonry                   = [ 'x_blog_style' => 'masonry' ];
  $condition_archive_style_masonry                = [ 'x_archive_style' => 'masonry' ];
  $condition_blog_full_post_content_enable        = [ 'x_blog_enable_full_post_content' => false ];




//#endregion

//#region Labels
// -------------

  $labels = [
    'setup'                  => __( 'Setup', '__x__' ),
    'blog-options'           => __( 'Blog Options', '__x__' ),
    'portfolio-options'      => __( 'Portfolio Options', '__x__' ),
    'shop-options'           => __( 'Shop Options', '__x__' ),
    'typography-options'     => __( 'Typography Options', '__x__' ),
    'post-carousel'          => __( 'Post Carousel', '__x__' ),
    'post-slider-blog'       => __( 'Post Slider (Blog)', '__x__' ),
    'post-slider-archive'    => __( 'Post Slider (Archive)', '__x__' ),
    'woocommerce'            => __( 'WooCommerce', '__x__' ),
    'colors'                 => __( 'Colors', '__x__' ),
    'navbar'                 => __( 'Navbar', '__x__' ),
    'logo-and-navigation'    => __( 'Logo and Navigation', '__x__' ),
    'logo'                   => __( 'Logo', '__x__' ),
    'logo-image'             => __( 'Logo (Image)', '__x__' ),
    'logo-alignment'         => __( 'Logo (Alignment)', '__x__' ),
    'links'                  => __( 'Links', '__x__' ),
    'links-alignment'        => __( 'Links (Alignment)', '__x__' ),
    'search'                 => __( 'Search', '__x__' ),
    'mobile-button'          => __( 'Mobile Button', '__x__' ),
    'widgetbar'              => __( 'Widgetbar', '__x__' ),
    'miscellaneous'          => __( 'Miscellaneous', '__x__' ),
    'archives'               => __( 'Archives', '__x__' ),
    'content'                => __( 'Content', '__x__' ),
    'titles'                 => __( 'Titles', '__x__' ),
    'subtitles'              => __( 'Subtitles', '__x__' ),
    'columns'                => __( 'Columns', '__x__' ),
    'widgets'                => __( 'Widgets', '__x__' ),
    'enable'                 => __( 'Enable', '__x__' ),
    'navbar-cart'            => __( 'Navbar Cart', '__x__' ),
  ];

//#endregion


  // Controls
  // --------

  $sub_modules = [];

  // Stack
  // -----


  $sub_modules[] = [
    'type'  => 'group-sub-module',
    'label' => __( 'Stack Options', '__x__' ),
    'options' => [ 'tag' => 'stack', 'name' => 'x-theme-options:stack' ],
    'controls' => [
      [
        'type'        => 'group',
        // 'label'       => $labels['setup'],
        // 'description' => __( 'Renew features a gorgeous look and feel that lends a clean, modern look to your site. All of your content will take center stage with Renew in place.', '__x__' ),
        // 'description' => __( 'Icon features a stunning, modern, fullscreen design with a unique fixed sidebar layout that scolls with users on larger screens as you move down the page. The end result is attractive, app-like, and intuitive.', '__x__' ),
        // 'description' => __( 'Ethos is a magazine-centric design that works great for blogs, news sites, or anything else that is content heavy with a focus on information. Customize the appearance of various items below and take note that some of these accent colors will be used for additional elements. For example, the "Navbar Background Color" option will also update the appearance of the widget titles in your sidebar.', '__x__' ),
        'controls'    => [

          // Integrity
          // ---------

          [
            'key'        => 'x_integrity_design',
            'type'       => 'choose',
            'label'      => __( 'Design', '__x__' ),
            'options'    => [ 'choices' => $choices_light_dark ],
            'conditions' => [ $condition_stack_is_integrity ],
          ],
          [
            'key'        => 'x_integrity_topbar_transparency_enable',
            'type'       => 'toggle',
            'label'      => __( 'Transparent Topbar', '__x__' ),
            'options'    => cs_recall( 'options_choices_off_on_bool_string' ),
            'conditions' => [ $condition_stack_is_integrity, $condition_classic_headers_enabled ],
          ],
          [
            'key'        => 'x_integrity_navbar_transparency_enable',
            'type'       => 'toggle',
            'label'      => __( 'Transparent Navbar', '__x__' ),
            'options'    => cs_recall( 'options_choices_off_on_bool_string' ),
            'conditions' => [ $condition_stack_is_integrity, $condition_classic_headers_enabled ],
          ],
          [
            'key'        => 'x_integrity_footer_transparency_enable',
            'type'       => 'toggle',
            'label'      => __( 'Transparent Footer', '__x__' ),
            'options'    => cs_recall( 'options_choices_off_on_bool_string' ),
            'conditions' => [ $condition_stack_is_integrity, $condition_classic_headers_enabled ],
          ],


          // Renew
          // -----

          [
            'keys' => [
              'value' => 'x_renew_topbar_text_color',
              'alt'   => 'x_renew_topbar_link_color_hover',
            ],
            'type'       => 'color',
            'label'      => __( 'Topbar', '__x__' ),
            'options'    => cs_recall( 'options_swatch_base_interaction_labels' ),
            'conditions' => [ $condition_stack_is_renew, $condition_classic_headers_enabled ],
          ],
          [
            'key'        => 'x_renew_topbar_background',
            'type'       => 'color',
            'label'      => __( 'Topbar Background', '__x__' ),
            'conditions' => [ $condition_stack_is_renew, $condition_classic_headers_enabled ],
          ],
          [
            'key'        => 'x_renew_logobar_background',
            'type'       => 'color',
            'label'      => __( 'Logobar Background', '__x__' ),
            'conditions' => [ $condition_stack_is_renew, $condition_classic_headers_enabled ],
          ],
          [
            'key'        => 'x_renew_navbar_background',
            'type'       => 'color',
            'label'      => __( 'Navbar Background', '__x__' ),
            'conditions' => [ $condition_stack_is_renew, $condition_classic_headers_enabled ],
          ],
          [
            'key'        => 'x_renew_navbar_button_color',
            'type'       => 'color',
            'label'      => __( 'Toggle', '__x__' ),
            'conditions' => [ $condition_stack_is_renew, $condition_classic_headers_enabled ],
          ],
          [
            'keys' => [
              'value' => 'x_renew_navbar_button_background',
              'alt'   => 'x_renew_navbar_button_background_hover',
            ],
            'type'       => 'color',
            'label'      => __( 'Toggle Background', '__x__' ),
            'options'    => cs_recall( 'options_swatch_base_interaction_labels' ),
            'conditions' => [ $condition_stack_is_renew, $condition_classic_headers_enabled ],
          ],
          [
            'key'        => 'x_renew_entry_icon_color',
            'type'       => 'color',
            'label'      => __( 'Entry Icons', '__x__' ),
            'conditions' => [ $condition_stack_is_renew ],
          ],
          [
            'key'        => 'x_renew_footer_text_color',
            'type'       => 'color',
            'label'      => __( 'Footer', '__x__' ),
            'conditions' => [ $condition_stack_is_renew, $condition_classic_footers_enabled ],
          ],
          [
            'key'        => 'x_renew_footer_background',
            'type'       => 'color',
            'label'      => __( 'Footer Background', '__x__' ),
            'conditions' => [ $condition_stack_is_renew, $condition_classic_footers_enabled ],
          ],


          // Ethos
          // -----

          [
            'key'        => 'x_ethos_topbar_background',
            'type'       => 'color',
            'label'      => __( 'Topbar Background', '__x__' ),
            'conditions' => [ $condition_stack_is_ethos, $condition_classic_headers_enabled ],
          ],
          [
            'key'        => 'x_ethos_navbar_background',
            'type'       => 'color',
            'label'      => __( 'Navbar Background', '__x__' ),
            'conditions' => [ $condition_stack_is_ethos, $condition_classic_headers_enabled ],
          ],
          [
            'key'        => 'x_ethos_sidebar_widget_headings_color',
            'type'       => 'color',
            'label'      => __( 'Widget Headings', '__x__' ),
            'conditions' => [ $condition_stack_is_ethos ],
          ],
          [
            'key'        => 'x_ethos_sidebar_color',
            'type'       => 'color',
            'label'      => __( 'Widget Text', '__x__' ),
            'conditions' => [ $condition_stack_is_ethos ],
          ],
        ],
      ], [
        'type'        => 'group',
        'label'       => $labels['blog-options'],
        // 'description' => __( 'Enabling the blog header will turn on the area above your posts on the index page that contains your title and subtitle. Disabling it will result in more content being visible above the fold.', '__x__' ),
        // 'description' => __( 'The entry icon color is for the post icons to the left of each title. Selecting "Creative" under the "Entry Icon Position" setting will allow you to align your entry icons in a different manner on your posts index page when "Content Left, Sidebar Right" or "Fullwidth" are selected as your "Content Layout" and when your blog "Style" is set to "Standard." This feature is intended to be paired with a "Boxed" layout.', '__x__' ),
        // 'description' => __( 'Enabling the filterable index will bypass the standard output of your blog page, allowing you to specify categories to highlight. Upon selecting this option, a text input will appear to enter in the IDs of the categories you would like to showcase. This input accepts a list of numeric IDs separated by a comma (e.g. 14, 1, 817).', '__x__' ),
        'controls'    => [

          // Integrity
          // ---------

          [
            'key'        => 'x_integrity_blog_header_enable',
            'type'       => 'toggle',
            'label'      => __( 'Header', '__x__' ),
            'options'    => cs_recall( 'options_choices_off_on_bool_string' ),
            'conditions' => [ $condition_stack_is_integrity, $condition_classic_headers_enabled ],
          ],
          [
            'key'        => 'x_integrity_blog_title',
            'type'       => 'text',
            'label'      => __( 'Title', '__x__' ),
            'conditions' => [ $condition_stack_is_integrity, $condition_classic_headers_enabled, $condition_integrity_blog_header_enable ],
          ],
          [
            'key'        => 'x_integrity_blog_subtitle',
            'type'       => 'text',
            'label'      => __( 'Subtitle', '__x__' ),
            'conditions' => [ $condition_stack_is_integrity, $condition_classic_headers_enabled, $condition_integrity_blog_header_enable ],
          ],


          // Renew
          // -----

          [
            'key'        => 'x_renew_blog_title',
            'type'       => 'text',
            'label'      => __( 'Title', '__x__' ),
            'conditions' => [ $condition_stack_is_renew, $condition_classic_headers_enabled ],
          ],
          [
            'key'        => 'x_renew_entry_icon_position',
            'type'       => 'select',
            'label'      => __( 'Entry Icon', '__x__' ),
            'options'    => [ 'choices' => $choices_renew_entry_icon_position ],
            'conditions' => [ $condition_stack_is_renew ],
          ],
          [
            'key'        => 'x_renew_entry_icon_position_horizontal',
            'type'       => 'unit-slider',
            'label'      => __( 'Horizontal Alignment', '__x__' ),
            'options'    => $options_renew_entry_icon_horizontal_alignment,
            'conditions' => [ $condition_stack_is_renew, $condition_renew_entry_icon_creative ],
          ],
          [
            'key'        => 'x_renew_entry_icon_position_vertical',
            'type'       => 'unit-slider',
            'label'      => __( 'Vertical Alignment', '__x__' ),
            'options'    => $options_renew_entry_icon_vertical_alignment,
            'conditions' => [ $condition_stack_is_renew, $condition_renew_entry_icon_creative ],
          ],


          // Icon
          // ----

          [
            'key'        => 'x_icon_post_title_icon_enable',
            'type'       => 'toggle',
            'label'      => __( 'Post Icons', '__x__' ),
            'conditions' => [ $condition_stack_is_icon ],
          ],
          [
            'key'        => 'x_icon_post_standard_colors_enable',
            'type'       => 'toggle',
            'label'      => __( 'Standard<br/>Colors', '__x__' ),
            'conditions' => [ $condition_stack_is_icon ],
          ],
          [
            'keys' => [
              'value' => 'x_icon_post_standard_color',
              'alt'   => 'x_icon_post_standard_background',
            ],
            'type'       => 'color',
            'label'      => '&nbsp;',
            'options'    => $options_icon_color_labels,
            'conditions' => [ $condition_stack_is_icon, [ 'x_icon_post_standard_colors_enable' => true ] ],
          ],
          [
            'key'        => 'x_icon_post_image_colors_enable',
            'type'       => 'toggle',
            'label'      => __( 'Image<br/>Colors', '__x__' ),
            'conditions' => [ $condition_stack_is_icon ],
          ],
          [
            'keys' => [
              'value' => 'x_icon_post_image_color',
              'alt'   => 'x_icon_post_image_background',
            ],
            'type'       => 'color',
            'label'      => '&nbsp;',
            'options'    => $options_icon_color_labels,
            'conditions' => [ $condition_stack_is_icon, [ 'x_icon_post_image_colors_enable' => true ] ],
          ],
          [
            'key'        => 'x_icon_post_gallery_colors_enable',
            'type'       => 'toggle',
            'label'      => __( 'Gallery<br/>Colors', '__x__' ),
            'conditions' => [ $condition_stack_is_icon ],
          ],
          [
            'keys' => [
              'value' => 'x_icon_post_gallery_color',
              'alt'   => 'x_icon_post_gallery_background',
            ],
            'type'       => 'color',
            'label'      => '&nbsp;',
            'options'    => $options_icon_color_labels,
            'conditions' => [ $condition_stack_is_icon, [ 'x_icon_post_gallery_colors_enable' => true ] ],
          ],
          [
            'key'        => 'x_icon_post_video_colors_enable',
            'type'       => 'toggle',
            'label'      => __( 'Video<br/>Colors', '__x__' ),
            'conditions' => [ $condition_stack_is_icon ],
          ],
          [
            'keys' => [
              'value' => 'x_icon_post_video_color',
              'alt'   => 'x_icon_post_video_background',
            ],
            'type'       => 'color',
            'label'      => '&nbsp;',
            'options'    => $options_icon_color_labels,
            'conditions' => [ $condition_stack_is_icon, [ 'x_icon_post_video_colors_enable' => true ] ],
          ],
          [
            'key'        => 'x_icon_post_audio_colors_enable',
            'type'       => 'toggle',
            'label'      => __( 'Audio<br/>Colors', '__x__' ),
            'conditions' => [ $condition_stack_is_icon ],
          ],
          [
            'keys' => [
              'value' => 'x_icon_post_audio_color',
              'alt'   => 'x_icon_post_audio_background',
            ],
            'type'       => 'color',
            'label'      => '&nbsp;',
            'options'    => $options_icon_color_labels,
            'conditions' => [ $condition_stack_is_icon, [ 'x_icon_post_audio_colors_enable' => true ] ],
          ],
          [
            'key'        => 'x_icon_post_quote_colors_enable',
            'type'       => 'toggle',
            'label'      => __( 'Quote<br/>Colors', '__x__' ),
            'conditions' => [ $condition_stack_is_icon ],
          ],
          [
            'keys' => [
              'value' => 'x_icon_post_quote_color',
              'alt'   => 'x_icon_post_quote_background',
            ],
            'type'       => 'color',
            'label'      => '&nbsp;',
            'options'    => $options_icon_color_labels,
            'conditions' => [ $condition_stack_is_icon, [ 'x_icon_post_quote_colors_enable' => true ] ],
          ],
          [
            'key'        => 'x_icon_post_link_colors_enable',
            'type'       => 'toggle',
            'label'      => __( 'Link<br/>Colors', '__x__' ),
            'conditions' => [ $condition_stack_is_icon ],
          ],
          [
            'keys' => [
              'value' => 'x_icon_post_link_color',
              'alt'   => 'x_icon_post_link_background',
            ],
            'type'       => 'color',
            'label'      => '&nbsp;',
            'options'    => $options_icon_color_labels,
            'conditions' => [ $condition_stack_is_icon, [ 'x_icon_post_link_colors_enable' => true ] ],
          ],


          // Ethos
          // -----

          [
            'key'        => 'x_ethos_filterable_index_enable',
            'type'       => 'toggle',
            'label'      => __( 'Filterable Index', '__x__' ),
            'conditions' => [ $condition_stack_is_ethos ],
          ],
          [
            'key'        => 'x_ethos_filterable_index_categories',
            'type'       => 'text',
            'label'      => __( 'Category IDs', '__x__' ),
            'conditions' => [ $condition_stack_is_ethos, $condition_ethos_filterable_index_enable ],
          ],
        ],
      ], [
        'type'        => 'group',
        'label'       => $labels['portfolio-options'],
        'conditions'  => [ $condition_stack_is_integrity, $condition_classic_headers_enabled ],
        // 'description' => __( 'Enabling portfolio index sharing will turn on social sharing links for each post on the portfolio index page. Activate and deactivate individual sharing links underneath the main Portfolio section.', '__x__' ),
        'controls'    => [
          [
            'key'   => 'x_integrity_portfolio_archive_sort_button_text',
            'type'  => 'text',
            'label' => __( 'Sort Button Text', '__x__' ),
          ],
          [
            'key'   => 'x_integrity_portfolio_archive_post_sharing_enable',
            'type'  => 'toggle',
            'label' => __( 'Index Sharing', '__x__' ),
          ],
        ],
      ], class_exists( 'WooCommerce' ) ? [
        'type'        => 'group',
        'label'       => $labels['shop-options'],
        // 'description' => __( 'Provide a title you would like to use for your shop. This will show up on the index page as well as in your breadcrumbs.', '__x__' ),
        'controls'    => [

          // Integrity
          // ---------

          [
            'key'        => 'x_integrity_shop_header_enable',
            'type'       => 'toggle',
            'label'      => __( 'Header', '__x__' ),
            'options'    => cs_recall( 'options_choices_off_on_bool_string' ),
            'conditions' => [ $condition_stack_is_integrity, $condition_classic_headers_enabled ],
          ],
          [
            'key'        => 'x_integrity_shop_title',
            'type'       => 'text',
            'label'      => __( 'Title', '__x__' ),
            'conditions' => [ $condition_stack_is_integrity, $condition_classic_headers_enabled, $condition_integrity_shop_header_enable ]
          ],
          [
            'key'        => 'x_integrity_shop_subtitle',
            'type'       => 'text',
            'label'      => __( 'Subtitle', '__x__' ),
            'conditions' => [ $condition_stack_is_integrity, $condition_classic_headers_enabled, $condition_integrity_shop_header_enable ]
          ],


          // Renew
          // -----

          [
            'key'        => 'x_renew_shop_title',
            'type'       => 'text',
            'label'      => __( 'Title', '__x__' ),
            'conditions' => [ $condition_stack_is_renew, $condition_classic_headers_enabled ],
          ],


          // Icon
          // ----

          [
            'key'        => 'x_icon_shop_title',
            'type'       => 'text',
            'label'      => __( 'Title', '__x__' ),
            'conditions' => [ $condition_stack_is_icon, $condition_classic_headers_enabled ],
          ],


          // Ethos
          // -----

          [
            'key'        => 'x_ethos_shop_title',
            'type'       => 'text',
            'label'      => __( 'Title', '__x__' ),
            'conditions' => [ $condition_stack_is_ethos, $condition_classic_headers_enabled ],
          ],
        ],
      ] : null, [
        'key'         => 'x_ethos_post_carousel_enable',
        'type'        => 'group',
        'label'       => $labels['post-carousel'],
        'options'     => cs_recall( 'options_group_toggle_off_on_bool_string' ),
        'conditions'  => [ $condition_stack_is_ethos, $condition_classic_headers_enabled ],
        // 'description' => __( 'The "Post Carousel" is an element located above the masthead, which allows you to showcase your posts in various formats. If "Featured" is selected, you can choose which posts you would like to appear in this location in the post meta options.', '__x__' ),
        'controls'    => [
          [
            'key'        => 'x_ethos_post_carousel_display',
            'type'       => 'select',
            'label'      => __( 'Display', '__x__' ),
            'options'    => [ 'choices' => $choices_ethos_post_carousel_and_slider_display ],
            'conditions' => [ $condition_ethos_post_carousel_enable ],
          ],
          [
            'key'        => 'x_ethos_post_carousel_count',
            'type'       => 'unit-slider',
            'label'      => __( 'Posts Per Page', '__x__' ),
            'options'    => $options_ethos_posts_per_page,
            'conditions' => [ $condition_ethos_post_carousel_enable ],
          ],
          [
            'key'        => 'x_ethos_post_carousel_display_count_extra_large',
            'type'       => 'unit-slider',
            'label'      => __( 'Extra Large<br/>Count', '__x__' ),
            'options'    => $options_ethos_posts_display,
            'conditions' => [ $condition_ethos_post_carousel_enable ],
          ],
          [
            'key'        => 'x_ethos_post_carousel_display_count_large',
            'type'       => 'unit-slider',
            'label'      => __( 'Large<br/>Count', '__x__' ),
            'options'    => $options_ethos_posts_display,
            'conditions' => [ $condition_ethos_post_carousel_enable ],
          ],
          [
            'key'        => 'x_ethos_post_carousel_display_count_medium',
            'type'       => 'unit-slider',
            'label'      => __( 'Medium<br/>Count', '__x__' ),
            'options'    => $options_ethos_posts_display,
            'conditions' => [ $condition_ethos_post_carousel_enable ],
          ],
          [
            'key'        => 'x_ethos_post_carousel_display_count_small',
            'type'       => 'unit-slider',
            'label'      => __( 'Small<br/>Count', '__x__' ),
            'options'    => $options_ethos_posts_display,
            'conditions' => [ $condition_ethos_post_carousel_enable ],
          ],
          [
            'key'        => 'x_ethos_post_carousel_display_count_extra_small',
            'type'       => 'unit-slider',
            'label'      => __( 'Extra Small<br/>Count', '__x__' ),
            'options'    => $options_ethos_posts_display,
            'conditions' => [ $condition_ethos_post_carousel_enable ],
          ],
        ],
      ], [
        'key'         => 'x_ethos_post_slider_blog_enable',
        'type'        => 'group',
        'label'       => $labels['post-slider-blog'],
        'options'     => cs_recall( 'options_group_toggle_off_on_bool_string' ),
        'conditions'  => [ $condition_stack_is_ethos ],
        // 'description' => __( 'The blog "Post Slider" is located at the top of the posts index page, which allows you to showcase your posts in various formats. If "Featured" is selected, you can choose which posts you would like to appear in this location in the post meta options. The archive "Post Slider" is located at the top of all archive pages, which allows you to showcase your posts in various formats. If "Featured" is selected, you can choose which posts you would like to appear in this location in the post meta options.', '__x__' ),
        'controls'    => [
          [
            'key'        => 'x_ethos_post_slider_blog_display',
            'type'       => 'select',
            'label'      => __( 'Display', '__x__' ),
            'options'    => [ 'choices' => $choices_ethos_post_carousel_and_slider_display ],
            'conditions' => [ $condition_ethos_post_slider_blog_enable ],
          ],
          [
            'key'        => 'x_ethos_post_slider_blog_count',
            'type'       => 'unit-slider',
            'label'      => __( 'Posts Per Page', '__x__' ),
            'options'    => $options_ethos_posts_per_page,
            'conditions' => [ $condition_ethos_post_slider_blog_enable ],
          ],
          [
            'key'        => 'x_ethos_post_slider_blog_height',
            'type'       => 'unit-slider',
            'label'      => __( 'Height', '__x__' ),
            'options'    => $options_ethos_post_slider_height,
            'conditions' => [ $condition_ethos_post_slider_blog_enable ],
          ],
        ],
      ], [
        'key'         => 'x_ethos_post_slider_archive_enable',
        'type'        => 'group',
        'label'       => $labels['post-slider-archive'],
        'options'     => cs_recall( 'options_group_toggle_off_on_bool_string' ),
        'conditions'  => [ $condition_stack_is_ethos ],
        // 'description' => __( 'The blog "Post Slider" is located at the top of the posts index page, which allows you to showcase your posts in various formats. If "Featured" is selected, you can choose which posts you would like to appear in this location in the post meta options. The archive "Post Slider" is located at the top of all archive pages, which allows you to showcase your posts in various formats. If "Featured" is selected, you can choose which posts you would like to appear in this location in the post meta options.', '__x__' ),
        'controls'    => [
          [
            'key'        => 'x_ethos_post_slider_archive_display',
            'type'       => 'select',
            'label'      => __( 'Display', '__x__' ),
            'options'    => [ 'choices' => $choices_ethos_post_carousel_and_slider_display ],
            'conditions' => [ $condition_ethos_post_slider_archive_enable ],
          ],
          [
            'key'        => 'x_ethos_post_slider_archive_count',
            'type'       => 'unit-slider',
            'label'      => __( 'Posts Per Page', '__x__' ),
            'options'    => $options_ethos_posts_per_page,
            'conditions' => [ $condition_ethos_post_slider_archive_enable ],
          ],
          [
            'key'        => 'x_ethos_post_slider_archive_height',
            'type'       => 'unit-slider',
            'label'      => __( 'Height', '__x__' ),
            'options'    => $options_ethos_post_slider_height,
            'conditions' => [ $condition_ethos_post_slider_archive_enable ],
          ],
        ],
      ]
    ]
  ];

  // Layout and Design
  // -----------------

  $breakpoint_keys = [
    'base' => 'x_breakpoint_base',
  ];

  if (
    apply_filters( 'x_legacy_allow_breakpoint_config', false )
    || apply_filters( 'cs_allow_breakpoint_ranges_change', true )
  ) {
    $breakpoint_keys['ranges'] = 'x_breakpoint_ranges';
  }

  $sub_modules[] = [
    'type'  => 'group-sub-module',
    'label' => __( 'Layout and Design', '__x__' ),
    'options' => [ 'tag' => 'layout-and-design', 'name' => 'x-theme-options:layout-and-design' ],
    'controls' => [
      // Layout
      apply_filters("cs_theme_options_layout_group", null),

      // Breakpoint
      [
        'type'  => 'breakpoint-manager',
        'label' => 'Breakpoints',
        'group'       => 'x:layout-and-design',
        'keys'  => $breakpoint_keys,
        'options' => [
          'notify' => [
            'message' => 'Please save and fully refresh Cornerstone for the new breakpoint configuration to take effect.',
            'timeout' => 10000
          ]
        ]
      ]
    ]
  ];


  // Typography
  // ----------

  $sub_modules[] = apply_filters("cs_theme_options_typography_group", null);


  // Buttons
  // -------

  $sub_modules[] = [
    'type'  => 'group-sub-module',
    'label' => __( 'Buttons', '__x__' ),
    'options' => [ 'tag' => 'buttons', 'name' => 'x-theme-options:buttons' ],
    'controls' => [
      [
        'type'        => 'group',
        // 'label'       => $labels['setup'],
        // 'description' => __( 'Retina ready, limitless colors, and multiple shapes. The buttons available in X are fun to use, simple to implement, and look great on all devices no matter the size.', '__x__' ),
        'description' => __( 'The button styles specified here will be utilized for native WordPress elements such as comment forms, add to cart actions, et cetera.', '__x__' ),
        'controls'    => [
          [
            'key'     => 'x_button_style',
            'type'    => 'select',
            'label'   => __( 'Style', '__x__' ),
            'options' => [ 'choices' => $choices_button_style ],
          ],
          [
            'key'     => 'x_button_shape',
            'type'    => 'select',
            'label'   => __( 'Shape', '__x__' ),
            'options' => [ 'choices' => $choices_button_shape ],
          ],
          [
            'key'     => 'x_button_size',
            'type'    => 'select',
            'label'   => __( 'Size', '__x__' ),
            'options' => [ 'choices' => $choices_button_size ],
          ],
          [
            'keys' => [
              'value' => 'x_button_color',
              'alt'   => 'x_button_color_hover',
            ],
            'type'    => 'color',
            'label'   => __( 'Color', '__x__' ),
            'options' => cs_recall( 'options_swatch_base_interaction_labels' ),
          ],
          [
            'keys' => [
              'value' => 'x_button_background_color',
              'alt'   => 'x_button_background_color_hover',
            ],
            'type'       => 'color',
            'label'      => __( 'Background', '__x__' ),
            'options'    => cs_recall( 'options_swatch_base_interaction_labels' ),
            'conditions' => [ $condition_button_background_color ],
          ],
          [
            'keys' => [
              'value' => 'x_button_border_color',
              'alt'   => 'x_button_border_color_hover',
            ],
            'type'    => 'color',
            'label'   => __( 'Border', '__x__' ),
            'options' => cs_recall( 'options_swatch_base_interaction_labels' ),
          ],
          [
            'keys' => [
              'value' => 'x_button_bottom_color',
              'alt'   => 'x_button_bottom_color_hover',
            ],
            'type'       => 'color',
            'label'      => __( 'Bottom', '__x__' ),
            'options'    => cs_recall( 'options_swatch_base_interaction_labels' ),
            'conditions' => [ $condition_button_bottom_color ],
          ],
        ],
      ]
    ]
  ];


  // Headers
  // -------

  $sub_modules[] = [
    'type'  => 'group-sub-module',
    'label' => __( 'Headers', '__x__' ),
    'options' => [ 'tag' => 'headers', 'name' => 'x-theme-options:headers' ],
    'controls' => [
      [
        'type'        => 'group',
        'label'       => $labels['navbar'],
        'description' => __( '"Top Height" must still be set even when using "Fixed Left" or "Fixed Right" positioning because on tablet and mobile devices, the menu is pushed to the top.', '__x__' ),
        'conditions'  => [ $condition_classic_headers_enabled ],
        'controls'    => [
          [
            'key'     => 'x_navbar_positioning',
            'type'    => 'select',
            'label'   => __( 'Position', '__x__' ),
            'options' => [ 'choices' => $choices_navbar_position ],
          ],
          [
            'key'        => 'x_fixed_menu_scroll',
            'type'       => 'select',
            'label'      => __( 'Scrolling', '__x__' ),
            'options'    => [ 'choices' => $choices_navbar_scrolling ],
            'conditions' => [ $condition_navbar_fixed_left_or_right ],
          ],
          [
            'key'     => 'x_navbar_height',
            'type'    => 'unit-slider',
            'label'   => __( 'Top Height', '__x__' ),
            'options' => $options_navbar_top_height,
          ],
          [
            'key'        => 'x_navbar_width',
            'type'       => 'unit-slider',
            'label'      => __( 'Side Width', '__x__' ),
            'options'    => $options_navbar_side_width,
            'conditions' => [ $condition_navbar_fixed_left_or_right ],
          ],

          // Sub indicator
          [
            'key' => 'x_navbar_subindicator_icon',
            'type' => 'icon',
            'label' => __( 'Subindicator', '__x__' ),
            'description' => __( 'The icon to use when displaying menus that have mutliple levels', '__x__' ),
          ],
        ],
      ], [
        'type'        => 'group',
        'label'       => $labels['logo-and-navigation'],
        'description' => __( 'Selecting "Inline" for your logo and navigation layout will place them both in the navbar. Selecting "Stacked" will place the logo in a separate section above the navbar.', '__x__' ),
        'conditions'  => [ $condition_classic_headers_enabled ],
        'controls'    => [
          [
            'key'     => 'x_logo_navigation_layout',
            'type'    => 'choose',
            'label'   => __( 'Layout', '__x__' ),
            'options' => [ 'choices' => $choices_inline_or_stacked ],
          ],
          [
            'key'        => 'x_logobar_adjust_spacing_top',
            'type'       => 'unit-slider',
            'label'      => __( 'Logobar Top', '__x__' ),
            'options'    => $options_logobar_adjust_spacing,
            'conditions' => [ $condition_logo_navigation_stacked ],
          ],
          [
            'key'        => 'x_logobar_adjust_spacing_bottom',
            'type'       => 'unit-slider',
            'label'      => __( 'Logobar Bottom', '__x__' ),
            'options'    => $options_logobar_adjust_spacing,
            'conditions' => [ $condition_logo_navigation_stacked ],
          ],
        ],
      ], [
        'type'        => 'group',
        'label'       => $labels['logo'],
        'description' => __( 'Your logo will show up as the site title by default, but can be overwritten below (it is also used as the alt text should you choose to use an image). Alternately, if you would like to use an image, upload it below. Logo alignment can also be adjusted in this section.', '__x__' ),
        'conditions'  => [ $condition_classic_headers_enabled ],
        'controls'    => [
          [
            'key'   => 'x_logo_text',
            'type'  => 'text',
            'label' => __( 'Text', '__x__' ),
          ],
          [
            'key'     => 'x_logo_visually_hidden_h1',
            'type'    => 'toggle',
            'label'   => __( 'Hidden &lt;h1&gt;', '__x__' ),
            'options' => cs_recall( 'options_group_toggle_off_on_bool_string' ),
          ],
          [
            'key'     => 'x_logo_font_size',
            'type'    => 'unit-slider',
            'label'   => __( 'Font Size', '__x__' ),
            'options' => $options_logo_font_size,
          ],
          [
            'key'        => 'x_logo_font_family_selection',
            'type'       => 'font-family',
            'label'      => __( 'Font Family', '__x__' ),
            'conditions' => [ $condition_font_manager_enabled ],
          ],
          [
            'key'        => 'x_logo_font_family',
            'type'       => 'select',
            'label'      => __( 'Font Family', '__x__' ),
            'options'    => [ 'choices' => $choices_list_font_families ],
            'conditions' => [ $condition_font_manager_disabled ],
          ],
          [
            'keys' => [
              'value'       => 'x_logo_font_weight_selection',
              'font_family' => 'x_logo_font_family_selection'
            ],
            'type'       => 'font-weight',
            'label'      => __( 'Font Weight', '__x__' ),
            'conditions' => [ $condition_font_manager_enabled ],
          ],
          [
            'key'        => 'x_logo_font_weight',
            'type'       => 'select',
            'label'      => __( 'Font Weight', '__x__' ),
            'conditions' => [ $condition_font_manager_disabled ],
            'options'    => [
              'filter'  => [ 'key' => 'choices', 'method' => 'font-weights', 'source' => 'x_logo_font_family' ],
              'choices' => $choices_list_font_weights,
            ],
          ],
          [
            'key'     => 'x_logo_letter_spacing',
            'type'    => 'unit-slider',
            'label'   => __( 'Letter Spacing', '__x__' ),
            'options' => $options_letter_spacing_compressed,
          ],
          [
            'key'        => 'x_logo_font_italic',
            'type'       => 'toggle',
            'label'      => __( 'Italic', '__x__' ),
            'options'    => cs_recall( 'options_group_toggle_off_on_bool_string' ),
            'conditions' => [ $condition_font_manager_enabled ],
          ],
          [
            'key'     => 'x_logo_uppercase_enable',
            'type'    => 'toggle',
            'label'   => __( 'Uppercase', '__x__' ),
            'options' => cs_recall( 'options_group_toggle_off_on_bool_string' ),
          ],
          [
            'key'   => 'x_logo_font_color',
            'type'  => 'color',
            'label' => __( 'Color', '__x__' ),
          ],
        ],
      ], [
        'type'        => 'group',
        'label'       => $labels['logo-image'],
        'description' => __( 'To make your logo retina ready, enter in the width of your uploaded image in the "Logo Width (px)" field and we\'ll take care of all the calculations for you. If you want your logo to stay the original size that was uploaded, leave the field blank.', '__x__' ),
        'conditions'  => [ $condition_classic_headers_enabled ],
        'controls'    => [
          [
            'key'   => 'x_logo',
            'type'  => 'image',
            'label' => __( 'Image', '__x__' ),
          ],
          [
            'key'   => 'x_logo_width',
            'type'  => 'text',
            'label' => __( 'Image Width (px)', '__x__' ),
          ],
        ],
      ],[
        'type'        => 'group',
        'label'       => $labels['logo-alignment'],
        'description' => __( 'Use the following controls to vertically align your logo as desired. Make sure to adjust your top alignment even if your navbar is fixed to a side as it will reformat to the top on smaller screens (this control will be hidden if you do not have a side navigation position selected).', '__x__' ),
        'conditions'  => [ $condition_classic_headers_enabled ],
        'controls'    => [
          [
            'key'     => 'x_logo_adjust_navbar_top',
            'type'    => 'unit-slider',
            'label'   => __( 'Top Alignment', '__x__' ),
            'options' => $options_navbar_logo_and_links_adjust_top,
          ],
          [
            'key'     => 'x_logo_adjust_navbar_side',
            'type'    => 'unit-slider',
            'label'   => __( 'Side Alignment', '__x__' ),
            'options' => $options_navbar_logo_and_links_adjust_side,
          ],
        ],
      ],[
        'type'        => 'group',
        'label'       => $labels['links'],
        'description' => __( 'Alter the appearance of the top-level navbar links for your site here and their alignment and spacing in the section below.', '__x__' ),
        'conditions'  => [ $condition_classic_headers_enabled ],
        'controls'    => [
          [
            'key'     => 'x_navbar_font_size',
            'type'    => 'unit-slider',
            'label'   => __( 'Font Size', '__x__' ),
            'options' => $options_navbar_font_size,
          ],
          [
            'key'        => 'x_navbar_font_family_selection',
            'type'       => 'font-family',
            'label'      => __( 'Font Family', '__x__' ),
            'conditions' => [ $condition_font_manager_enabled ],
          ],
          [
            'key'        => 'x_navbar_font_family',
            'type'       => 'select',
            'label'      => __( 'Font Family', '__x__' ),
            'options'    => [ 'choices' => $choices_list_font_families ],
            'conditions' => [ $condition_font_manager_disabled ],
          ],
          [
            'keys' => [
              'value'       => 'x_navbar_font_weight_selection',
              'font_family' => 'x_navbar_font_family_selection'
            ],
            'type'       => 'font-weight',
            'label'      => __( 'Font Weight', '__x__' ),
            'conditions' => [ $condition_font_manager_enabled ]
          ],
          [
            'key'        => 'x_navbar_font_weight',
            'type'       => 'select',
            'label'      => __( 'Font Weight', '__x__' ),
            'conditions' => [ $condition_font_manager_disabled ],
            'options'    => [
              'filter'  => [ 'key' => 'choices', 'method' => 'font-weights', 'source' => 'x_navbar_font_family' ],
              'choices' => $choices_list_font_weights,
            ],
          ],
          [
            'key'     => 'x_navbar_letter_spacing',
            'type'    => 'unit-slider',
            'label'   => __( 'Letter Spacing', '__x__' ),
            'options' => $options_letter_spacing_compressed,
          ],
          [
            'key'        => 'x_navbar_font_italic',
            'type'       => 'toggle',
            'label'      => __( 'Italic', '__x__' ),
            'options'    => cs_recall( 'options_group_toggle_off_on_bool_string' ),
            'conditions' => [ $condition_font_manager_enabled ],
          ],
          [
            'key'     => 'x_navbar_uppercase_enable',
            'type'    => 'toggle',
            'label'   => __( 'Uppercase', '__x__' ),
            'options' => cs_recall( 'options_group_toggle_off_on_bool_string' ),
          ],
          [
            'keys' => [
              'value' => 'x_navbar_link_color',
              'alt'   => 'x_navbar_link_color_hover',
            ],
            'type'    => 'color',
            'label'   => __( 'Color', '__x__' ),
            'options' => cs_recall( 'options_swatch_base_interaction_labels' ),
          ],
        ],
      ],[
        'type'        => 'group',
        'label'       => $labels['links-alignment'],
        'description' => __( 'Customize the vertical alignment of your links for both top and side navbar positions as well as alter the vertical spacing between links for top navbar positions with the "Link Spacing" control.', '__x__' ),
        'conditions'  => [ $condition_classic_headers_enabled ],
        'controls'    => [
          [
            'key'     => 'x_navbar_adjust_links_top',
            'type'    => 'unit-slider',
            'label'   => __( 'Top Alignment', '__x__' ),
            'options' => $options_navbar_logo_and_links_adjust_top,
          ],
          [
            'key'     => 'x_navbar_adjust_links_side',
            'type'    => 'unit-slider',
            'label'   => __( 'Side Alignment', '__x__' ),
            'options' => $options_navbar_logo_and_links_adjust_side,
          ],
          [
            'key'     => 'x_navbar_adjust_links_top_spacing',
            'type'    => 'unit-slider',
            'label'   => __( 'Top Spacing', '__x__' ),
            'options' => $options_navbar_links_top_spacing,
          ],
        ],
      ],[
        'type'        => 'group',
        'label'       => $labels['search'],
        'conditions'  => [ $condition_classic_headers_enabled ],
        'description' => __( 'Activate search functionality for the navbar. If activated, an icon will appear that when clicked will activate the search modal.', '__x__' ),
        'controls' => [
          [
            'key'  => 'x_header_search_enable',
            'type' => 'toggle',
            'label' => $labels['enable']
          ]
        ]
      ],[
        'type'        => 'group',
        'label'       => $labels['mobile-button'],
        'description' => __( 'Adjust the vertical alignment and size of the mobile button that appears on smaller screen sizes in your navbar.', '__x__' ),
        'conditions'  => [ $condition_classic_headers_enabled ],
        'controls'    => [
          [
            'key'     => 'x_navbar_adjust_button_size',
            'type'    => 'unit-slider',
            'label'   => __( 'Size', '__x__' ),
            'options' => $options_navbar_button_size,
          ],
          [
            'key'     => 'x_navbar_adjust_button',
            'type'    => 'unit-slider',
            'label'   => __( 'Alignment', '__x__' ),
            'options' => $options_navbar_button_spacing,
          ],
        ],
      ],[
        'type'        => 'group',
        'label'       => $labels['widgetbar'],
        'description' => __( 'Specify how many widget areas should appear in the collapsible Widgetbar and select the colors for its associated toggle.', '__x__' ),
        'conditions'  => [ $condition_classic_headers_enabled ],
        'controls'    => [
          [
            'key'     => 'x_header_widget_areas',
            'type'    => 'choose',
            'label'   => __( 'Widget Areas', '__x__' ),
            'options' => [ 'choices' => $choices_widget_areas ],
          ],
          [
            'keys' => [
              'value' => 'x_widgetbar_button_background',
              'alt'   => 'x_widgetbar_button_background_hover',
            ],
            'type'       => 'color',
            'label'      => __( 'Toggle Background', '__x__' ),
            'options'    => cs_recall( 'options_swatch_base_interaction_labels' ),
            'conditions' => [ $condition_header_has_widget_areas ],
          ],
        ],
      ],[
        'type'        => 'group',
        'label'       => $labels['miscellaneous'],
        'description' => __( 'Specify how many widget areas should appear in the collapsible Widgetbar and select the colors for its associated toggle.', '__x__' ),
        'conditions'  => [ $condition_classic_headers_enabled ],
        'controls'    => [
          [
            'key'     => 'x_topbar_display',
            'type'    => 'toggle',
            'label'   => __( 'Topbar', '__x__' ),
            'options' => cs_recall( 'options_group_toggle_off_on_bool_string' ),
          ],
          [
            'key'        => 'x_topbar_content',
            'type'       => 'textarea',
            'label'      => __( 'Topbar Content', '__x__' ),
            'options'    => $options_topbar_content,
            'conditions' => [ $condition_topbar_enabled ],
          ],
          [
            'key'     => 'x_breadcrumb_display',
            'type'    => 'toggle',
            'label'   => __( 'Crumbs', '__x__' ),
            'options' => cs_recall( 'options_group_toggle_off_on_bool_string' ),
          ],
        ],
      ]
    ]
  ];


  // Footers
  // -------

  $sub_modules[] = [
    'type'  => 'group-sub-module',
    'label' => __( 'Footers', '__x__' ),
    'options' => [ 'tag' => 'footers', 'name' => 'x-theme-options:footers' ],
    'controls' => [
      [
        'type'        => 'group',
        // 'label'       => $labels['setup'],
        'conditions'  => [ $condition_classic_footers_enabled ],
        'controls'    => [
          [
            'key'        => 'x_footer_widget_areas',
            'type'       => 'choose',
            'label'      => __( 'Widget Areas', '__x__' ),
            'options'    => [ 'choices' => $choices_widget_areas ],
            'conditions' => [ $condition_classic_footers_enabled ],
          ],
          [
            'key'        => 'x_footer_bottom_display',
            'type'       => 'toggle',
            'label'      => __( 'Bottom Footer', '__x__' ),
            'options'    => cs_recall( 'options_group_toggle_off_on_bool_string' ),
            'conditions' => [ $condition_classic_footers_enabled ],
          ],
          [
            'key'        => 'x_footer_menu_display',
            'type'       => 'toggle',
            'label'      => __( 'Menu', '__x__' ),
            'options'    => cs_recall( 'options_group_toggle_off_on_bool_string' ),
            'conditions' => [ $condition_footer_bottom_display_enabled, $condition_classic_footers_enabled ],
          ],
          [
            'key'        => 'x_footer_social_display',
            'type'       => 'toggle',
            'label'      => __( 'Social', '__x__' ),
            'options'    => cs_recall( 'options_group_toggle_off_on_bool_string' ),
            'conditions' => [ $condition_classic_footers_enabled, $condition_footer_bottom_display_enabled ],
          ],
          [
            'key'        => 'x_footer_content_display',
            'type'       => 'toggle',
            'label'      => __( 'Content Area', '__x__' ),
            'options'    => cs_recall( 'options_group_toggle_off_on_bool_string' ),
            'conditions' => [ $condition_classic_footers_enabled, $condition_footer_bottom_display_enabled ],
          ],
          [
            'key'        => 'x_footer_content',
            'type'       => 'textarea',
            'label'      => '&nbsp;',
            'conditions' => [ $condition_classic_footers_enabled, $condition_footer_bottom_display_enabled, $condition_footer_content_enabled ],
          ],
        ],
      ]
    ]
  ];


  // Blog
  // ---

  $sub_modules[] = [
    'type'  => 'group-sub-module',
    'label' => __( 'Blog', '__x__' ),
    'options' => [ 'tag' => 'blog', 'name' => 'x-theme-options:blog' ],
    'controls' => [
      [
        'type'        => 'group',
        // 'label'       => $labels['setup'],
        // 'description' => __( 'Adjust the style and layout of your blog using the settings below. This will only affect the posts index page of your blog and will not alter any archive or search results pages. The "Layout" option allows you to keep your sidebar on your posts index page if you have already selected "Content Left, Sidebar Right" or "Sidebar Left, Content Right" for you "Content Layout" option, or remove the sidebar completely if desired.', '__x__' ),
        'description' => __( 'Adjust the style and layout of your blog using the settings below. This will only affect the posts index page of your blog and will not alter any archives.', '__x__' ),
        'controls'    => [
          [
            'key'     => 'x_blog_style',
            'type'    => 'choose',
            'label'   => __( 'Style', '__x__' ),
            'options' => [ 'choices' => $choices_blog_styles ],
          ],
          [
            'key'     => 'x_blog_layout',
            'type'    => 'choose',
            'label'   => __( 'Layout', '__x__' ),
            'options' => [ 'choices' => $choices_section_layouts ],
          ],
          [
            'key'        => 'x_blog_masonry_columns',
            'type'       => 'choose',
            'label'      => __( 'Columns', '__x__' ),
            'options'    => [ 'choices' => $choices_masonry_columns ],
            'conditions' => [ $condition_blog_style_masonry ],
          ],
        ],
      ], [
        'type'        => 'group',
        'label'       => $labels['archives'],
        // 'description' => __( 'Adjust the style and layout of your archive pages using the settings below. The "Layout" option allows you to keep your sidebar on your posts index page if you have already selected "Content Left, Sidebar Right" or "Sidebar Left, Content Right" for you "Content Layout" option, or remove the sidebar completely if desired.', '__x__' ),
        'description' => __( 'Adjust the style and layout of your archive pages using the settings below.', '__x__' ),
        'controls'    => [
          [
            'key'     => 'x_archive_style',
            'type'    => 'choose',
            'label'   => __( 'Style', '__x__' ),
            'options' => [ 'choices' => $choices_blog_styles ],
          ],
          [
            'key'     => 'x_archive_layout',
            'type'    => 'choose',
            'label'   => __( 'Layout', '__x__' ),
            'options' => [ 'choices' => $choices_section_layouts ],
          ],
          [
            'key'        => 'x_archive_masonry_columns',
            'type'       => 'choose',
            'label'      => __( 'Columns', '__x__' ),
            'options'    => [ 'choices' => $choices_masonry_columns ],
            'conditions' => [ $condition_archive_style_masonry ],
          ],
        ],
      ], [
        'type'        => 'group',
        'label'       => $labels['content'],
        'description' => __( 'Selecting the "Full Post on Index" option below will allow the entire contents of your posts to be shown on the post index pages for all stacks. Deselecting this option will allow you to set the length of your excerpt.', '__x__' ),
        'controls'    => [
          [
            'key'     => 'x_blog_enable_post_meta',
            'type'    => 'toggle',
            'label'   => __( 'Post Meta', '__x__' ),
            'options' => cs_recall( 'options_group_toggle_off_on_bool_string' ),
          ],
          [
            'key'     => 'x_blog_enable_full_post_content',
            'type'    => 'toggle',
            'label'   => __( 'Full Post on Index', '__x__' ),
            'options' => cs_recall( 'options_group_toggle_off_on_bool_string' ),
          ],
          [
            'key'        => 'x_blog_excerpt_length',
            'type'       => 'unit-slider',
            'label'      => __( 'Excerpt Length', '__x__' ),
            'options'    => $options_blog_excerpt_length,
            'conditions' => [ $condition_blog_full_post_content_enable ],
          ],
        ],
      ]
    ]
  ];



  // Portfolio
  // --------

  $sub_modules[] = apply_filters("cs_theme_options_portfolio_group", null);


  // Social
  // -----

  $sub_modules[] = apply_filters("cs_theme_options_social_group", null);


  // Miscellaneous
  // ------------

  $sub_modules[] = [
    'type'  => 'group-sub-module',
    'label' => __( 'Miscellaneous', '__x__' ),
    'options' => [ 'tag' => 'miscellaneous', 'name' => 'x-theme-options:miscellaneous' ],
    'controls' => [
      // Scroll Top
      apply_filters("cs_theme_options_scroll_top_group", null),

      // Font awesome
      apply_filters("cs_theme_options_fontawesome_group", null),
    ]
  ];



  // bbPress
  // -------

  if ( class_exists( 'bbPress' ) ) {

    $sub_modules[] = [
      'type'  => 'group-sub-module',
      'label' => __( 'bbPress', '__x__' ),
      'options' => [ 'tag' => 'bbpress', 'name' => 'x-theme-options:bbpress' ],
      'controls' => [
        [
          'type'        => 'group',
          // 'label'       => $labels['setup'],
          'description' => __( 'This section handles all options regarding your bbPress setup. Select your content layout, section titles, along with plenty of other options to get bbPress up and running. The "Layout" option allows you to keep your sidebar if you have already selected "Content Left, Sidebar Right" or "Sidebar Left, Content Right" for your "Content Layout" option, or remove the sidebar completely if desired.', '__x__' ),
          'label'      => __( 'Templates', '__x__' ),
          'key' => 'x_bbpress_enable_templates',
          'options' => cs_recall( 'options_group_toggle_off_on_bool_string' ),
          'controls'    => [
            [
              'key'     => 'x_bbpress_layout_content',
              'type'    => 'choose',
              'label'   => __( 'Layout', '__x__' ),
              'options' => [ 'choices' => $choices_section_layouts ],
            ],
            [
              'key'     => 'x_bbpress_enable_quicktags',
              'type'    => 'toggle',
              'label'   => __( 'Topic/Reply Quicktags', '__x__' ),
              'options' => cs_recall( 'options_group_toggle_off_on_bool_string' ),
            ],
            [
              'key'        => 'x_bbpress_header_menu_enable',
              'type'       => 'toggle',
              'label'      => __( 'Navbar Menu', '__x__' ),
              'options'    => cs_recall( 'options_group_toggle_off_on_bool_string' ),
              'conditions' => [ $condition_classic_headers_enabled ],
            ],
          ],
        ]
      ]
    ];

  }


  // BuddyPress
  // ----------

  if ( class_exists( 'BuddyPress' ) ) {

    $buddy_press_enabled = [
      [ 'x_buddypress_enable' => true ],
    ];

    $sub_modules[] = [
      'type'  => 'group-sub-module',
      'label' => __( 'BuddyPress', '__x__' ),
      'options' => [ 'tag' => 'buddypress', 'name' => 'x-theme-options:buddypress' ],
      'controls' => [
        [
          'type'        => 'group',
          // 'label'       => $labels['setup'],
          'description' => __( 'This section handles all options regarding your BuddyPress setup. Select your content layout, section titles, along with plenty of other options to get BuddyPress up and running. The "Layout" option allows you to keep your sidebar if you have already selected "Content Left, Sidebar Right" or "Sidebar Left, Content Right" for your "Content Layout" option, or remove the sidebar completely if desired.', '__x__' ),
          // 'description' => __( 'You can add links to various "components" manually in your navigation or activate registration and login links in the WordPress admin bar via BuddyPress\' settings if desired. Selecting this setting provides you with an additional theme-specific option that will include a simple navigation item with quick links to various BuddyPress components.', '__x__' ),
          'controls'    => [
            [
              'key'        => 'x_buddypress_enable',
              'type'       => 'toggle',
              'label'      => __( 'Enabled', '__x__' ),
              'options'    => cs_recall( 'options_group_toggle_off_on_bool_string' ),
            ],
            [
              'key'        => 'x_buddypress_templates_enable',
              'type'       => 'toggle',
              'label'      => __( 'Templates Enabled', '__x__' ),
              'options'    => cs_recall( 'options_group_toggle_off_on_bool_string' ),
              'conditions' => $buddy_press_enabled,
            ],

            [
              'key'     => 'x_buddypress_layout_content',
              'type'    => 'choose',
              'label'   => __( 'Layout', '__x__' ),
              'options' => [ 'choices' => $choices_section_layouts ],
              'conditions' => $buddy_press_enabled,
            ],
            [
              'key'        => 'x_buddypress_header_menu_enable',
              'type'       => 'toggle',
              'label'      => __( 'Navbar Menu', '__x__' ),
              'options'    => cs_recall( 'options_group_toggle_off_on_bool_string' ),
              'conditions' => [ $condition_classic_headers_enabled ],
              'conditions' => $buddy_press_enabled,
            ],
          ],
        ], [
          'type'        => 'group',
          'label'       => $labels['titles'],
          'description' => __( 'Set the titles for the various "components" in BuddyPress (e.g. groups list, registration, et cetera). Keep in mind that the "Sites Title" isn\'t utilized unless you have WordPress Multisite setup on your installation. Additionally, while they might not be present as actual titles on some pages, they are still used as labels in other areas such as the breadcrumbs, so keep this in mind when selecting inputs here.', '__x__' ),
          'conditions' => $buddy_press_enabled,
          'controls'    => [
            [
              'key'   => 'x_buddypress_activity_title',
              'type'  => 'text',
              'label' => __( 'Activity', '__x__' ),
            ],
            [
              'key'   => 'x_buddypress_groups_title',
              'type'  => 'text',
              'label' => __( 'Groups', '__x__' ),
            ],
            [
              'key'   => 'x_buddypress_blogs_title',
              'type'  => 'text',
              'label' => __( 'Sites', '__x__' ),
            ],
            [
              'key'   => 'x_buddypress_members_title',
              'type'  => 'text',
              'label' => __( 'Members', '__x__' ),
            ],
            [
              'key'   => 'x_buddypress_register_title',
              'type'  => 'text',
              'label' => __( 'Register', '__x__' ),
            ],
            [
              'key'   => 'x_buddypress_activate_title',
              'type'  => 'text',
              'label' => __( 'Activate', '__x__' ),
            ],
          ],
        ], [
          'type'        => 'group',
          'label'       => $labels['subtitles'],
          'description' => __( 'Set the subtitles for the various "components" in BuddyPress (e.g. groups list, registration, et cetera). Keep in mind that the "Sites Subtitle" isn\'t utilized unless you have WordPress Multisite setup on your installation. Additionally, subtitles are not utilized across every Stack but are left here for ease of management.', '__x__' ),
          'conditions' => $buddy_press_enabled,
          'controls'    => [
            [
              'key'   => 'x_buddypress_groups_subtitle',
              'type'  => 'text',
              'label' => __( 'Groups', '__x__' ),
            ],
            [
              'key'   => 'x_buddypress_blogs_subtitle',
              'type'  => 'text',
              'label' => __( 'Sites', '__x__' ),
            ],
            [
              'key'   => 'x_buddypress_members_subtitle',
              'type'  => 'text',
              'label' => __( 'Members', '__x__' ),
            ],
            [
              'key'   => 'x_buddypress_register_subtitle',
              'type'  => 'text',
              'label' => __( 'Register', '__x__' ),
            ],
            [
              'key'   => 'x_buddypress_activate_subtitle',
              'type'  => 'text',
              'label' => __( 'Activate', '__x__' ),
            ],
          ],
        ]
      ]
    ];


  }


  // WooCommerce
  // -----------

  if ( class_exists( 'WooCommerce' ) ) {
    $sub_modules[] = apply_filters("cs_theme_options_woocommerce_group", null);
  }


  // Output
  // ------

  return [
    [
      'type'  => 'group-module',
      'label' => __( 'Options', '__x__' ),
      'controls' => apply_filters("cs_theme_options_modules", $sub_modules),
    ]
  ];

}



// Integration
// =============================================================================

add_filter( 'cs_theme_options_controls', 'x_theme_options_register' );

function x_theme_options_preview_setup() {
  add_filter( 'pre_option_x_cache_google_fonts_request', 'x_google_fonts_queue' );
}

add_action('cs_before_preview_frame', 'x_theme_options_preview_setup' );
