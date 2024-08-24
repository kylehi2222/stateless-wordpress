<?php

/*

add_filter( 'cs_looper_custom_my_data', function( $result, $args, $element ) {

  return [];
} );

This is also used by LooperProviders
*/

use Themeco\Cornerstone\Services\LooperProviders;

class Cornerstone_Looper_Provider_Custom extends Cornerstone_Looper_Provider_Generic_Array {
  /**
   * From looper provider registry
   */
  private $valuesFromControls = [];
  private $customType = '';

  public function __construct($customType = "", $valuesFromControls = [])
  {
    $this->customType = $customType;
    $this->valuesFromControls = $valuesFromControls;
  }

  // Main filter
  public function get_array_items( $element ) {
    $args = cs_dynamic_content($element['looper_provider_json']);
    $args = is_string($args)
      ? json_decode( $args, true )
      : $args;

    $args = array_merge(
      $this->valuesFromControls,
      is_array($args) ? $args : []
    );

    // If not from Custom Registry API
    // Create a hook from that
    $hook = !empty($this->customType)
      ? $this->customType
      : str_replace( '-', '_', sanitize_title( cs_dynamic_content( $element['looper_provider_custom'] ) ) );

    $result = apply_filters( LooperProviders::CUSTOM_LOOPER_PREFIX . $hook, [], $args, $element );
    return is_array( $result ) ? $result : [$result];
  }
}
