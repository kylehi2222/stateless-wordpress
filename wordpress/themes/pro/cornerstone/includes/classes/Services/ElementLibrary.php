<?php

namespace Themeco\Cornerstone\Services;

use Themeco\Cornerstone\Plugin;

class ElementLibrary implements Service {

  protected $prefab = [];
  protected $prefabs = [];
  protected $groups = [];
  public $plugin;
  public $elements;

  public function __construct(Plugin $plugin, Elements $elements) {
    $this->plugin = $plugin;
    $this->elements = $elements;
  }

  public function setup() {
    $this->register_group('deprecated', __( 'Deprecated', 'cornerstone' ));
    $this->register_group('layout', __( 'Layout', 'cornerstone' ));

    // Group Regsistration
    $this->register_group('structure', __( 'Structure', 'cornerstone' ));
    $this->register_group('content', __( 'Content', 'cornerstone' ));
    $this->register_group('media', __( 'Media', 'cornerstone' ));
    $this->register_group('interactive', __( 'Interactive', 'cornerstone' ));
    $this->register_group('slider', __( 'Slider', 'cornerstone' ));
    $this->register_group('navigation', __( 'Navigation', 'cornerstone' ));
    $this->register_group('wordpress', __( 'WordPress', 'cornerstone' ));
    $this->register_group('dynamic', __( 'Dynamic', 'cornerstone' ));
    $this->register_group('archive', __( 'Archive', 'cornerstone' ));
    $this->register_group('post', __( 'Post', 'cornerstone' ));
    $this->register_group('site', __( 'Site', 'cornerstone' ));
    $this->register_group('classic', __( 'Classic', 'cornerstone' ));
    $this->register_group('advanced', __( 'Advanced', 'cornerstone' ));
  }

  public function register_group( $name, $title ) {
    $this->groups[ $name ] = $title;
  }

  public function register_prefab_element( $group, $name, $options ) {
    if ( ! isset( $this->prefab[ $group ] ) ) {
      $this->prefab[ $group ] = [];
    }

    try {
      $this->prefabs[ $group ][ $name ] = $this->normalize_prefab_element( $options );
    } catch (\Exception $e) {
      trigger_error('Unabled to register prefab: ' . $e->getMessage( ) );
    }

  }

  /**
   * Get prefab element data
   *
   * @param string $group
   * @param string $name
   *
   * @return array
   */
  public function get_prefab_element($group, $name) {
    if (empty($this->prefabs[$group][$name])) {
      trigger_error("Prefab not defined : {$group} / {$name}");
      return [];
    }

    return $this->prefabs[$group][$name];
  }

  /**
   * Get prefab element values that would be used in the element
   *
   * @param string $group
   * @param string $name
   *
   * @return array
   */
  public function get_prefab_element_values($group, $name) {
    $prefab = $this->get_prefab_element($group, $name);

    if (empty($prefab['values'])) {
      return [];
    }

    return $prefab['values'];
  }

  public function normalize_prefab_element( $options ) {

    if (!isset($options['type'])) {
      throw new \Exception('type required');
    }

    $options = array_merge( [
      'scope'  => 'all',
      'title'  => $options['type'],
      'icon'   => '',
      'values' => []
    ], $options );

    $prefab = array_merge( [ '_type' => $options['type'] ], $options['values'] );
    $options['values'] = $this->elements->migrations()->migrate( [ $prefab ] )[0];

    return $options;
  }

  public function unregister_prefab_element( $group, $name ) {
    if (isset( $this->prefab[ $group ] ) ) {
      unset( $this->prefabs[ $group][ $name ] );
    }
  }

  public function get_library() {

    if ( !did_action( 'cs_register_dynamic_elements') ) {
      require_once( $this->plugin->path . '/includes/elements/prefab-elements.php' );
      do_action( 'cs_register_prefab_elements' );
    }

    return [ 'groups' => $this->groups, 'prefabs' => $this->prefabs ];

  }


}
