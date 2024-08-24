<?php

/**
 * Plugin boilerplate.
 * This bootstraps the plugin and activates the required components.
 */


abstract class Cornerstone_Plugin_Component {
  public function setup() { }
}

class Cornerstone_Plugin {

  protected $registry;
  protected $components = [];
  protected $path;
  protected $url;
  protected $novus;
  public static $instance;
  protected $autoload_directories = [];

  /**
   * Assign plugin variables
   */
  public function __construct( $novus ) {
    $this->registry = $novus->config();
    $this->path = $this->registry['path'];
    $this->url = $this->registry['url'];
    $this->novus = $novus;
  }

  public function novus($service = '') {
    return $service ? $this->novus->service( $service ) : $this->novus;
  }

  public function path( $to = '' ) {
    return $this->path  . $to;
  }

  public function initialize() {
    $class_folder = self::$instance->path( 'includes/_classes' );
    $this->autoload_directories = glob( $class_folder . '/*', GLOB_ONLYDIR );
    $this->autoload_directories[] = $class_folder;
		spl_autoload_register( array( __CLASS__, 'autoloader' ) );
    return $this;
  }

  public function component( $name ) {

    if ( ! isset( $this->components[ $name ] ) ) {

      $class = 'Cornerstone_' . $name;
      $exists = false;

      try {
        $exists = class_exists( $class );
      } catch ( Exception $e ) {
        trigger_error( 'Exception: ' . $e->getMessage() . "\n" );
      }

      if ( ! $exists ) {
        trigger_error("Could not load Component | $name", E_USER_WARNING);
        return false;
      }

      if ( false === $name ) {
        return false;
      }

      $instance = new $class( $this );
      $this->components[ $name ] = $instance;

      $instance->setup();

    }

    return $this->components[ $name ];

  }


	/**
	 * Return plugin instance.
	 * @return object  Singleton instance
	 */
	public static function instance() {
		return ( isset( self::$instance ) ) ? self::$instance : false;
	}

  public static function run( $novus ) {
    self::$instance = new self( $novus );
    return self::$instance->initialize();
  }

	/**
	 * Cornerstone class autoloader.
	 * @param  string $class_name
	 * @return void
	 */
	public static function autoloader( $class_name ) {

		if ( false === strpos( $class_name, 'Cornerstone' ) ) {
			return;
		}

		$class = str_replace( 'Cornerstone_', '', $class_name );
		$file = 'class-' . str_replace( '_', '-', strtolower( $class ) ) . '.php';

		foreach ( self::$instance->autoload_directories as $directory ) {

			$path = $directory . '/' . $file;

			if ( ! file_exists( $path ) ) {
				continue;
			}

			require_once( $path );

		}

  }

}
