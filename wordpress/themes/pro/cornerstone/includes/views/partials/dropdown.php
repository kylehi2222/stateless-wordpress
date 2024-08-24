<?php

// =============================================================================
// VIEWS/PARTIALS/DROPDOWN.PHP
// -----------------------------------------------------------------------------
// Dropdown partial.
// =============================================================================

$_region              = ( isset( $_region )                               ) ? $_region              : '';
$classes              = ( isset( $classes )                               ) ? $classes              : [];
$atts                 = ( isset( $atts )                                  ) ? $atts                 : [];
$dropdown_custom_atts = ( isset( $dropdown_custom_atts )                  ) ? $dropdown_custom_atts : null;
$tag                  = ( isset( $dropdown_tag ) && $dropdown_tag         ) ? $dropdown_tag         : 'div';

// Dropdown hover converted to object
// passed directly to stem.js hoverintent
$hoverintent = cs_split_to_object($_view_data, 'dropdown_hover');
$hoverintent = json_encode($hoverintent);

// Prepare Atts
// ------------

$atts = array_merge([
  'id'                => ( isset( $id ) && ! empty( $id ) ) ? $id . '-dropdown' : $toggleable_id . '-dropdown',
  'class'             => array_merge( [ 'x-dropdown' ], $classes ),
  'data-x-stem'       => NULL,
  'data-x-stem-root'  => NULL,
  'data-x-toggleable' => $toggleable_id,
  'data-x-hoverintent' => $hoverintent,
  'aria-hidden'       => 'true',
], $atts);

if ( isset( $style ) && ! empty( $style ) ) {
  $atts['style'] = $style;
}

if ( $_region === 'left' ) {
  $atts['data-x-stem-root'] = 'h';
}

if ( $_region === 'right' ) {
  $atts['data-x-stem-root'] = 'rh';
}

if (!empty($dropdown_position)) {
  $atts['data-x-stem'] = $dropdown_position;
  $atts['data-x-stem-force'] = $dropdown_position;
}

// RVT reset dynamic rendering
if (!empty($dropdown_content_dynamic_rendering)) {
  $atts['data-rvt-offscreen-reset'] = '';
}

// ESC key close
if (!empty($dropdown_esc_key_close)) {
  $atts['data-x-esc-close'] = '';
}

// Direct close
if (!empty($dropdown_direct_close)) {
  $atts['data-x-dropdown-direct-close'] = '';
}

// Output
// ------

echo cs_tag( $tag, $atts, $dropdown_custom_atts, $dropdown_content);
