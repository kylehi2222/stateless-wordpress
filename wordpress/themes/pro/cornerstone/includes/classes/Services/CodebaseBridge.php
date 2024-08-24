<?php

namespace Themeco\Cornerstone\Services;

use Themeco\Cornerstone\Plugin;


// Prior to WordPress supporting modern PHP versions allowing namespaces
// the Cornerstone_Plugin class was the main entry point. It was responsible
// for loading all the services. This CodebaseBridge class helps integrate
// the new pattern for loading services with the old one.
class CodebaseBridge implements Service {

  protected $plugin;
  protected $legacyPlugin;

  public function __construct(Plugin $plugin) {
    $this->plugin = $plugin;
  }

  public function setup() {
    require_once $this->plugin->path . '/includes/_classes/plugin.php';
    $this->legacyPlugin = \Cornerstone_Plugin::run( $this->plugin );
    add_action( 'init', array( $this, 'tco_init' ) );
		add_action( 'admin_init', array( $this, 'tco_init' ) );
  }

  public function legacyPlugin() {
    return $this->legacyPlugin;
  }

	public function tco_init() {
		tco_common()->init( [ 'url' => $this->plugin->url . '/assets/tco' ]);
	}


}
