<?php

// =============================================================================
// FRAMEWORK/FUNCTIONS/PRO/BARS/DEFINITIONS/BAR.PHP
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
  cs_values( 'flex-box', 'bar_row' ),
  cs_values( 'flex-box', 'bar_col' ),
  [
    'bar_base_font_size'     => cs_value( '1em', 'style' ),
    'bar_position_top'       => cs_value( 'relative', 'markup' ),
    'bar_width'              => cs_value( '240px', 'style' ), // left / right bars only
    'bar_height'             => cs_value( '100px', 'style' ), // top / bottom bars only
    'bar_margin_top'         => cs_value( '0px', 'style' ),
    'bar_margin_sides'       => cs_value( '0px', 'style' ),
    'bar_overflow'           => cs_value( 'visible', 'style' ),
    'bar_z_index'            => cs_value( '9999', 'style' ),
    'bar_bg_color'           => cs_value( '#ffffff', 'style:color' ),
    'bar_bg_advanced'        => cs_value( false, 'markup' ),

    'bar_global_container'   => cs_value( false, 'markup' ),
    'bar_outer_spacing'      => cs_value( '20px', 'style' ),
    'bar_content_length'     => cs_value( '100%', 'style' ),
    'bar_content_max_length' => cs_value( 'none', 'style' ),
  ],
  [
    'bar_scroll_allowed'                       => cs_value( true, 'markup' ), // true when bar_height !== auto
    'bar_scroll'                               => cs_value( false, 'markup' ),
    'bar_scroll_buttons'                       => cs_value( false, 'markup' ),
    'bar_scroll_buttons_font_size'             => cs_value( '16px', 'style' ),
    'bar_scroll_buttons_offset'                => cs_value( '0px', 'style' ),
    'bar_scroll_buttons_bck_icon'              => cs_value( 'o-chevron-left', 'markup', true ),
    'bar_scroll_buttons_fwd_icon'              => cs_value( 'o-chevron-right', 'markup', true ),
    'bar_scroll_buttons_width'                 => cs_value( '2em', 'style' ),
    'bar_scroll_buttons_height'                => cs_value( '3em', 'style' ),
    'bar_scroll_buttons_color'                 => cs_value( 'rgba(111, 111, 111, 0.88)', 'style' ),
    'bar_scroll_buttons_color_alt'             => cs_value( 'rgba(77, 77, 77, 0.99)', 'style' ),
    'bar_scroll_buttons_bg_color'              => cs_value( 'rgba(226, 226, 226, 0.94)', 'style' ),
    'bar_scroll_buttons_bg_color_alt'          => cs_value( '', 'style' ),
    'bar_scroll_buttons_border_width'          => cs_value( '!0px', 'style' ),
    'bar_scroll_buttons_border_style'          => cs_value( 'none', 'style' ),
    'bar_scroll_buttons_border_color'          => cs_value( 'transparent', 'style:color' ),
    'bar_scroll_buttons_border_color_alt'      => cs_value( '', 'style:color' ),
    'bar_scroll_buttons_bck_border_radius'     => cs_value( '0px 4px 4px 0px', 'style' ),
    'bar_scroll_buttons_fwd_border_radius'     => cs_value( '4px 0px 0px 4px', 'style' ),
    'bar_scroll_buttons_box_shadow_dimensions' => cs_value( '!0px 0px 0px 0px', 'style' ),
    'bar_scroll_buttons_box_shadow_color'      => cs_value( 'transparent', 'style:color' ),
    'bar_scroll_buttons_box_shadow_color_alt'  => cs_value( '', 'style:color' ),

    'bar_sticky'                               => cs_value( false, 'markup' ),
    'bar_sticky_keep_margin'                   => cs_value( false, 'markup' ),
    'bar_sticky_hide_initially'                => cs_value( false, 'markup' ),
    'bar_sticky_only_show_on_scroll_up'        => cs_value( false, 'markup' ),
    'bar_sticky_starts_fixed'                  => cs_value( false, 'markup' ),
    'bar_sticky_slide_enabled'                 => cs_value( true, 'markup' ),
    'bar_sticky_z_stack'                       => cs_value( false, 'markup' ),
    'bar_sticky_trigger_selector'              => cs_value( '', 'markup' ),
    'bar_sticky_trigger_offset'                => cs_value( '0', 'markup' ),
    'bar_sticky_shrink'                        => cs_value( '1', 'markup' ),
    'bar_sticky_scroll_offset'                 => cs_value( true, 'markup' ),

    'bar_row_flexbox'                          => cs_value( true, 'style' ),
    'bar_row_flex_direction'                   => cs_value( 'row', 'style' ),
    'bar_row_flex_wrap'                        => cs_value( false, 'style' ),
    'bar_row_flex_justify'                     => cs_value( 'space-between', 'style' ),
    'bar_row_flex_align'                       => cs_value( 'center', 'style' ),

    'bar_col_flexbox'                          => cs_value( true, 'style' ),
    'bar_col_flex_direction'                   => cs_value( 'column', 'style' ),
    'bar_col_flex_wrap'                        => cs_value( false, 'style' ),
    'bar_col_flex_justify'                     => cs_value( 'space-between', 'style' ),
    'bar_col_flex_align'                       => cs_value( 'center', 'style' ),

    'bar_padding'                              => cs_value( '!0px 0px 0px 0px', 'style' ),
    'bar_border_width'                         => cs_value( '!0px', 'style' ),
    'bar_border_style'                         => cs_value( 'none', 'style' ),
    'bar_border_color'                         => cs_value( 'transparent', 'style:color' ),
    'bar_border_radius'                        => cs_value( '!0px', 'style' ),
    'bar_box_shadow_dimensions'                => cs_value( '0px 3px 25px 0px', 'style' ),
    'bar_box_shadow_color'                     => cs_value( 'rgba(0, 0, 0, 0.15)', 'style:color' ),
  ],
  'omega',
  'omega:custom-atts'
);



// Style
// =============================================================================

function x_element_tss_bar() {
  return [
    'modules' => [
      'bar',
      ['effects',[
        'args' => [
          'selectors' => '.x-bar-scroll-button'
        ],
      ]]
    ]
  ];
}





// Render
// =============================================================================

function x_element_render_bar( $data ) {

  $bar_region_is_lr                       = $data['_region'] === 'left' || $data['_region'] === 'right';
  $bar_region_is_tbf                      = !$bar_region_is_lr;
  $bar_is_sticky                          = $data['_region'] === 'top' && $data['bar_sticky'] === true;
  $bar_has_content_scrolling              = $data['bar_scroll'] === true && $data['bar_scroll_allowed'];

  $is_preview = apply_filters( 'cs_is_preview', false );

  $bar_position = cs_identity_bar_position( $data );


  // Spacers
  // -------

  $bar_space_html = cs_create_bar_space( $data );

  // Relative top bar spacers

  $bar_before_space = '';
  if ( $data['bar_position_top'] === 'relative' && $bar_is_sticky && ! $data['bar_sticky_hide_initially'] ) {
    if ( $is_preview ) {
      $bar_before_space = $bar_space_html;
    } else {
      echo $bar_space_html;
    }
  }


  // Deferred Bar Spaces
  // -------------------
  // Runs concurrently with code from the bar setup functions to allow for
  // proper output of spaces to hooks.
  //
  // 01. Always tie bottom bars into the footer
  // 02. If we are previewing, attach the hooks for left and right bars
  //     Right bars have a different action in the preview.

  if ( $bar_position === 'fixed' ) {

    if ( 'bottom' === $data['_region'] ) { // 01
      cs_defer_html( 'x_before_site_end', $bar_space_html );
    }

    if ( apply_filters( 'cs_is_preview_render', false ) ) { // 02

      $preview_bar_space_actions = array(
        'top'   => 'x_before_site_begin',
        'left'  => 'x_before_site_begin',
        'right' => 'x_after_site_end', // 02
      );

      if ( isset( $preview_bar_space_actions[$data['_region']] ) ) {
        cs_defer_html( $preview_bar_space_actions[$data['_region']], $bar_space_html );
      }

    }

  }


  // Output
  // ------

  $bar_region_is_lr                       = $data['_region'] === 'left' || $data['_region'] === 'right';
  $bar_region_is_tbf                      = ! $bar_region_is_lr;
  $bar_is_sticky                          = $data['_region'] === 'top' && $data['bar_sticky'] === true;
  $bar_has_content_scrolling              = $data['bar_scroll'] === true && $data['bar_scroll_allowed'];
  $bar_has_content_scrolling_with_buttons = $bar_region_is_tbf && $bar_has_content_scrolling && $data['bar_scroll_buttons'] === true;


  // Prepare Classes
  // ---------------

  $classes = [
    'x-bar',
    'x-bar-' . $data['_region'],
    $bar_region_is_lr ? 'x-bar-v' : 'x-bar-h',
    'x-bar-' . $bar_position
  ];

  if ( $bar_is_sticky ) {
    $classes[] = 'x-bar-is-sticky';
  }


  if ( $bar_is_sticky && $data['bar_sticky_hide_initially'] ) {
    $classes[] = 'x-bar-is-initially-hidden';
  }

  if ( ( $bar_region_is_lr || ( $bar_region_is_tbf && $data['bar_global_container'] == false ) ) && $data['bar_scroll'] === false ) {
    $classes[] = 'x-bar-outer-spacers';
  }


  // Prepare Data
  // ------------

  $bar_data = array(
    'id'     => $data['unique_id'],
    'region' => $data['_region'],
  );

  if ( $bar_region_is_lr ) {
    $bar_data['width'] = $data['bar_width'];
  }

  if ( $bar_region_is_tbf ) {
    $bar_data['height'] = $data['bar_height'];
  }

  if ( $bar_is_sticky ) {
    $bar_data['keepMargin']      = $data['bar_sticky_keep_margin'];
    $bar_data['hideInitially']   = $data['bar_sticky_hide_initially'];
    $bar_data['zStack']          = $data['bar_sticky_z_stack'];
    $bar_data['scrollOffset']    = $data['bar_sticky_scroll_offset'];
    $bar_data['triggerOffset']   = $data['bar_sticky_trigger_offset'];
    $bar_data['triggerSelector'] = $data['bar_sticky_trigger_selector'];
    $bar_data['shrink']          = $data['bar_sticky_shrink'];
    $bar_data['only_show_on_scroll_up'] = $data['bar_sticky_only_show_on_scroll_up'];
    $bar_data['slideEnabled'] = $data['bar_sticky_slide_enabled'];
  }

  if ( $bar_has_content_scrolling ) {
    $bar_data['scroll'] = true;

    if ( $bar_has_content_scrolling_with_buttons ) {
      $bar_data['scrollButtons'] = true;
    }
  }


  // Atts: Bar
  // ---------

  if ($bar_is_sticky && !empty($data['bar_sticky_starts_fixed'])) {
    $classes[] = 'x-bar-fixed';
  }

  $atts_bar = array(
    'class'      => array_merge( $classes, $data['classes'] ),
    'data-x-bar' => cs_prepare_json_att( $bar_data, true ),
  );

  if ( isset( $data['id'] ) && ! empty( $data['id'] ) ) {
    $atts_bar['id'] = $data['id'];
  }

  if ( isset( $data['style'] ) && ! empty( $data['style'] ) ) {
    $atts_bar['style'] = $data['style'];
  }

  $atts_bar = cs_apply_effect( $atts_bar, $data );


  // Atts: Bar Scroll
  // ----------------

  $bar_scroll_begin   = '';
  $bar_scroll_end     = '';
  $bar_scroll_buttons = '';

  if ( $bar_has_content_scrolling ) {

    // $suppress_scroll      = ( $bar_region_is_tbf ) ? 'suppressScrollY' : 'suppressScrollX';
    // $atts_bar_scroll_data = array( $suppress_scroll => true );
    // $atts_bar_scroll      = array( 'class' => array( $style_id, 'x-bar-scroll', 'x-bar-outer-spacers' ), 'data-x-scrollbar' => cs_prepare_json_att( $atts_bar_scroll_data, true ) );
    // $bar_scroll_begin     = '<div ' . cs_atts( $atts_bar_scroll ) . '>';
    // $bar_scroll_end       = '</div>';

    $bar_scroll_begin = cs_open_tag('div', [ 'class' => [ $data['style_id'], 'x-bar-scroll-outer' ] ])
      . cs_open_tag('div', [ 'class' => [ $data['style_id'], 'x-bar-scroll-inner', 'x-bar-outer-spacers' ] ]);
    $bar_scroll_end        = '</div></div>';

    if ( $bar_has_content_scrolling_with_buttons ) {

      $atts_bar_scroll_button_bck  = array( 'class' => 'x-bar-scroll-button is-bck', 'data-x-bar-scroll-button' => 'bck' );
      $atts_bar_scroll_button_fwd  = array( 'class' => 'x-bar-scroll-button is-fwd', 'data-x-bar-scroll-button' => 'fwd' );
      $bar_scroll_buttons_icon_bck = cs_get_partial_view( 'icon', [ 'icon' => $data['bar_scroll_buttons_bck_icon'] ] );
      $bar_scroll_buttons_icon_fwd = cs_get_partial_view( 'icon', [ 'icon' => $data['bar_scroll_buttons_fwd_icon'] ] );
      $bar_scroll_buttons          = '<button ' . cs_atts( $atts_bar_scroll_button_bck ) . '>' . $bar_scroll_buttons_icon_bck . '</button><button ' . cs_atts( $atts_bar_scroll_button_fwd ) . '>' . $bar_scroll_buttons_icon_fwd . '</button>';

    }

  }


  // Content
  // -------

  $content_classes = array( $data['style_id'], 'x-bar-content' );

  if ( $bar_region_is_tbf && $data['bar_global_container'] == true ) {
    $content_classes[] = 'x-container max width';
  }

  $atts_bar_content = array(
    'class' => $content_classes
  );

  return cs_tag('div', $atts_bar, $data['custom_atts'], [
    $bar_before_space,
    $data['bar_bg_advanced'] ? cs_make_bg( $data ) : '',
    $bar_scroll_begin,
    cs_tag('div', $atts_bar_content, cs_render_child_elements( $data, 'x_bar' ) ),
    $bar_scroll_end,
    $bar_scroll_buttons
  ]);

}



// Builder Setup
// =============================================================================

function x_element_builder_setup_bar() {

  // Conditions
  // ----------

  $condition_bar_container_enabled                                         = [ 'bar_global_container' => true ];
  $condition_bar_container_disabled                                        = [ 'bar_global_container' => false ];
  $condition_bar_is_t                                                      = [ '_region' => 'top' ];
  $condition_bar_is_not_t                                                  = [ 'key' => '_region', 'op' => '!=', 'value' => 'top' ];
  $conditions_bar_is_t_and_sticky                                          = [ $condition_bar_is_t, [ 'bar_sticky' => true ] ];
  $conditions_bar_is_t_and_absolute                                        = [ $condition_bar_is_t, [ 'bar_position_top' => 'absolute' ] ];
  $condition_bar_is_lr                                                     = [ 'key' => '_region', 'op' => 'IN', 'value' => [ 'left', 'right' ] ];
  $condition_bar_is_tbf                                                    = [ 'key' => '_region', 'op' => 'NOT IN', 'value' => [ 'left', 'right' ] ];
  $condition_bar_height_is_auto                                            = [ 'bar_height' => 'auto' ];
  $condition_bar_height_is_not_auto                                        = [ 'key' => 'bar_height', 'op' => '!=', 'value' => 'auto' ];
  $conditions_bar_is_tbf_and_height_is_not_auto_and_scrolling              = [ $condition_bar_is_tbf, $condition_bar_height_is_not_auto, [ 'bar_scroll' => true ] ];
  $conditions_bar_is_tbf_and_height_is_not_auto_and_scrolling_with_buttons = [ $condition_bar_is_tbf, $condition_bar_height_is_not_auto, [ 'bar_scroll' => true ], [ 'bar_scroll_buttons' => true ] ];


  // Options
  // -------

  $options_bar_position_top = [
    'choices' => [
      [ 'value' => 'relative', 'label' => cs_recall( 'label_relative' ) ],
      [ 'value' => 'absolute', 'label' => cs_recall( 'label_absolute' ) ],
    ],
  ];

  $options_bar_and_scroll_buttons_offset = [
    'available_units' => [ 'px', 'em', 'rem' ],
    'valid_keywords'  => [ 'calc' ],
    'fallback_value'  => '0px',
    'ranges'          => [
      'px'  => [ 'min' => 0, 'max' => 50, 'step' => 1    ],
      'em'  => [ 'min' => 0, 'max' => 3,  'step' => 0.05 ],
      'rem' => [ 'min' => 0, 'max' => 3,  'step' => 0.05 ],
    ],
  ];

  $options_bar_sticky_trigger_offset = [
    'unit_mode'      => 'unitless',
    'fallback_value' => 0,
    'min'            => 0,
    'max'            => 150,
    'step'           => 1,
  ];

  $options_bar_sticky_shrink = [
    'unit_mode'      => 'unitless',
    'fallback_value' => 1,
    'min'            => 0.33,
    'max'            => 1,
    'step'           => 0.001,
  ];


  // Settings
  // --------

  $settings_bar_scroll_buttons = [
    'label_prefix' => cs_recall( 'label_button' ),
    'group'        => 'bar:scrolling',
    'options'      => cs_recall( 'options_color_swatch_base_interaction_labels' ),
    'conditions'   => $conditions_bar_is_tbf_and_height_is_not_auto_and_scrolling_with_buttons,
    'alt_color'    => true,
  ];

  $settings_bar_scroll_bck_button = [
    'label_prefix' => cs_recall( 'label_back_button' ),
    'group'        => 'bar:scrolling',
    'options'      => cs_recall( 'options_color_swatch_base_interaction_labels' ),
    'conditions'   => $conditions_bar_is_tbf_and_height_is_not_auto_and_scrolling_with_buttons,
    'alt_color'    => true,
  ];

  $settings_bar_scroll_fwd_button = [
    'label_prefix' => cs_recall( 'label_forward_button' ),
    'group'        => 'bar:scrolling',
    'options'      => cs_recall( 'options_color_swatch_base_interaction_labels' ),
    'conditions'   => $conditions_bar_is_tbf_and_height_is_not_auto_and_scrolling_with_buttons,
    'alt_color'    => true,
  ];


  // Individual Controls - Children
  // ------------------------------

  $control_bar_children = [
    'type'  => 'children',
    'group' => 'bar:children'
  ];


  // Individual Controls - Setup
  // ---------------------------

  $control_bar_base_font_size = cs_recall( 'control_mixin_font_size', [ 'key' => 'bar_base_font_size'                               ] );
  $control_bar_width          = cs_recall( 'control_mixin_width',     [ 'key' => 'bar_width', 'condition' => $condition_bar_is_lr   ] );
  $control_bar_height         = cs_recall( 'control_mixin_height',    [ 'key' => 'bar_height', 'condition' => $condition_bar_is_tbf ] );

  $control_bar_position_top = [
    'key'       => 'bar_position_top',
    'type'      => 'choose',
    'title'     => cs_recall( 'label_position' ),
    'condition' => $condition_bar_is_t,
    'options'   => $options_bar_position_top,
  ];

  $control_bar_offset_top = [
    'key'        => 'bar_margin_top',
    'type'       => 'unit-slider',
    'label'      => cs_recall( 'label_offset_top' ),
    'conditions' => $conditions_bar_is_t_and_absolute,
    'options'    => $options_bar_and_scroll_buttons_offset,
  ];

  $control_bar_offset_sides = [
    'key'        => 'bar_margin_sides',
    'type'       => 'unit-slider',
    'label'      => cs_recall( 'label_offset_sides' ),
    'conditions' => $conditions_bar_is_t_and_absolute,
    'options'    => $options_bar_and_scroll_buttons_offset,
  ];

  $control_bar_options = [
    'keys' => [
      'scroll' => 'bar_scroll',
    ],
    'type'    => 'checkbox-list',
    'label'   => cs_recall( 'label_options' ),
    'conditions' => [ $condition_bar_is_not_t ],
    'options' => [
      'list' => [
        [ 'key' => 'scroll', 'label' => cs_recall( 'label_scrolling' ) ],
      ],
    ],
  ];

  $control_bar_options_top = [
    'keys' => [
      'scroll' => 'bar_scroll',
      'sticky' => 'bar_sticky',
    ],
    'type'    => 'checkbox-list',
    'label'   => cs_recall( 'label_options' ),
    'conditions' => [ $condition_bar_is_t ],
    'options' => [
      'list' => [
        [ 'key' => 'scroll', 'label' => cs_recall( 'label_scrolling' ) ],
        [ 'key' => 'sticky', 'label' => cs_recall( 'label_sticky' )    ],
      ],
    ],
  ];

  $control_bar_overflow    = cs_recall( 'control_mixin_overflow',          [ 'key' => 'bar_overflow'                                                  ] );
  $control_bar_z_index     = cs_recall( 'control_mixin_z_index',           [ 'key' => 'bar_z_index'                                                   ] );
  $control_bar_background  = cs_recall( 'control_mixin_bg_color_solo_adv', [ 'keys' => [ 'value' => 'bar_bg_color', 'checkbox' => 'bar_bg_advanced' ] ] );


  // Individual Controls - Content Sizing
  // ------------------------------------

  $control_bar_global_container_placeholder = cs_recall( 'control_mixin_global_container_placeholder_x3', [ 'key' => 'bar_global_container', 'conditions' => [ $condition_bar_is_tbf, $condition_bar_container_enabled ]                                                                                ] );
  $control_bar_outer_spacing_tbf            = cs_recall( 'control_mixin_font_size',                       [ 'key' => 'bar_outer_spacing', 'label' => cs_recall( 'label_gutters' ), 'desc' => cs_recall( 'label_gutters' ), 'conditions' => [ $condition_bar_is_tbf, $condition_bar_container_disabled ] ] );
  $control_bar_content_width                = cs_recall( 'control_mixin_width',                           [ 'key' => 'bar_content_length', 'label' => cs_recall( 'label_width' ), 'conditions' => [ $condition_bar_is_tbf, $condition_bar_container_disabled ]                                          ] );
  $control_bar_content_max_width            = cs_recall( 'control_mixin_max_width',                       [ 'key' => 'bar_content_max_length', 'label' => cs_recall( 'label_max_width' ), 'conditions' => [ $condition_bar_is_tbf, $condition_bar_container_disabled ]                                  ] );
  $control_bar_outer_spacing_lr             = cs_recall( 'control_mixin_font_size',                       [ 'key' => 'bar_outer_spacing', 'label' => cs_recall( 'label_gutters' ), 'desc' => cs_recall( 'label_gutters' ), 'condition' => $condition_bar_is_lr                                          ] );
  $control_bar_content_height               = cs_recall( 'control_mixin_width',                           [ 'key' => 'bar_content_length', 'label' => cs_recall( 'label_height' ), 'condition' => $condition_bar_is_lr                                                                                  ] );
  $control_bar_content_max_height           = cs_recall( 'control_mixin_max_width',                       [ 'key' => 'bar_content_max_length', 'label' => cs_recall( 'label_max_height' ), 'condition' => $condition_bar_is_lr                                                                          ] );


  // Individual Controls - Content Scrolling
  // ---------------------------------------

  $control_bar_scroll_buttons_enable = [
    'key'        => 'bar_scroll_buttons',
    'type'       => 'choose',
    'label'      => cs_recall( 'label_button_navigation' ),
    'conditions' => $conditions_bar_is_tbf_and_height_is_not_auto_and_scrolling,
    'options'    => cs_recall( 'options_choices_off_on_bool' ),
  ];

  $control_bar_scroll_buttons_bck_icon = [
    'key'        => 'bar_scroll_buttons_bck_icon',
    'type'       => 'icon',
    'label'      => cs_recall( 'label_back_icon' ),
    'conditions' => $conditions_bar_is_tbf_and_height_is_not_auto_and_scrolling_with_buttons,
  ];

  $control_bar_scroll_buttons_fwd_icon = [
    'key'        => 'bar_scroll_buttons_fwd_icon',
    'type'       => 'icon',
    'label'      => cs_recall( 'label_forward_icon' ),
    'conditions' => $conditions_bar_is_tbf_and_height_is_not_auto_and_scrolling_with_buttons,
  ];

  $control_bar_scroll_buttons_icons = [
    'type'       => 'group',
    'label'      => cs_recall( 'label_icons' ),
    'conditions' => $conditions_bar_is_tbf_and_height_is_not_auto_and_scrolling_with_buttons,
    'controls'   => [
      $control_bar_scroll_buttons_bck_icon,
      $control_bar_scroll_buttons_fwd_icon,
    ],
  ];

  $control_bar_scroll_buttons_font_size = cs_recall( 'control_mixin_font_size', [ 'key' => 'bar_scroll_buttons_font_size', 'conditions' => $conditions_bar_is_tbf_and_height_is_not_auto_and_scrolling_with_buttons ] );

  $control_bar_scroll_buttons_offset = [
    'key'        => 'bar_scroll_buttons_offset',
    'type'       => 'unit-slider',
    'label'      => cs_recall( 'label_offset' ),
    'conditions' => $conditions_bar_is_tbf_and_height_is_not_auto_and_scrolling_with_buttons,
    'options'    => $options_bar_and_scroll_buttons_offset,
  ];

  $control_bar_scroll_buttons_width    = cs_recall( 'control_mixin_width',        [ 'key' => 'bar_scroll_buttons_width', 'conditions' => $conditions_bar_is_tbf_and_height_is_not_auto_and_scrolling_with_buttons                                                                ] );
  $control_bar_scroll_buttons_height   = cs_recall( 'control_mixin_height',       [ 'key' => 'bar_scroll_buttons_height', 'conditions' => $conditions_bar_is_tbf_and_height_is_not_auto_and_scrolling_with_buttons                                                               ] );
  $control_bar_scroll_buttons_color    = cs_recall( 'control_mixin_color_int',    [ 'keys' => [ 'value' => 'bar_scroll_buttons_color', 'alt' => 'bar_scroll_buttons_color_alt' ], 'conditions' => $conditions_bar_is_tbf_and_height_is_not_auto_and_scrolling_with_buttons       ] );
  $control_bar_scroll_buttons_bg_color = cs_recall( 'control_mixin_bg_color_int', [ 'keys' => [ 'value' => 'bar_scroll_buttons_bg_color', 'alt' => 'bar_scroll_buttons_bg_color_alt' ], 'conditions' => $conditions_bar_is_tbf_and_height_is_not_auto_and_scrolling_with_buttons ] );


  // Individual Controls - Sticky
  // ----------------------------

  $control_bar_sticky_trigger_selector = [
    'key'        => 'bar_sticky_trigger_selector',
    'type'       => 'text',
    'label'      => cs_recall( 'label_trigger_selector' ),
    'conditions' => $conditions_bar_is_t_and_sticky,
    'options'    => [ 'placeholder' => cs_recall( 'label_target_element_optional' ) ],
  ];

  $control_bar_sticky_trigger_offset = [
    'key'        => 'bar_sticky_trigger_offset',
    'type'       => 'unit-slider',
    'label'      => cs_recall( 'label_trigger_offset' ),
    'conditions' => $conditions_bar_is_t_and_sticky,
    'options'    => $options_bar_sticky_trigger_offset,
  ];

  $control_bar_sticky_shrink = [
    'key'        => 'bar_sticky_shrink',
    'type'       => 'unit-slider',
    'label'      => cs_recall( 'label_shrink_amount' ),
    'conditions' => $conditions_bar_is_t_and_sticky,
    'options'    => $options_bar_sticky_shrink,
  ];

  $control_bar_sticky_options = [
    'keys' => [
      'sticky_keep_margins'   => 'bar_sticky_keep_margin',
      'sticky_hide_initially' => 'bar_sticky_hide_initially',
      'sticky_only_show_on_scroll_up' => 'bar_sticky_only_show_on_scroll_up',
      'sticky_starts_fixed' => 'bar_sticky_starts_fixed',
      'sticky_slide_enabled' => 'bar_sticky_slide_enabled',
      'sticky_z_stack'        => 'bar_sticky_z_stack',
      'sticky_scroll_offset' => 'bar_sticky_scroll_offset',
    ],
    'type'       => 'checkbox-list',
    'label'      => cs_recall( 'label_options' ),
    'conditions' => $conditions_bar_is_t_and_sticky,
    'options'    => [
      'list' => [
        [ 'key' => 'sticky_keep_margins',   'label' => cs_recall( 'label_keep_margin' )    ],
        [ 'key' => 'sticky_hide_initially', 'label' => cs_recall( 'label_hide_initially' ) ],
        [ 'key' => 'sticky_z_stack',        'label' => cs_recall( 'label_z_index_stack' )  ],
        [
          'key' => 'sticky_starts_fixed',
          'label' => __('Starts Fixed', CS_LOCALIZE),
          'description' => __('This header should be sticky when the page loads', CS_LOCALIZE),
        ],
        [
          'key' => 'sticky_slide_enabled',
          'label' => __('Slide Enabled', CS_LOCALIZE),
          'description' => __('Should the bar slide when fixing. Slide does not happen when the bar is the first bar', CS_LOCALIZE),
        ],
        [
          'key' => 'sticky_only_show_on_scroll_up',
          'label' => __('Scroll Up', CS_LOCALIZE),
          'description' => __('Only show when scrolling up', CS_LOCALIZE),
        ],
        [
          'key' => 'sticky_scroll_offset',
          'label' => __('Scroll Offset', CS_LOCALIZE),
          'description' => __('When scrolling through an anchor, use the height of the sticky bar to offset the scrolling position', CS_LOCALIZE),
        ],
      ],
    ],
  ];


  // Compose Controls
  // ----------------

  return cs_compose_controls(
    [
      'controls' => [
        $control_bar_children,
        [
          'type'     => 'group',
          'group'    => 'bar:setup',
          'controls' => [
            $control_bar_base_font_size,
            $control_bar_width,
            $control_bar_height,
            $control_bar_options,
            $control_bar_options_top,
            $control_bar_position_top,
            $control_bar_offset_top,
            $control_bar_offset_sides,
            $control_bar_overflow,
            $control_bar_z_index,
            $control_bar_background,
          ],
        ],
      ],
      'control_nav' => [
        'bar'                   => cs_recall( 'label_primary_control_nav' ),
        'bar:children'          => cs_recall( 'label_children' ),
        'bar:setup'             => cs_recall( 'label_setup' ),
        'bar:background-layers' => cs_recall( 'label_background_layers' ),
        'bar:size'              => cs_recall( 'label_content_sizing' ),
        'bar:scrolling'         => cs_recall( 'label_content_scrolling' ),
        'bar:sticky'            => cs_recall( 'label_sticky' ),
        'bar:design'            => cs_recall( 'label_design' ),
      ]
    ],
    cs_partial_controls( 'bg', [
      'group'     => 'bar:background-layers',
      'condition' => [ 'bar_bg_advanced' => true ],
    ] ),
    [
      'controls' => [
        [
          'keys'    => [ 'checkbox' => 'bar_global_container' ],
          'type'    => 'group',
          'label'   => cs_recall( 'label_nbsp' ),
          'group'   => 'bar:size',
          'options' => [
            'checkbox'         => cs_recall( 'options_group_checkbox_off_on_bool', [ 'label' => cs_recall( 'label_global_container' ) ] )
          ],
          'condition' => $condition_bar_is_tbf,
          'controls'  => [
            $control_bar_global_container_placeholder,
            $control_bar_outer_spacing_tbf,
            $control_bar_content_width,
            $control_bar_content_max_width,
          ],
        ],
        [
          'type'      => 'group',
          'title'     => cs_recall( 'label_content_sizing' ),
          'group'     => 'bar:size',
          'condition' => $condition_bar_is_lr,
          'controls'  => [
            $control_bar_outer_spacing_lr,
            $control_bar_content_height,
            $control_bar_content_max_height,
          ],
        ],
        [
          'type'      => 'group',
          'group'     => 'bar:scrolling',
          'condition' => $condition_bar_height_is_not_auto,
          'controls'  => [
            $control_bar_scroll_buttons_enable,
            // $control_bar_scroll_buttons_bck_icon,
            // $control_bar_scroll_buttons_fwd_icon,
            $control_bar_scroll_buttons_icons,
            $control_bar_scroll_buttons_font_size,
            $control_bar_scroll_buttons_offset,
            $control_bar_scroll_buttons_width,
            $control_bar_scroll_buttons_height,
            $control_bar_scroll_buttons_color,
            $control_bar_scroll_buttons_bg_color,
          ],
        ],
        cs_control( 'border', 'bar_scroll_buttons', $settings_bar_scroll_buttons ),
        cs_control( 'border-radius', 'bar_scroll_buttons_bck', $settings_bar_scroll_bck_button ),
        cs_control( 'border-radius', 'bar_scroll_buttons_fwd', $settings_bar_scroll_fwd_button ),
        cs_control( 'box-shadow', 'bar_scroll_buttons', $settings_bar_scroll_buttons ),
        [
          'type'      => 'group',
          'group'     => 'bar:sticky',
          'condition' => $condition_bar_is_t,
          'controls'  => [
            $control_bar_sticky_trigger_selector,
            $control_bar_sticky_trigger_offset,
            $control_bar_sticky_shrink,
            $control_bar_sticky_options,
          ],
        ],
        cs_control( 'flexbox', 'bar', [
          'group'      => 'bar:design',
          'layout_pre' => 'row',
          'conditions' => [ $condition_bar_is_tbf ],
        ] ),
        cs_control( 'flexbox', 'bar', [
          'group'      => 'bar:design',
          'layout_pre' => 'col',
          'conditions' => [ $condition_bar_is_lr ],
        ] ),
        cs_control( 'padding', 'bar', [ 'group' => 'bar:design', 'condition' => $condition_bar_height_is_auto ] ),
        cs_control( 'border', 'bar', [ 'group' => 'bar:design' ] ),
        cs_control( 'border-radius', 'bar', [ 'group' => 'bar:design' ] ),
        cs_control( 'box-shadow', 'bar', [ 'group' => 'bar:design' ] )
      ]
    ],
    cs_partial_controls( 'effects', [ 'has_provider' => false ] ),
    cs_partial_controls( 'omega', [ 'add_custom_atts' => true ] )
  );

}



// Register Element
// =============================================================================

cs_register_element( 'bar', [
  'title'      => __( 'Bar', 'cornerstone' ),
  'values'     => $values,
  'migrations' => [
    [
      'bar_base_font_size'                       => '16px',
      'bar_width'                                => '15em',
      'bar_height'                               => '6em',
      'bar_outer_spacing'                        => '2em',
      'bar_scroll_buttons_box_shadow_dimensions' => '!0em 0em 0em 0em',
      'bar_padding'                              => '!0em',
      'bar_border_radius'                        => '!0px 0px 0px 0px',
      'bar_box_shadow_dimensions'                => '0em 0.15em 2em 0em',
    ],
  ],
  'includes'   => [ 'bg', 'effects' ],
  'builder'    => 'x_element_builder_setup_bar',
  'tss'        => 'x_element_tss_bar',
  'render'     => 'x_element_render_bar',
  'icon'       => 'native',
  'children'   => 'x_bar',
  'group'      => 'layout',
  'options'    => [
    'valid_children'    => 'container',
    'valid_parent'      => 'region',
    'unnestable'        => true,
    'index_labels'      => true,
    'empty_placeholder' => false,
    'is_draggable'      => false,
	'scope'             => [ 'layout:header', 'layout:footer' ],
    'default_children'  => [ [ '_type' => 'container', 'container_flex' => '1 0 auto' ] ],
    'add_new_element'   => [ '_type' => 'container' ],
    'contrast_keys'     => [ 'bg:bar_bg_advanced', 'bar_bg_color' ],
    'side_effects'      => [
      [
        'observe'    => 'bar_height',
        'conditions' => [ ['key' => 'bar_height', 'op' => '!=', 'value' => 'auto' ] ],
        'apply'      => [ 'bar_scroll_allowed' => true ]
      ],
      [
        'observe'    => 'bar_height',
        'conditions' => [ ['key' => 'bar_height', 'op' => '==', 'value' => 'auto' ] ],
        'apply'      => [ 'bar_scroll_allowed' => false ]
      ],
      [
        'observe'    => 'bar_scroll',
        'conditions' => [
          ['key' => '_region', 'op' => 'IN', 'value' => ['top', 'bottom', 'footer'] ],
          ['key' => 'bar_scroll_allowed', 'op' => '==', 'value' => true ],
          ['key' => 'bar_scroll', 'op' => '==', 'value' => true ],
        ],
        'apply' => [
          'bar_content_length'     => 'auto',
          'bar_content_max_length' => 'none'
        ]
      ],
      [
        'observe'    => 'bar_bg_advanced',
        'conditions' => [
          ['key' => 'bar_bg_advanced', 'op' => '==', 'value' => true ],
          ['key' => 'bar_z_index',     'op' => '==', 'value' => 'auto' ]
        ],
        'apply' => [
          'bar_z_index' => '1'
        ]
      ]
    ]
  ]
] );
