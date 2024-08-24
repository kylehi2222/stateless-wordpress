<?php

namespace Themeco\ControlPartials\Comparison;

/**
 * HTML Editor control partial
 */

cs_register_control_partial( 'comparison-select', function($settings = []) {

  // Setup
  // -----

  $key = cs_get_array_value($settings, 'key', '');

  // Output
  // ------

  return [
    'key' => $key,
    'label' => __('Comparison', CS_LOCALIZE),
    'type' => 'select',
    'options' => [
      'choices' => apply_filters("cs_comparison_choices", []),
    ],
  ];
});


// Filter for comparison choices
add_filter("cs_comparison_choices", function() {
  return [
    [
      'value' => "=",
      "label" => __("Equal To (=)", CS_LOCALIZE),
    ],
    [
      'value' => "!=",
      "label" => __("Does Not Equal (!=)", CS_LOCALIZE),
    ],
    [
      'value' => ">",
      "label" => __("Greater Than (>)", CS_LOCALIZE),
    ],
    [
      'value' => ">=",
      "label" => __("Greater Than Or Equal To (>=)", CS_LOCALIZE),
    ],
    [
      'value' => "<",
      "label" => __("Less Than (<)", CS_LOCALIZE),
    ],
    [
      'value' => "<=",
      "label" => __("Less Than Or Equal To (<=)", CS_LOCALIZE),
    ],
    [
      'value' => "IN",
      "label" => __("In", CS_LOCALIZE),
    ],
    [
      'value' => "NOT IN",
      "label" => __("Not In", CS_LOCALIZE),
    ],
    [
      'value' => "LIKE",
      "label" => __("Like", CS_LOCALIZE),
    ],
    [
      'value' => "NOT LIKE",
      "label" => __("Not Like", CS_LOCALIZE),
    ],
    [
      'value' => "BETWEEN",
      "label" => __("Between", CS_LOCALIZE),
    ],
    [
      'value' => "NOT BETWEEN",
      "label" => __("Not Between", CS_LOCALIZE),
    ],
    [
      'value' => "EXISTS",
      "label" => __("Exists", CS_LOCALIZE),
    ],
    [
      'value' => "NOT EXISTS",
      "label" => __("Does Not Exist", CS_LOCALIZE),
    ],
    [
      'value' => "REGEXP",
      "label" => __("Regular Expression", CS_LOCALIZE),
    ],
    [
      'value' => "NOT REGEXP",
      "label" => __("Not Regular Expression", CS_LOCALIZE),
    ],
  ];
});
