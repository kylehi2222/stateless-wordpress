<?php

// =============================================================================
// CORNERSTONE/INCLUDES/ELEMENTS/DEFINITIONS/COMMENT-LIST.PHP
// -----------------------------------------------------------------------------
// V2 element definitions.
// =============================================================================

// =============================================================================
// TABLE OF CONTENTS
// -----------------------------------------------------------------------------
//   01. Values
//   02. Style
//   03. Render
//   04. Builder Setup
//   05. Register Element
// =============================================================================

// Values
// =============================================================================

$values = cs_compose_values(
  [
    'comment_list_type'           => cs_value( 'all', 'markup' ),
    'comment_list_style'          => cs_value( 'ol', 'markup' ),
    'comment_list_order'          => cs_value( 'oldest', 'markup' ),
    'comment_list_avatar_size'    => cs_value( 32, 'markup' ),
    // 'comment_list_base_font_size' => cs_value( '1em' ),
    // 'comment_list_width'          => cs_value( 'auto' ),
    // 'comment_list_max_width'      => cs_value( 'none' ),
    // 'comment_list_bg_color'       => cs_value( 'transparent', 'style:color' ),
    'comment_list_margin'         => cs_value( '!0em' ),
  ],
  'omega',
  'omega:custom-atts'
);



// Style
// =============================================================================

function x_element_tss_comment_list() {
  return [
    'require' => [ 'elements-wp' ],
    'modules' => [ 'comment-list', 'effects' ]
  ];
}



// Render
// =============================================================================

function x_element_render_comment_list( $data ) {

  extract( $data );


  // Prepare Atts
  // ------------

  $atts = [
    'class' => array_merge( [ 'x-comment-markup', 'x-comment-list' ], $data['classes'] )
  ];

  if ( isset( $id ) && ! empty( $id ) ) {
    $atts['id'] = $id;
  }

  if ( isset( $data['style'] ) && ! empty( $data['style'] ) ) {
    $atts['style'] = $data['style'];
  }

  $atts = cs_apply_effect( $atts, $data );


  // Prepare Args
  // ------------

  $args = array(
    'style'             => $comment_list_style,
    // 'format'            => 'html5',
    // 'type'              => 'comment', // $comment_list_type,
    'avatar_size'       => $comment_list_avatar_size, // 32
    'short_ping'        => false,
    // 'echo'              => true,
    // 'walker'            => null,
    // 'max_depth'         => null,
    // 'end-callback'      => null,
    // 'page'              => null,
    // 'per_page'          => null,
    // 'reverse_children'  => null,
  );

  if ( function_exists( 'x_get_stack' ) ) {
    $stack = x_get_stack();
    $callback = cs_stack_is_custom()
      ? 'cs_stack_comment_list'
      : 'x_' . $stack . '_comment';

    $args['callback'] = $callback;
  }

  if ( $comment_list_order === 'newest' ) {
    $args['reverse_top_level'] = true;
  }


  // Simulate wp-comments.php to populate query
  // ------------------------------------------

  $shim_template = function() { return cornerstone()->path . '/includes/views/app/shim.php'; };
  add_filter( 'comments_template', $shim_template );
  comments_template();
  remove_filter( 'comments_template', $shim_template );
  $count = intval(get_comments_number());


  // Output
  // ------
  // 01. Comments are open, has comments.
  // 02. Comments are open, no comments.
  // 03. Comments are closed.

  $list                 = null;
  $output_list_comments = null;
  $output_no_comments   = null;
  $output_closed        = null;

  ob_start();
  echo wp_list_comments( $args );
  $list_template = ob_get_clean();

  $list_args = [ 'class' => 'x-comments-list', 'id' => 'comments'];

  if ( have_comments() && comments_open() && $count > 0 ) { // 01
    $list = cs_tag( $comment_list_style, $list_args, $list_template );
  }

  return cs_tag( 'div', $atts, [
    $list,
    $output_no_comments,
    $output_closed
  ]);
}



// Builder Setup
// =============================================================================

function x_element_builder_setup_comment_list() {

  // Groups
  // ------

  $group        = 'comment_list';
  $group_setup  = $group . ':setup';
  $group_design = $group . ':design';


  // Options
  // -------

  $options_comment_list_type = [
    'choices' => [
      [ 'value' => 'all',       'label' => cs_recall( 'label_all' )        ],
      [ 'value' => 'comment',   'label' => cs_recall( 'label_comments' )   ],
      [ 'value' => 'pingback',  'label' => cs_recall( 'label_pingbacks' )  ],
      [ 'value' => 'trackback', 'label' => cs_recall( 'label_trackbacks' ) ],
      [ 'value' => 'pings',     'label' => cs_recall( 'label_pings' )      ],
    ]
  ];

  $options_comment_list_style = [
    'choices' => [
      [ 'value' => 'ol', 'label' => '<ol>' ],
      [ 'value' => 'ul', 'label' => '<ul>' ],
    ]
  ];

  $options_comment_list_order = [
    'choices' => [
      [ 'value' => 'oldest', 'label' => cs_recall( 'label_oldest' ) ],
      [ 'value' => 'newest', 'label' => cs_recall( 'label_newest' ) ],
    ]
  ];

  $options_comment_list_unit_slider = [
    'unit_mode'       => 'unitless',
    'fallback_value'  => 32,
    'min'             => 16,
    'max'             => 48,
    'step'            => 1,
  ];

  $options_comment_list_no_comments_content = [
    'height' => 2,
  ];

  $options_comment_list_closed_content = [
    'height' => 2,
  ];


  // Settings
  // --------

  $settings_comment_list_design = [
    'group' => $group_design,
  ];


  // Individual Controls (Base)
  // --------------------------

  $control_comment_list_type = [
    'key'     => 'comment_list_type',
    'type'    => 'select',
    'label'   => cs_recall( 'label_type' ),
    'options' => $options_comment_list_type,
  ];

  $control_comment_list_style = [
    'key'     => 'comment_list_style',
    'type'    => 'choose',
    'label'   => cs_recall( 'label_html_tag' ),
    'options' => $options_comment_list_style,
  ];

  $control_comment_list_order = [
    'key'     => 'comment_list_order',
    'type'    => 'choose',
    'label'   => cs_recall( 'label_order_by' ),
    'options' => $options_comment_list_order,
  ];

  $control_comment_list_avatar_size = [
    'key'     => 'comment_list_avatar_size',
    'type'    => 'unit-slider',
    'label'   => cs_recall( 'label_avatar_size' ),
    'options' => $options_comment_list_unit_slider,
  ];

  // $control_comment_list_base_font_size = cs_recall( 'control_mixin_font_size',     [ 'key' => 'comment_list_base_font_size'           ] );
  // $control_comment_list_width          = cs_recall( 'control_mixin_width',         [ 'key' => 'comment_list_width'                    ] );
  // $control_comment_list_max_width      = cs_recall( 'control_mixin_max_width',     [ 'key' => 'comment_list_max_width'                ] );
  // $control_comment_list_bg_color       = cs_recall( 'control_mixin_bg_color_solo', [ 'keys' => [ 'value' => 'comment_list_bg_color' ] ] );


  // Compose Controls
  // ----------------

  return cs_compose_controls(
    [
      'controls' => [
        [
          'type'     => 'group',
          'group'    => $group_setup,
          'controls' => [
            $control_comment_list_type,
            $control_comment_list_style,
            $control_comment_list_order,
            $control_comment_list_avatar_size,
            // $control_comment_list_base_font_size,
            // $control_comment_list_width,
            // $control_comment_list_max_width,
            // $control_comment_list_bg_color,
          ],
        ],
        cs_control( 'margin', 'comment_list', $settings_comment_list_design ),
      ],
      'control_nav' => [
        $group        => cs_recall( 'label_primary_control_nav' ),
        $group_setup  => cs_recall( 'label_setup' ),
        $group_design => cs_recall( 'label_design' ),
      ],
    ],
    cs_partial_controls( 'effects' ),
    cs_partial_controls( 'omega', [ 'add_custom_atts' => true ] )
  );
}



// Register Element
// =============================================================================

cs_register_element( 'comment-list', [
  'title'    => __( 'Comment List', 'cornerstone' ),
  'values'   => $values,
  'includes' => [ 'effects' ],
  'builder'  => 'x_element_builder_setup_comment_list',
  'tss'      => 'x_element_tss_comment_list',
  'render'   => 'x_element_render_comment_list',
  'icon'     => 'native',
  'group'    => 'post',
] );
