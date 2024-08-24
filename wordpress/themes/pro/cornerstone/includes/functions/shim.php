<?php

if ( ! function_exists('x_attr_class')) :

  // Generate Class Attribute
  // =============================================================================


  function x_attr_class( $classes = [] ) {

    if (WP_DEBUG) {
      trigger_error("x_attr_class is now cs_attr_class", E_USER_DEPRECATED);
    }

    return cs_attr_class($classes);

  }

endif;

// Was once an x function
function x_element_decorate( $element, $parent = null ) {
  if (WP_DEBUG) {
    trigger_error("x_element_decorate is now cs_element_decorate", E_USER_DEPRECATED);
  }

  return cs_element_decorate($element, $parent);
}
