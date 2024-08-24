<?php

/**
* WordPress built in nav menu integration
*/

namespace Themeco\Cornerstone\Services;

class NavMenu implements Service {

  public function __construct() {
  }

  /**
  * Implementation
  */
  public function setup() {
    // Add dynamic content to nav menus
    add_filter("wp_nav_menu_items", function($items) {
      if (apply_filters("cs_use_dynamic_content_in_nav_menu", true)) {
        return cs_dynamic_content($items);
      }

      return $items;
    }, 200);
  }

}
