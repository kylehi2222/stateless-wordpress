<?php

namespace Themeco\StarterStack;

$breakpoint_keys = [
  'base' => 'x_breakpoint_base',
];

if ( apply_filters( 'x_legacy_allow_breakpoint_config', false ) ) {
  $breakpoint_keys['ranges'] = 'x_breakpoint_ranges';
}

// Template enabled theme options

define("STARTER_WOOCOMMERCE_ENABLED", class_exists("WooCommerce"));


// Register stack
cs_stack_register(
  //Config
  [
    'id' => 'starter',
    'label' => __("Starter", "__x__"),

    // Extends blank stack
    'extends' => 'blank',

    'stylesheets' => [],

    // Values to save in DB
    'values' => [],

    // Css files and outputs
    'css' => [
      getPHPCSS(__DIR__ . '/css/starter-layout.css'),

      // Font mode
      x_get_option("x_root_font_size_mode") === "stepped"
        ? getPHPCSS(__DIR__ . '/css/starter-font-stepped.css')
        : __DIR__ . '/css/starter-font-scaling.css'
        ,

      __DIR__ . '/css/starter-inputs.css',

      // this is included because of some flow controls
      getWCCSS(),

      getPHPCSS(__DIR__ . '/css/starter-typography.css'),
    ],

    // Stack controls
    'controls' => function() {
      return [

        // Layout and Design
        [
          'type'  => 'group-sub-module',
          'label' => __( 'Layout and Design', '__x__' ),
          'options' => [ 'tag' => 'layout-and-design', 'name' => 'x-theme-options:layout-and-design' ],
          'controls' => [
            // Layout
            apply_filters("cs_theme_options_layout_group", null),
          ],
        ],


        // Typography
        apply_filters("cs_theme_options_typography_group", null, ['no_oembed' => true]),

      ];
    },
  ]
);

function getWCCSS() {

  if (!STARTER_WOOCOMMERCE_ENABLED) {
    return '';
  }


  return getPHPCSS(__DIR__ . '/css/starter-woocommerce.css');
}

/**
 * PHP CSS render
 * @TODO do through templates
 */
function getPHPCSS($file) {

  // WC CSS
  ob_start();

  include($file);

  return ob_get_clean();

}
