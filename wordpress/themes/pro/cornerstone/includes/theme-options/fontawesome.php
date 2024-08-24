<?php

/**
 * Filter to add Font Awesome controls
 */

use Themeco\Cornerstone\Services\FontAwesome;

use function Themeco\ElementDesignations\designationLoop;

add_filter("cs_theme_options_fontawesome_group", function() {

  return [
    'type' => 'group',
    'group'       => 'x:layout-and-design',
    'label' => __( 'Font Awesome', '__x__' ),
    'description' => __( 'Below is a list of the various Font Awesome icon types. Enable or disable them depending on your preferences for usage (for example, if you only plan on using the "Light" icons, you can disable all other weights for a slight performance boost in Webfont mode). Keep in mind that completely disabling all Font Awesome icons means that you will not be able to utilize any of the icon pickers throughout our builders and that the markup for icons will still be output to the frontend of your site.', '__x__' ),
    'controls'    => [

      // Load type control
      apply_filters('cs_theme_options_fontawesome_load_types', [
        'key' => 'x_font_awesome_icon_type',
        'global' => true,
      ]),

      // Load type for elements
      [
        'key'     => 'x_font_awesome_load_types_for_elements',
        'label'   => __( 'Element load types', '__x__' ),
        'description'   => __( 'Ability to change the Icon load type for individual elements that support it. This will also remove the SVG directory if this is disabled and load type is set to Webfonts. SVG requires PHP zip installed', '__x__' ),
        'type'    => 'toggle',
      ],

      // Enable controls
      [
        'key'     => 'x_font_awesome_solid_enable',
        'type'    => 'toggle',
        'label'   => __( 'Solid', '__x__' ),
        'options' => cs_recall( 'options_group_toggle_off_on_bool_string' ),
      ],
      [
        'key'     => 'x_font_awesome_regular_enable',
        'type'    => 'toggle',
        'label'   => __( 'Regular', '__x__' ),
        'options' => cs_recall( 'options_group_toggle_off_on_bool_string' ),
      ],
      [
        'key'     => 'x_font_awesome_light_enable',
        'type'    => 'toggle',
        'label'   => __( 'Light', '__x__' ),
        'options' => cs_recall( 'options_group_toggle_off_on_bool_string' ),
      ],

      // Sharp Regular
      [
        'key'     => 'x_font_awesome_sharp-regular_enable',
        'type'    => 'toggle',
        'label'   => __( 'Sharp Regular', '__x__' ),
        'options' => cs_recall( 'options_group_toggle_off_on_bool_string' ),
      ],

      // Sharp Light
      [
        'key'     => 'x_font_awesome_sharp-light_enable',
        'type'    => 'toggle',
        'label'   => __( 'Sharp Light', '__x__' ),
        'options' => cs_recall( 'options_group_toggle_off_on_bool_string' ),
      ],

      // Sharp Solid
      [
        'key'     => 'x_font_awesome_sharp-solid_enable',
        'type'    => 'toggle',
        'label'   => __( 'Sharp Solid', '__x__' ),
        'options' => cs_recall( 'options_group_toggle_off_on_bool_string' ),
      ],

      [
        'key'     => 'x_font_awesome_brands_enable',
        'type'    => 'toggle',
        'label'   => __( 'Brands', '__x__' ),
        'options' => cs_recall( 'options_group_toggle_off_on_bool_string' ),
      ],
    ],
  ];
});

// Load type select
// Returns null if load type for elements is empty
// and not a global control
add_filter("cs_theme_options_fontawesome_load_types", function($params = []) {
  $key = cs_get_array_value($params, 'key', '');
  $conditions = cs_get_array_value($params, 'conditions', []);

  // Not global control and
  // the non global load type controls are disabled
  if (
    empty($params['global'])
    && !FontAwesome::hasIndividualLoadTypes()
  ) {
    return null;
  }

  return [
    'key' => $key,
    'label' => __("Load Type", "cornerstone"),
    'description' => __("SVG will only ever load SVGs you have inserted to your Document. Webfont is the default for sites prior to Pro 6.4.0 and CS 7.4.0, and is useful if you are inserting a large amount of icons in Document."),
    'type' => 'select',
    'conditions' => $conditions,
    'options' => [
      'choices' => apply_filters("cs_theme_options_fontawesome_load_type_choices", []),
    ],
  ];
});

// Load types as select choices
add_filter("cs_theme_options_fontawesome_load_type_choices", function() {
  return [
    [
      'value' => 'svg',
      'label' => cs_recall("label_svg"),
    ],
    [
      'value' => 'webfont',
      'label' => __("Webfont", "cornerstone"),
    ],
  ];
});


/**
 * markup:icon-type
 */
add_filter("cs_element_pre_render", function($element, $definition) {
  return designationLoop($element, $definition, "markup:icon-type", function($value) {
    // Check if individual load types or not set
    if (!FontAwesome::hasIndividualLoadTypes() || empty($value)) {
      return FontAwesome::getDefaultLoadType();
    }

    return $value;
  });
}, 0, 2);
