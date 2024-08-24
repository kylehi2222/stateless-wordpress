<?php

namespace Themeco\Cornerstone;

use Themeco\Cornerstone\Util\Factory;

class Plugin {

  public static $instance;
  public $path;
  public $url;

  protected $container;
  protected $services_container = [];
  protected $services = [];
  protected $config = [];
  protected $registry = [];

  public function setupIocContainer() {
    Factory::setup();
    $this->container = Factory::container();
    $this->container->register(__CLASS__, $this);
    $this->container->setRegistrationHandler(function($class, $instance, $interfaces, $hasSetup) {
      if ( isset( $interfaces[ __NAMESPACE__ . '\Services\Service'])) {
        $this->container->register($class, $instance);
        if ( $hasSetup ) {
          $instance->setup();
        }
      }
    });
    return $this;
  }

  public function initialize( $config = []) {
    $this->config = $config;

    $this->path = untrailingslashit( $this->config['path'] );
    $this->url = untrailingslashit( $this->config['url'] );

    $this->load_config_files( 'includes' );

    $services = $this->config('services');
    foreach ($services as $class) {
      $this->resolve( $class );
    }

    add_action( 'after_setup_theme', function() {
      load_plugin_textdomain( 'cornerstone', false, $this->path . '/lang' );
      $this->load_config_files( 'integrations' );
    } );

  }

  public function load_config_files( $key ) {
    $files = $this->config( $key);

    foreach ( $files as $files) {
      if ( is_string( $files) ) {
        require_once( $this->path . $files);
      } else {
        list($enabled, $path) = $files;
        if ( $enabled ) {
          require_once( $this->path . $path );
        }
      }

    }
  }

  /**
   * Simple ioc container and dependency injection.
   * Services (that implement the Service interface) will be
   * registered as singletons
   */
  public function resolve($class) {
    return $this->container->resolve($class);
  }

  public function service( $name ) {
    return $this->resolve( __NAMESPACE__ . "\Services\\$name" );
  }

  public function config( $key = null ) {
    if ($key === null) {
      return $this->config;
    }
    if ( ! isset( $this->config[ $key ] ) ) {
      return null;
    }
    return is_callable( $this->config[ $key ] ) ? call_user_func( $this->config[ $key ] ) : $this->config[ $key ];
  }

  public function resolveFromConfig( $key ) {
    $group = $this->config($key);

    foreach ($group as $class) {
      $setup = [ $this->resolve($class), 'setup' ];
      if ( is_callable( $setup ) ) {
        call_user_func( $setup );
      }
    }
  }

  /**
   * Plugin getter function allowing quick access to read only properties
   * and anything placed into $registry when the plugin was initialized
   */
  public function __get($name) {
    switch ($name) {
      case 'path':
        return $this->path;
      case 'url':
        return $this->url;
      default:
        return null;
    }
  }

  public function path( $path = '') {
    return $this->path . $path;
  }

  public function url( $url = '') {
    return $this->url . $url;
  }

  /**
   * Singleton Instantiation
   */
  public static function instance() {
    if ( ! isset( self::$instance ) ) {
      self::$instance = new self;
      self::$instance->setupIocContainer();
      spl_autoload_register([__CLASS__, 'autoloader']);
    }
    return self::$instance;
  }

  public static function autoloader($class) {
    $prefix = 'Themeco\\Cornerstone\\';
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) return;
    $filename = self::$instance->path . '/includes/classes/' . str_replace('\\', '/', substr($class, $len)) . '.php';
    if (file_exists($filename)) require_once $filename;
  }

}
