<?php

/**
 * Controls
 */

function cs_control( $type, $key_prefix = '', $control = array() ) {
  return cornerstone('Elements')->controlMacros()->control( $type, $key_prefix, $control );
}

/**
 * Array merge with options
 */
function cs_amend_control( $control, $update ) {
  $options = [];

  if ( isset( $control['options'] ) && isset( $update['options'] ) ) {
    $options = [
      'options' => array_merge( $control['options'], $update['options'] )
    ];
  }

  return array_merge( $control, $update, $options );
}


/**
 * Settings
 */

function cs_remember( $key, $value ) {
  return cornerstone('Registry')->remember( $key, $value );
}

function cs_recall( $key, $args = [] ) {
  return cornerstone('Registry')->recall( $key, $args );
}

function cs_maybe_recall( $key, $fallback = '', $args = [] ) {
  return cornerstone('Registry')->maybe_recall( $key, $fallback, $args );
}

/**
 * Values
 */

function cs_value( $default = null, $designation = 'style', $protected = false ) {
  return [ $default, $designation, $protected ];
}

function cs_values( $values, $key_prefix = '' ) {
  return cornerstone('Registry')->values( $values, $key_prefix );
}

function cs_defaults( $values, $key_prefix = '' ) {
  return cornerstone('Registry')->defaults( $values, $key_prefix );
}

function cs_define_values( $key, $values ) {
  return cornerstone('Registry')->define_values( $key, $values );
}

function cs_extend_values( $key, $values ) {
  return cornerstone('Registry')->extend_values( $key, $values );
}

function cs_compose_values() {
  return cornerstone('Registry')->compose_values( func_get_args() );
}

function cs_compose_controls() {
  return cornerstone('Registry')->compose_partials( func_get_args() );
}

function cs_register_control_partial( $name, $function ) {
  return cornerstone('Registry')->register_control_partial( $name, $function );
}


function cs_partial_controls( $name, $settings = array() ) {
  return cornerstone('Registry')->apply_control_partial( $name, $settings );
}
