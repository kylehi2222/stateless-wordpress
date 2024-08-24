<?php

namespace Themeco\Cornerstone\Elements;

use Themeco\Cornerstone\Util\Parameter;
use Themeco\Cornerstone\Services\Elements;

class Decorator {

  protected $types = [];
  protected $id;
  protected $tss;
  protected $virtualStack = [];
  protected $documentId;
  protected $elementsService;
  protected $isPreview;

  public function __construct(Elements $elements) {
    $this->elementsService = $elements;
  }

  public function setId( $id ) {
    $this->documentId = $id;
    return $this;
  }

  public function setIsPreview( $isPreview = false ) {
    $this->isPreview = $isPreview;
  }

  public function decorate( $elements, $isPreview = false ) {
    $this->setIsPreview($isPreview);
    return [$this->processElements( $elements ), $this->types];
  }

  public function processElements( $elements, $parent = null, $offscreen = '', $depth = 0 ) {

    $decorated = [];

    foreach ($elements as $index => $undecorated) {
      try {
        $decorated[] = $this->decorateElement($undecorated, $parent, [
          '_depth'     => $depth,
          '_order'     => $index,
          '_offscreen' => $offscreen
        ]);
      } catch (\Exception $e) {
        trigger_error( $e->getMessage(), E_USER_WARNING);
      }
    }

    return $decorated;
  }

  public function decorateElement($element, $parent = null, $contextual = [] ) {

    if ( ! isset( $element['_type'] ) ) {
      throw new \Exception('Can not decorate element without _type: ' . $this->documentId);
    }

    if ( $element['_type'] !== 'root' && ! isset( $element['_region'] ) ) {
      throw new \Exception('Can not decorate element without _region: ' . $this->documentId . '| ' . $element['_type']);
    }

    if ( !in_array( $element['_type'], $this->types ) ) {
      $this->types[] = $element['_type'];
    }

    foreach ($contextual as $key => $value) {
      $element[$key] = $value;
    }

    $definition = $this->elementsService->get_element( $element['_type'] );

    $values = $definition->get_aggregated_values();

    // Process markup:color
    $element = apply_filters('cs_element_decorate', $element, $definition);


    if ( ! isset( $element['classes'] ) ) {
      $element['classes'] = [];
    }

    $element = $definition->apply_defaults( $element );

    $element['_p'] = $this->documentId;

    $unique_id = $this->documentId . '-' . $element['_id'];
    $element['style_id'] = 'e' . $unique_id;
    $element['unique_id'] = 'e' . $unique_id;


    if ($this->isPreview) {
      if (! isset( $element['_tss_container'] ) ) {
        $element['_tss_container'] = ( !empty( $parent ) && isset( $parent['_tss_container'] ) && ! isset( $parent['_builder_outlet'] ) ) ? $parent['_tss_container'] : $element['_id'];
      }
    } else {
      $element['_tss_container'] = $element['_p'];
    }

    if ( isset($element['legacy_region_detect']) ) {
      $element['legacy_tbf_detect'] = $element['legacy_region_detect'] && ( $element['_region'] === 'top' || $element['_region'] === 'bottom' || $element['_region'] === 'footer' );
    }

    // Allow shadow elements to get parent keys (e.g. V2 Accordion)
    if ( ! is_null( $parent ) && $definition->shadow_parent() ) {

      $element['p_style_id'] = $parent['style_id'];
      $element['p_unique_id'] = $parent['unique_id'];

      foreach ($parent as $key => $value) {
        if ( ! isset( $element[$key] ) ) {
          $element[$key] = $value;
        }
      }

      $element['_parent_data'] = $parent;
    }

    $children_offscreen = $element['_offscreen'] ? $element['_offscreen'] : '';

    if ( !$children_offscreen && $definition->has_offscreen_dropzone() ) {
      $children_offscreen = $element['_type'];
    }

    $currentComponent = end($this->virtualStack);

    if ($currentComponent && isset( $currentComponent[$element['_id']])) {

      list($egress, $unwrap) = $currentComponent[$element['_id']];

      if ($egress['_type'] === 'component-thru' && $element['_type'] === 'component') {
        $element['_p_data'] = isset( $egress['_p_data'] ) ? $egress['_p_data'] : [];
        $element['_parameters'] = $this->makeParams( $element );


        if (isset( $egress['_customize_component'] ) && $egress['_customize_component'] && isset( $element['_virtual_root'] ) ) {
          $element['_virtual_root'] = $this->mergeCustomize($element['_virtual_root'], $egress);
        }
      }

      if ($egress['_type'] === 'slot' || ($egress['_type'] === 'component-thru' && $element['_type'] !== 'component')) {
        $element['_p_local'] = isset( $egress['_p_data'] ) ? $egress['_p_data'] : [];
        $element['_parameters'] = array_merge(
          $this->makeParams( $element ),
          $egress['_parameters']
        );
      }

      if ($this->isPreview) {
        if (isset( $egress['_builder_atts'] ) && isset( $element['_builder_atts'] ) ) {
          $element['_builder_atts'] = $egress['_builder_atts'];
        }

        if ($egress['_type'] === 'slot' && count( $this->virtualStack ) <=1 ) {
          $element['_builder_atts']['data-cs-dropzone'] = $egress['_id'];
          $element['_builder_outlet'] = $egress['_id'];
        }
      }

    } else {

      $element['_parameters'] = $this->makeParams( $element );

      if ( $this->isPreview && isset( $element['_builder_atts'] ) && count( $this->virtualStack ) <=0 ) {
        $element['_builder_atts']['data-cs-observeable-id'] = $element['_id'];
        $element['_builder_atts']['data-cs-observe'] = 'true';
        if(!$definition->render_children()) {
          if (!isset( $element['_virtual_root'] ) ) {
            $element['_builder_outlet'] = $element['_id'];
          }
        }
      }

    }

    if ( $this->isPreview && isset( $element['_virtual_root'] ) && $element['_virtual_direct'] && count( $this->virtualStack ) <=0 ) {
      $element['_builder_atts']['data-cs-observeable-id'] = $element['_id'];
      $element['_builder_atts']['data-cs-dropzone'] = $element['_id'];
      $element['_builder_outlet'] = $element['_id'];
    }

    $element['_modules'] = $this->processElements(
      isset( $element['_modules'] ) ? $element['_modules'] : [],
      $element,
      $children_offscreen,
      $element['_depth'] + ($element['_type'] === 'root' ? 0 : 1)
    );

    if ( isset( $element['_virtual_root'] )) {

      $map = [];

      if ( !$element['_virtual_direct'] ) {
        foreach ($element['_modules'] as $child) {
          if ( isset( $element['_virtual_map'][$child['_id']] )) {
            $item = $element['_virtual_map'][$child['_id']];
            $map[$item['id']] = [$child, $item['unwrap']];
          }
        }
      }

      $this->virtualStack[] = $map;

      if ( isset( $element['_builder_atts'] ) ) {
        $element['_virtual_root']['_builder_atts'] = $element['_builder_atts'];
      }

      $element['_virtual_root']['_p_local'] = isset( $element['_p_data'] ) ? $element['_p_data'] : [];
      $element['_virtual_root']['_tss_container'] = $element['_tss_container'];

      $element['_virtual_root'] = $this->decorateElement($element['_virtual_root'], $element, [
        '_offscreen' => $children_offscreen,
        '_order' => $element['_order'],
        '_depth' => $element['_depth']
      ]);


      if ( isset($element['_customize_component']) && $element['_customize_component'] ) {
        $element['_virtual_root'] = $this->mergeCustomize($element['_virtual_root'], $element);
      }

      $element['_virtual_root']['_parameters'] = array_merge(
        $element['_virtual_root']['_parameters'],
        $element['_parameters']
      );

      array_pop($this->virtualStack);

    }

    if ( ! in_array( $element['_type'], [ 'component', 'slot', 'component-thru'] ) && isset( $element['_builder_atts'] ) ) {

      if ( $this->isPreview && isset( $element['_builder_atts']['data-cs-observeable-id'] ) )  {
        if ( $definition->is_dropzone() && count( $this->virtualStack ) <=0 ) {
          $element['_builder_atts']['data-cs-dropzone'] = $element['_builder_atts']['data-cs-observeable-id'];
          $element['_builder_outlet'] = $element['_builder_atts']['data-cs-observeable-id'];
        }
      }

    }

    // Figure out what DC keys to render later
    $element['_dc_keys'] = array_filter(array_keys( $element ), function( $key ) use ($element, $values) {
      return is_string($element[$key]) && isset( $values[$key] ) && strpos($values[$key][1],'markup') === 0 && strpos($key, 'looper_') !== 0;
    });


    unset($element['_p_json']);
    return $element;

  }

  public function makeParams( $element ) {
    $input = isset( $element['_p_local'] ) && ! empty ( $element['_p_local'] ) ? $element['_p_local'] : [];
    return Parameter::create( null, null, isset( $element['_p_json'] ) ? $element['_p_json'] : '')->apply( $input );
  }

  public function mergeCustomize( $destination, $source ) {

    // Class
    if ( isset( $source['class'] ) ) {
      if ( ! isset( $destination['class'] ) ) {
        $destination['class'] = '';
      }
      $destination['class'] = trim($destination['class'] . ' ' . $source['class']);
    }

    // Element CSS
    if ( !empty( $source['css'] ) ) {
      if ( ! isset( $destination['css'] ) ) {
        $destination['css'] = '';
      }
      $destination['css'] = trim($destination['css'] . ' ' . $source['css']);
    }

    // Custom Atts
    if ( !empty( $source['custom_atts'] ) ) {
      $into = isset( $destination['custom_atts'] ) ? json_decode( $destination['custom_atts'], true ) : null;
      $into = $into ? $into : [];
      $source_atts = json_decode( $source['custom_atts'], true );
      $source_atts = $source_atts ? $source_atts : [];
      $destination['custom_atts'] = json_encode(array_merge( $into, $source_atts));
    }

    // Retrieve all omega keys and defaults

    $defaults = array_merge(
      cs_defaults( 'omega' ),
      cs_defaults( 'omega:looper-provider' ),
      cs_defaults( 'omega:looper-consumer' )
    );

    // unset values managed directly
    unset( $defaults['class'] );
    unset( $defaults['css'] );



    foreach ($defaults as $key => $value) {


      if ( ! isset($destination[$key]) ) {
        $destination[$key] = $value; // set default
      }

      if ( ! empty( $source[$key] ) ) {
        $destination[$key] = $source[$key];
      }
    }


    return $destination;
  }
}
