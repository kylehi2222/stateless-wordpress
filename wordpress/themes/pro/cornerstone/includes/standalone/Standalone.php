<?php

// @TODO support data
add_filter( 'cs_breakpoint_ranges', function($data) {
  return [ 480, 767, 979, 1200 ];
});

// Default range to match defaults
add_filter( 'cs_breakpoint_default_ranges', function($data) {
  return [ 480, 767, 979, 1200 ];
});


/**
 * Fontawesome setup
 * @TODO consilidate X to this one
 */
add_filter( 'cs_fa_config', function ( $fa ) {
  $fa = array_merge( $fa, [
    'icon_type' => cs_stack_get_value( 'x_font_awesome_icon_type', 'webfont' ),
    'load_type_for_elements' => cs_stack_get_value("x_font_awesome_load_types_for_elements", false),
    'x_font_awesome_icon_type' => cs_stack_get_value( 'x_font_awesome_icon_type', 'webfont' ),
    'x_font_awesome_load_types_for_elements' => cs_stack_get_value( 'x_font_awesome_load_types_for_elements', false ),
    'x_font_awesome_shim_enable' => cs_stack_get_value( 'x_font_awesome_shim_enable', 'markup'),
    'fa_solid_enable'   => (bool) cs_stack_get_value( 'x_font_awesome_solid_enable', true ),
    'fa_regular_enable' => (bool) cs_stack_get_value( 'x_font_awesome_regular_enable', true ),
    'fa_light_enable'   => (bool) cs_stack_get_value( 'x_font_awesome_light_enable', true ),
    'fa_brands_enable'  => (bool) cs_stack_get_value( 'x_font_awesome_brands_enable', true ),
    'fa_sharp-light_enable'  => (bool) cs_stack_get_value( 'x_font_awesome_sharp-light_enable', false ),
    'fa_sharp-regular_enable'  => (bool) cs_stack_get_value( 'x_font_awesome_sharp-regular_enable', false ),
    'fa_sharp-solid_enable'  => (bool) cs_stack_get_value('x_font_awesome_sharp-solid_enable'),
  ]);

  return $fa;
});

// Fix for standalone not working with normal options
add_filter("pre_option_x_font_awesome_icon_type", function() {
  return cs_stack_get_value( 'x_font_awesome_icon_type', 'webfont' );
});
