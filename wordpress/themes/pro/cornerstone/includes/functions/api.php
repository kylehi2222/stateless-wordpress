<?php

use Themeco\Cornerstone\Elements\Decorator;

/**
 * Element Registration
 */

function cs_register_element( $type, $options ) {
  cornerstone('Elements')->register_element( $type, $options );
}

function cs_unregister_element( $name ) {
	cornerstone('Elements')->unregister_element( $name );
}

function cs_register_element_group( $name, $title ) {
  cornerstone( 'ElementLibrary' )->register_group( $name, $title);
}

function cs_register_prefab_element( $group, $name, $options ) {
  cornerstone( 'ElementLibrary' )->register_prefab_element( $group, $name, $options );
}

function cs_prefab_element($group, $name) {
  return cornerstone( 'ElementLibrary' )->get_prefab_element( $group, $name );
}

function cs_prefab_element_values($group, $name) {
  return cornerstone( 'ElementLibrary' )->get_prefab_element_values( $group, $name );
}

function cs_unregister_prefab_element( $group, $name ) {
	cornerstone( 'ElementLibrary' )->unregister_prefab_element( $group, $name );
}

function cs_register_include( $type, $options = [] ) {
  cornerstone('Elements')->register_include( $type, $options );
}

function cs_get_element( $name ) {
  return cornerstone('Elements')->get_element( $name );
}

/**
 * Set which post types should be enabled by default when Cornerstone is first
 * activated.
 * @param  array $types Array of strings specifying post type names.
 * @return void
 */
function cornerstone_set_default_post_types( $types ) {
	// Deprecated
}

/**
 * Allows integrating themes to disable Themeco cross-promotion, and other
 * presentational items. Example:
 *
		cornerstone_theme_integration( array(
			'remove_global_validation_notice' => true,
			'remove_themeco_offers'           => true,
			'remove_purchase_link'            => true,
			'remove_support_box'              => true
		) );
 *
 * @param  array $args List of items to flag
 * @return void
 */
function cornerstone_theme_integration( $args ) {
	$args = cs_define_defaults( $args, array(
    'remove_global_validation_notice' => false,
    'remove_themeco_offers'           => false,
    'remove_purchase_link'            => false,
    'remove_support_box'              => false,
  ) );

  foreach ( $args as $key => $value ) {
    if ( $value ) {
      add_filter( "_cornerstone_integration_$key", '__return_true' );
    }
  }
}


function cornerstone_register_styles( $id, $css, $priority = 1) {
  return cornerstone('Styling')->addStyles( $id, $css, $priority );
}



function cornerstone_options_register_options( $options, $_unused = array() ) {
  cornerstone('ThemeOptions')->register_options( $options );
}

function cornerstone_enqueue_custom_script( $id, $content, $type = 'text/javascript' ) {
	return cornerstone('EnqueueScripts')->addScript( $id, $content, $type );
}

function cornerstone_dequeue_custom_script( $id ) {
	return cornerstone('EnqueueScripts')->removeScript( $id );
}

function cornerstone_post_process_css( $css ) {
	return cornerstone('Styling')->postProcess( $css );
}

function cornerstone_post_process_value( $value ) {
  return cornerstone('DynamicContent')->postProcessValue( $value );
}

function cornerstone_post_process_color( $value ) {
	return apply_filters('cs_css_post_process_color', $value);
}

function cornerstone_cleanup_generated_styles() {
  do_action( 'cs_purge_tmp' );
}

function cornerstone_queue_font( $font ) {
  return cornerstone('GlobalFonts')->queue_font( $font );
}

function cornerstone_dynamic_content_register_field( $field ) {
  cornerstone('DynamicContent')->register_field( $field );
}

function cornerstone_dynamic_content_register_group( $group ) {
  cornerstone('DynamicContent')->register_group( $group );
}

/**
 * Register dynamic content option
 */
function cs_dynamic_content_register_dynamic_option($type, $controls) {
  cornerstone('DynamicContent')->registerDynamicOptions($type, $controls);
}

/**
 * Get registered dynamic content controls
 */
function cs_dynamic_content_get_dynamic_options() {
  return cornerstone('DynamicContent')->getDynamicOptions();
}


function cs_dynamic_content( $content, $asString = true ) {
  return cornerstone('DynamicContent')->run( $content, $asString );
}

function cs_dynamic_content_string( $content ) {
  return cornerstone('DynamicContent')->run( $content, true );
}

/**
 * Get registered option choices from type and params
 *
 * @param string $type
 * @param array $params
 *
 * @return array
 */
function cs_dynamic_options_get($type, $params = []) {
  // Setup if never setup before
  if (!did_action('cs_dynamic_content_setup')) {
    do_action( 'cs_dynamic_content_setup' );
  }

  return array_map(function($option) {
    $option['label'] = esc_html( mb_strimwidth( $option['label'], 0, 60, '...') );
    return $option;
  }, apply_filters( "cs_dynamic_options_$type", [], $params ) );
}

// Dynamic content recusive object render helper

function cs_dynamic_content_object($data = []) {
  if (!is_array($data)) {
    return $data;
  }

  foreach ($data as $key => $value) {
    if (is_string($value)) {
      $data[$key] = cs_dynamic_content($value);
    } else if (is_array($value)) {
      $data[$key] = cs_dynamic_content_object($value);
    }
  }

  return $data;
}


/**
 * Decorate element data with defaults and definition
 */
function cs_element_decorate( $element, $parent = null ) {
  $decorator = new Decorator(cornerstone("Elements"));
  $decorator->setId(1);
  $decorator->setIsPreview(did_action("cs_element_rendering"));
  return $decorator->decorateElement($element, $parent);
}




/**
 * Deprecated
 */
function cornerstone_add_element( $class_name ) {
	CS()->component( 'Classic_Element_Manager' )->add_mk1_element( $class_name );
}

function cornerstone_make_placeholder_pixel( $color = 'rgba(0, 0, 0, 0.35)' ) {
  return cs_placeholder_image( 1, 1, $color );
}

function cornerstone_make_placeholder_image_uri( $color = '#eeeeee', $height = null, $width = null ) {
	return cs_placeholder_image(
    is_null( $height ) ? apply_filters('cs_default_image_height', 48 ) : $height,
    is_null( $width ) ? apply_filters('cs_default_image_width', 48 ) : $width,
    $color
  );
}

function cornerstone_get_element( $name ) {
  return cs_get_element( $name );
}

function cornerstone_register_element( $type, $atts, $deprecated = null ) {
  if ( null !== $deprecated || is_string( $atts ) ) {
    /**
     * Override for old method. Register a new element
     * @param  $class_name Name of the class you've created in definition.php
     * @param  $name       slug name of the element. "alert" for example.
     * @param  $path       Path to the folder containing a definition.php file.
     */
  	CS()->component( 'Classic_Element_Manager' )->add( $type, $atts, $deprecated );
    return;
  }

  cs_register_element( $type, $atts );
}

function cornerstone_remove_element( $name ) {
	CS()->component( 'Classic_Element_Manager' )->remove( $name );
}

function cornerstone_register_integration( $name, $class_name ) {
  trigger_error( "cornerstone_register_integration is deprecated", E_USER_WARNING );
}

function cornerstone_unregister_integration( $name ) {
  trigger_error( "cornerstone_unregister_integration is deprecated", E_USER_WARNING );
}

function cornerstone_options_get_defaults() {
  trigger_error( "cornerstone_options_get_defaults is deprecated", E_USER_WARNING );
  return array();
}

function cornerstone_options_get_default( $name ) {
  trigger_error( "cornerstone_options_get_default is deprecated", E_USER_WARNING );
  return '';
}

function cornerstone_options_get_value( $name ) {
  trigger_error( "cornerstone_options_get_value is deprecated", E_USER_WARNING );
  return '';
}

function cornerstone_options_update_value( $name, $value ) {
  trigger_error( "cornerstone_options_update_value is deprecated", E_USER_WARNING );
  return '';
}

function cornerstone_options_register_section( $name, $value = array() ) {
  trigger_error( "cornerstone_options_register_section is deprecated", E_USER_WARNING );
}

function cornerstone_options_register_sections( $groups ) {
  trigger_error( "cornerstone_options_register_sections is deprecated", E_USER_WARNING );
}

function cornerstone_options_register_control( $option_name, $control ) {
  trigger_error( "cornerstone_options_register_control is deprecated", E_USER_WARNING );
}

function cornerstone_options_unregister_section( $name ) {
  trigger_error( "cornerstone_options_unregister_section is deprecated", E_USER_WARNING );
}

function cornerstone_options_unregister_control( $option_name ) {
  trigger_error( "cornerstone_options_unregister_control is deprecated", E_USER_WARNING );
}

function cornerstone_options_enable_custom_css( $option_name, $selector = '' ) {
  trigger_error( "cornerstone_options_enable_custom_css is deprecated", E_USER_WARNING );
}

function cornerstone_options_enable_custom_js( $option_name ) {
  trigger_error( "cornerstone_options_enable_custom_js is deprecated", E_USER_WARNING );
}


// Control helpers
function cornerstone_control_range($settings) {
  // Setup
  // -----

  $label = ( isset( $settings['label'] ) ) ? $settings['label'] : '';
  $key = ( isset( $settings['key'] ) ) ? $settings['key'] : '';
  $min = ( isset( $settings['min'] ) ) ? $settings['min'] : 0;
  $max = ( isset( $settings['max'] ) ) ? $settings['max'] : 1;
  $steps = ( isset( $settings['steps'] ) ) ? $settings['steps'] : .01;
  $group = ( isset( $settings['group'] ) ) ? $settings['group'] : null;
  $description = ( isset( $settings['description'] ) ) ? $settings['description'] : null;
  $conditions = ( isset( $settings['conditions'] ) ) ? $settings['conditions'] : [];

  // Output
  // ------

  return [
    'key' => $key,
    'group' => $group,
    'description' => $description,
    'type' => 'unit-slider',
    'fallback_value' => 1,
    'label' => $label,
    'conditions' => $conditions,
    'options' => [
      'unit_mode' => 'unitless',
      'min' => $min,
      'max' => $max,
      'step' => $steps,
    ],
  ];
}

/**
 * Render document elements into HTML
 *
 * @param int $postId
 *
 * @return string
 */
function cs_render_document_html($postId) {
  return cornerstone('Resolver')->renderContentByPostId($postId);
}
