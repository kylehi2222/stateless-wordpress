<?php

// =============================================================================
// VIEWS/PARTIALS/ICON.PHP
// -----------------------------------------------------------------------------
// Icon partial.
// =============================================================================

use Themeco\Cornerstone\Services\FontAwesome;

$atts        = ( isset( $atts )          ) ? $atts          : array();
$classes     = ( isset( $classes )       ) ? $classes       : array();
$custom_atts = ( isset( $custom_atts )   ) ? $custom_atts   : null;

$icon = cs_dynamic_content( $icon );
$icon_type = ( isset( $icon_type ) && FontAwesome::hasIndividualLoadTypes()  )
  ? $icon_type
  : FontAwesome::getDefaultLoadType();

// Prepare Attr Values
// -------------------

$_classes = [ 'x-icon' ];

if (isset( $atts['class'])) {
  if (is_array( $atts['class'] ) ) {
    $_classes = array_merge( $_classes, $atts['class']);
  } else {
    $_classes[] = $atts['class'];
  }
  unset($atts['class']);
}


// Prepare Atts
// ------------

$atts = array_merge( $atts, array(
  'class'       => array_merge( $_classes, $classes ),
  'aria-hidden' => 'true',
) );

$icon_data                = fa_get_attr( $icon );
$atts[$icon_data['attr']] = $icon_data['entity'];

if ( isset( $id ) && ! empty( $id ) ) {
  $atts['id'] = $id;
}

if ( isset( $style ) && ! empty( $style ) ) {
  $atts['style'] = $style;
}

$atts = cs_apply_effect( $atts, $_view_data );


// SVG output
// URL Image
// Unused currently
if ($icon_type === "svg_image") {
  // Unset data attribute for icon
  unset($atts[$icon_data['attr']]);

  $svgPath = fa_get_svg_path($icon);
  $atts['src'] = $svgPath;

  echo cs_tag( 'img', $atts, $custom_atts, '');
  return;
}

// SVG element output
if ($icon_type === "svg") {
  $svgElement = fa_get_svg_output($icon);

  // Unset data attribute for icon
  unset($atts[$icon_data['attr']]);
  $effects = cs_apply_effect([], $_view_data);

  // Output inside div for better styling
  echo cs_tag( 'div', $atts, $custom_atts, $svgElement);
  return;
}


// Output <i> glyph
// ------

// Load webfonts if not already
FontAwesome::setShouldAddStyles();

echo cs_tag( 'i', $atts, $custom_atts, '');
