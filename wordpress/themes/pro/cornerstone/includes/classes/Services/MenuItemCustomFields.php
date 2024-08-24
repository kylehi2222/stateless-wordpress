<?php

namespace Themeco\Cornerstone\Services;

use Themeco\Cornerstone\Plugin;

class MenuItemCustomFields implements Service {

  protected $plugin;

  public function __construct(Plugin $plugin) {
    $this->plugin = $plugin;
  }

  public function setup() {
    add_action('init', function() {
      require_once $this->plugin->path . '/includes/extend/menu-item-custom-fields/menu-item-custom-fields.php';
      require_once $this->plugin->path . '/includes/extend/menu-item-custom-fields/menu-item-custom-fields-map.php';
    });
  }

}