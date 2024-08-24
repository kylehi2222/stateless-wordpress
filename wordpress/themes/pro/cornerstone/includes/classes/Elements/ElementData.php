<?php

namespace Themeco\Cornerstone\Elements;

use Themeco\Cornerstone\Plugin;

class ElementData {

  public $plugin;
  public $decorator;
  public $components;
  protected static $idCache = [];
  protected $id;
  protected $data;
  protected $decorated;
  protected $options;
  protected $referencedComponents = [];

  public function __construct(Plugin $plugin, Decorator $decorator) {
    $this->plugin = $plugin;
    $this->decorator = $decorator;
  }

  public function set( $id, $data ) {
    $this->id = $id;
    $this->data = $data;
    $this->decorator->setId( $id );
    return $this;
  }

  public function options( $options ) {
    $this->options = $options;
    return $this;
  }

  public function raw() {
    return $this->data;
  }

  public function decorated() {
    return $this->decoratedWithTypes()[0];
  }

  public function decoratedWithTypes() {
    if ( ! isset( $this->decorated ) ) {
      $this->decorated = $this->decorate();
    }
    return $this->decorated;
  }

  public function decorate() {
    return $this->decorator->decorate( $this->populate( $this->data ), $this->isPreview() );
  }

  /**
   * Decorate one element
   */
  public function decorateElement($element, $parent = null) {
    return $this->decorator->decorate( $element, $parent );
  }

  public function isPreview() {
    return isset( $this->options['preview'] ) && $this->options['preview'];
  }

  public function getPopulator() {
    $populator = $this->plugin->resolve(IdPopulater::class);

    if ( $this->isPreview() ) {
      $populator->setPreview();
    }
    return $populator;
  }

  public static function nextId( $key ) {
    if ( ! isset( self::$idCache[$key] ) ) {
      self::$idCache[$key] = 0;
    }
    return self::$idCache[$key]++;
  }

  public function remapComponentIds( $egressId, $component, $component_id ) {

    $this->referencedComponents[$component["doc"]] = true;

    $idKey = implode('',[$this->id, $egressId, $component_id]);

    $elements = $component['data'];

    $original_map = array_merge(
      isset( $component['thru'] ) ? $component['thru'] : [],
      isset( $component['slots'] ) ? $component['slots'] : []
    );

    $resolved_map = [];

    $walk = function( $originalId ) use ($idKey, $egressId, $elements, &$walk, $original_map, &$resolved_map) {
      $element = $elements[$originalId];

      $uniqueId = $egressId . '-v' . self::nextId( $idKey );

      if (isset( $original_map[$originalId] ) ) {
        $resolved_map[$uniqueId] = [
          'id' => $original_map[$originalId],
          'unwrap' => isset( $element['_c_unwrap'] ) && $element['_c_unwrap']
        ];
      }

      $element['_id'] = $uniqueId;

      if ( isset( $element['_modules'] ) ) {
        $element['_modules'] = array_filter( array_map( $walk, $element['_modules'] ) );
      }

      return $element;
    };

    return [ $walk($component['root']), $resolved_map ];

  }

  public function populate( $elements ) {
    return $this->expandComponents( $this->getPopulator()->populate( $elements )->result() );
  }

  public function expandComponents( $elements ) {

    $this->components = $this->plugin->service('Components');

    $walk = function( $element ) use (&$walk) {

      if ( is_int( $element['_id'] ) ) {
        $element['_id'] = 'e' . $element['_id'];
      }

      if ( ! isset( $element['_modules'] ) ) {
        $element['_modules'] = [];
      }

      if ($this->isPreview()) {
        if ( ! isset( $element['_builder_atts'] ) ) {
          $element['_builder_atts'] = [];
        }
      }

      if ($element['_type'] === 'component') {

        $component = $this->components->getComponent( $element['component_id'] );

        if ( ! $component ) {

          trigger_error('Unable to resolve Cornerstone component with id: ' . $element['component_id'] . ' | ' . $element['_id'], E_USER_WARNING);
          return array_merge($element, [ '_type' => 'undefined']);
        }

        list($populatedComponent, $idMap) = $this->remapComponentIds( $element['_id'], $component, $element['component_id'] );

        $element['_virtual_map'] = $idMap;
        $element['_virtual_root'] = $walk( $populatedComponent );
        $element['_virtual_direct'] = ! isset( $component['slots']) && isset( $component['children']) && $component['children'];

      }


      $element['_modules'] = array_filter( array_map( $walk, $element['_modules'] ) );

      // Remap a final time to have [ "source_id" => "virtual_id" ]
      if ( !empty( $element['_virtual_map'] ) ) {
        $remapped = [];
        foreach ($element['_virtual_map'] as $key => $value) {
          foreach ($element['_modules'] as $child) {
            if ( !empty($child['virtual_id']) && $child['virtual_id'] === $value['id']) {
              $value['id'] = $key;
              $value['type'] = $child['_type'];
              $remapped[$child['_id']] = $value;
              break;
            }
          }
        }

        $element['_virtual_map'] = $remapped;
      }

      return $element;
    };


    $result = array_filter( array_map( $walk, $elements ) );

    return $result;

  }

  public function getReferencedComponents() {
    $this->decorated();
    return array_keys($this->referencedComponents);
  }

}
