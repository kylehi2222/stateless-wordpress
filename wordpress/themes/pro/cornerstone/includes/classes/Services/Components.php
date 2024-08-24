<?php

namespace Themeco\Cornerstone\Services;

use Themeco\Cornerstone\Plugin;
use Themeco\Cornerstone\Documents\SettingsFragment;
use Themeco\Cornerstone\Documents\Component;
use Themeco\Cornerstone\Util\Factory;

class Components implements Service {
  public static $COMPONENT_NUM = 0;

  protected $plugin;
  protected $resolver;
  protected $elementsService;
  protected $cache;

  public function __construct(Plugin $plugin, Resolver $resolver, Elements $elements) {
    $this->plugin = $plugin;
    $this->resolver = $resolver;
    $this->elementsService = $elements;
  }

  public function setup() {
    add_shortcode( 'cs_component', [ $this, 'component_shortcode' ] );
    add_shortcode( 'cs_gb', [ $this, 'gb_shortcode' ] );
    add_action( 'init', [ $this, 'register' ], 20 );
    add_action( 'cs_save_component', [ $this, 'purge_cache' ] );
    add_action( 'cs_delete_component', [ $this, 'purge_cache' ] );
    add_action( 'cs_purge_tmp', [ $this, 'purge_cache' ] );

    // Instance ID of currently rendering shortcode
    // Needed if using multiple of the same component
    // to get proper styling
    add_filter("cs_component_instance_id", function() {
      return static::$COMPONENT_NUM;
    });
  }

  public function register() {

    cs_register_element( 'component', [
      'title'   => __( 'Component', 'cornerstone' ),
      'values'  => cs_compose_values(
        [
          'component_id' => cs_value( '' )
        ],
        'omega',
        'omega:custom-atts',
        'omega:looper-provider',
        'omega:looper-consumer'
      ),
      'builder' => function() {
        return cs_compose_controls(
          cs_partial_controls( 'omega',[
            'add_custom_atts' => true,
            'add_looper_provider' => true,
            'add_looper_consumer' => true,
            'add_presets' => false,
            'condition' => [ '_customize_component' => true ]
          ] )
        );
      },
      'icon'    => 'native',
      'options'    => [
        'templates' => false,
        'library' => false, // added via component library
        'valid_children' => '*',
        'no_controls_message' => __('This Component does not have any custom parameters mapped to it.', 'cornerstone' )
      ]
    ] );

    cs_register_element( 'component-thru', [
      'title'   => __( 'Component', 'cornerstone' ),
      'values'  => cs_compose_values(
        [
          'component_id' => cs_value( '' ),  // ID of the component to be resolved
          'virtual_id' => cs_value( '' ),    // ID used to virtualize this element
        ],
        'omega',
        'omega:custom-atts',
        'omega:looper-provider',
        'omega:looper-consumer'
      ),
      'builder' => function() {
        return cs_compose_controls(
          cs_partial_controls( 'omega',[
            'add_custom_atts' => true,
            'add_looper_provider' => true,
            'add_looper_consumer' => true,
            'add_presets' => false,
            'condition' => [ '_customize_component' => true, ]
          ] )
        );
      },
      'icon'    => 'native',
      'options' => [ 'templates' => false, 'library' => false ]
    ] );


    cs_register_element( 'slot', [
      'title'   => csi18n('common.component-slot'),
      'values'  => cs_compose_values(
        [
          'virtual_id' => cs_value( '' )
        ]
      ),
      'builder' => function() {
        return cs_compose_controls(
          [
            'controls' => [
              [
                'type'  => 'children',
                'group' => 'component:children'
              ]
            ],
            'control_nav' => [
              'component' => cs_recall( 'label_primary_control_nav' ),
              'component:children' => cs_recall( 'label_children' ),
            ],
          ]
        );
      },
      'icon'    => 'native',
      'options' => [ 'templates' => false, 'library'   => false, 'valid_children' => '*' ]
    ] );

  }


  /**
  * Use component as shortcode output
  */

  public function component_shortcode( $fullAtts ) {

    // Attributes
    $atts = shortcode_atts([
      'id'    => '',
      'class' => '',
      'name'  => '',
      'parameters' => [],
      'no_wrap' => false,
    ], $fullAtts, 'cs_component' );

    // No valid ID
    if (empty($atts['id'])) {
      if (WP_DEBUG) {
        trigger_error("No ID passed to component shortcode");
      }

      return "";
    }

    // Grabber
    $component = $this->getComponent($atts['id']);

    $parameters = is_string($atts['parameters'])
      ? json_decode($atts['parameters'], true)
      : [];

    // Invalid Parameters passed
    if (!is_array($parameters)) {
      if (WP_DEBUG) {
        trigger_error("Parameters passed to {$atts['id']} invalid");
      }

      $parameters = [];
    }

    $parametersFromAtts = $fullAtts;

    // Dissalowed
    unset($parametersFromAtts['id']);
    unset($parametersFromAtts['class']);
    unset($parametersFromAtts['name']);
    unset($parametersFromAtts['parameters']);

    $parameters = array_merge($parametersFromAtts, $parameters);

    // Errors
    if (!$component) {
      if (WP_DEBUG) {
        trigger_error("Component with id not found : " . $atts['id'], E_USER_NOTICE);
      }

      return;
    }

    $region = 'content-' . self::$COMPONENT_NUM;

    $componentData = [
      '_region'         => $region,
      //'_region'         => 'content',
      '_type'           => 'global-block',
      'global_block_id'    => $component['doc'],
      'component_id'    => $atts['id'],
      'no_wrap'    => !empty($atts['no_wrap']),
      'style_id' => $atts['id'] . $region,
      '_id' => $atts['id'],
      '_p' => $component['doc'],
      '_p_data' => $parameters,
    ];

    // Decorates element
    // renders from _virtual_root
    $elementData = $this
      ->resolver->makeElementData($component['doc'], [$componentData])
      ->decorated();

    // Get CSS Output
    //$css = cornerstone("Tss")->processComponentShortcode($elementData, $atts['id']);

    // Get HTML output
    $html = $this->elementsService->renderElements($elementData);

    //echo "<code>" . phtml($html) . "</code>";

    //$html = "<div class='cs-component-content'>{$html}</div>";

    //$html .= "<style>".$css."</style>";

    ++self::$COMPONENT_NUM;

    // Output from renderElements
    return $html;
  }


  public function gb_shortcode( $atts ) {

    $atts = shortcode_atts( array(
      'id'    => '',
      'class' => '',
      'name'  => '',
    ), $atts, 'cs_gb' );

    if ( !empty($atts['name']) ) {
      $posts = get_posts( [ 'name'=>$atts['name'], 'post_type' => 'cs_global_block','posts_per_page' => 1, 'post_status' => ['tco-data', 'publish'] ] );
      $atts['id'] = empty( $posts ) ? $atts['id'] : $posts[0]->ID;
    }

    ob_start();

    do_action( 'cs_gb_shortcode_before', $atts );

    echo $this->elementsService->renderElements($this->resolver->makeElementData($atts['id'],[
      [
        'style_id'        => $atts['id'],
        '_region'         => 'content',
        '_type'           => 'global-block',
        '_p'              => $atts['id'],
        'global_block_id' => $atts['id'],
        'class'           => $atts['class']
      ]
    ])->decorated());

    do_action( 'cs_gb_shortcode_after', $atts );

    return ob_get_clean();

  }

  public function purge_cache() {
    global $wpdb;
    $wpdb->delete( $wpdb->postmeta, [ 'meta_key' => '_cs_component_map' ] );
    delete_option( 'cs_component_cache' );
  }


  public function enumerate() {
    if ( ! isset( $this->cache ) ) {
      $this->cache = $this->loadCache();
    }
    return $this->cache;
  }

  public function appData() {
    $data = $this->enumerate();
    return [
      'componentData'         => $data[0],
      'componentDocSettings'  => $data[1],
      'componentVirtualIndex' => $data[2],
      'errors' => $data[3],
    ];
  }

  public function getComponent( $componentId ) {
    $all = $this->enumerate();
    return isset($all[0][$componentId]) ? $all[0][$componentId] : null;
  }

  public function loadCache() {

    $cache = cs_maybe_json_decode( get_option( 'cs_component_cache' ) );

    if ( empty( $cache ) ) {
      $cache = $this->generateComponentCache();
      update_option( 'cs_component_cache', cs_json_encode( $cache ) );
    }

    return $cache;
  }

  public function generateComponentCache() {

    $maps = [];
    $errors = [];

    $posts = get_posts([
      'post_type'      => 'cs_global_block',
      'post_status'    => 'tco-data',
      'suppress_filters' => true,
      'cs_all_wpml' => true,
      'posts_per_page' => 1000
    ]);

    $docs = [];
    $result = [[],[],[], $errors];

    foreach ( $posts as $post ) {
      $document = $this->resolver->getDocument( $post );

      if (! is_a( $document, Component::class )) continue;

      $settings = $document->settings();
      $id = $document->id();
      $cid = "c" . $id;
      $result[1][$cid] = [
        $settings['customCSS'],
        $settings['customJS'],
        $settings['library_group'],
        $settings['document_visibility']
      ];
      $docs[$id] = $document;

      $componentMap = $document->getComponentMap();

      foreach ($componentMap as $componentId => $component) {
        if ( isset( $maps[$componentId] ) ) {
          $docId1 = $id;
          $docId2 = $maps[$componentId][0];
          $errors[] = "Overlapping component _c_id value: $componentId. Documents $docId1 and $docId2";
        }
        $maps[$componentId] = [$id,$component];
      }

    }

    if ( empty( $docs ) ) {
      return $result;
    }

    foreach ( $maps as $componentId => $value ) {

      list($docId, $info) = $value;
      $elementId = $info['id'];

      $slotIds = [];

      foreach( $info['slots'] as $slotSet) {
        $slotIds[] = $slotSet[0];
      }

      $data = $docs[$docId]->getScopedElementData( $elementId, $slotIds );

      $component = [
        'root'     => $elementId, // Component Root element ID
        'doc'      => $docId,     // Document ID for retrieving settings
        'data'     => $data
      ];

      if ( ! empty( $info['slots'] ) && $info['slots'][0][0] === $elementId ) {
        $component['children'] = true;
      } else {
        if (! empty( $info['slots'] ) ) {
          $component['slots'] = [];
          foreach ($info['slots'] as $virtual) {
            list($virtualElementId, $virtualComponentId) = $virtual;
            $component['slots'][$virtualElementId] = $virtualComponentId;
            $result[2][$virtualComponentId] = [$componentId,$virtualElementId];
          }
        }

        if (! empty( $info['thru'] ) ) {
          $component['thru'] = [];
          foreach ($info['thru'] as $virtual) {
            list($virtualElementId, $virtualComponentId) = $virtual;
            $component['thru'][$virtualElementId] = $virtualComponentId;
            $result[2][$virtualComponentId] = [$componentId,$virtualElementId];
          }
        }
      }

      $result[0][$componentId] = $component;

    }

    return $result;

  }

  public function resolve( $id ) {
    $this->enumerate();

    if ( ! isset( $this->cache[0][$id]) ) {
      throw new \Exception('not-found');
    }

    $doc_id = $this->cache[0][$id]['doc'];
    list($css, $js) = $this->cache[1]['c' . $doc_id];

    return [
      Factory::create(SettingsFragment::class)->setup((int) $doc_id, [
        'customCss' => $css,
        'customJs'  => $js
      ] ),
      []
    ];
  }

}
