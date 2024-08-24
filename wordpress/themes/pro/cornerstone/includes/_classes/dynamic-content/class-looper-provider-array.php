<?php

abstract class Cornerstone_Looper_Provider_Array extends Cornerstone_Looper_Provider {

  protected $items = [];
  protected $itemKeys = [];
  protected $size = 0;
  protected $original_items = [];
  protected $loopKeys = false;

  public function setup( $element ) {

    $items = $this->get_array_items( $element );

    $this->loopKeys = !empty($element['looper_provider_array_loop_keys']);

    // Handle WP errors
    if ( is_wp_error( $items ) ) {
      $this->set_error( $items );
      $items = [];
    }

    // If a single non array item is passed through we will consider it an array of a single item
    if ( ! is_array( $items ) ) {
      $items = [ $items ];
    }

    // Has string keys
    if ($this->has_string_keys($items)) {
      // Loop keys and use the keys as the index name
      if ($this->loopKeys) {
        $this->itemKeys = array_keys($items);
        $items = array_values( $items );
      } else {
        // Dont loop keys
        // create array of just this object
        // this is the default
        $items = [ $items ];
      }
    }

    
  
    $offset = intval( cs_dynamic_content( $element['looper_provider_array_offset'] ) );
    $this->items = $offset === 0 ? $items : array_slice( $items, $offset );
    $this->original_items = $this->items;
    $this->size = count($this->items);
    
  }
  
  public function advance() {
    return array_shift($this->items);
  }

  public function rewind() {
    $this->items = $this->original_items;
  }
  
  public function get_context() {
    return $this->items;
  }

  public function get_index() {
    return $this->size - (count($this->items) + 1); // return zero based index
  }

  /**
   * Check if actually at end
   */
  public function checkForEmpty() {
    return count($this->items) === 0;
  }

  // Gets keyed array name
  // or returns int value + 1
  public function get_index_name() {
    $intIndex = $this->get_index();

    return empty($this->itemKeys[$intIndex])
      ? $intIndex + 1
      : $this->itemKeys[$intIndex];
  }

  public function get_size() {
    return $this->size;
  }

  public function has_string_keys($input) {
    return count(array_filter(array_keys($input), 'is_string')) > 0;
  }

  abstract function get_array_items( $element );

}
