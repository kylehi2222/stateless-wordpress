<?php

namespace Themeco\Cornerstone\Documents;

use Themeco\Cornerstone\Elements\ComponentMap;
use Themeco\Cornerstone\Util\Factory;

class Component extends Document {

  protected $type = 'component';
  protected $permissionContext = 'component';
  protected $meta;

  private $componentOutputId = null;
  private $componentInstanceId = null;
  private $componentOutputParameters = null;
  private $_decoded;

  protected $storedSettings = [
    'customCSS',
    'customJS',
    'library_group'
  ];

  // Used in duplication of properties that can't
  // be copied
  protected $nonCopyable = [
    '_c_export',
    '_c_id',
    '_c_library_top_level',
    '_c_prefab',
  ];

  /**
   * Overwrites for correct styling
   * on reuse of doc for component shortcode
   */
  public function id() {
    if ($this->componentOutputId) {
      return $this->id . '-' . $this->componentInstanceId . '-'  .$this->componentOutputId;
    }

    return $this->id;
  }

  public function defaultSettings() {
    return [
      'customCSS'           => '',
      'customJS'            => '',
      'general_post_title'  => $this->title,
      'general_post_name'   => '',
      'library_group'       => '',
      'document_visibility' => ''
    ];
  }

  public function getDocType() {
    return 'custom:component';
  }

  public function builderInfo() {
    return [
      'settingControls' => $this->getSettingControls()
    ];
  }

  public function load($post) {
    $this->setPost($post);

    $this->_decoded = $this->decodePostContent();

    // Shortcircuit to Global Block
    if ($this->post->post_content && is_null($this->_decoded) ) {
      return self::instanceFromDocType('custom:global-block-compat')->initialize()->load( self::normalizePost( $post ) );
    }

    $this->setPostData( $this->readPostData() );

    return $this;
  }

  public function readPostData() {

    if (empty($this->_decoded)) {
      $this->_decoded = [
        'elements' => [],
        'settings' => [],
      ];
    }

    $elements = $this->migrations->migrate( $this->_decoded['elements'], true );

    $this->title = $this->post->post_title;

    $settings = array_merge( $this->_decoded['settings'], [
      'general_post_name' => $this->post->post_name,
      'general_post_title' => $this->post->post_title
    ]);

    if (isset( $settings['custom_css'] ) && ! isset( $settings['customCSS'] ) ) {
      $settings['customCSS'] = $settings['custom_css'];
      unset($settings['custom_css']);
    }

    if (isset( $settings['custom_js'] ) && ! isset( $settings['customJS'] ) ) {
      $settings['customJS'] = $settings['custom_js'];
      unset($settings['custom_js']);
    }

    return [$elements, $settings];

  }

  public function update( $update ) {

    if ( isset( $update['clone'] ) ) {
      $clone_document = self::locate( (int) $update['clone'] );

      //Global block copying
      //will return new object
      //@TODO dont do that
      if ( is_a( $clone_document, GlobalBlock::class ) ) {
        return Document::create('custom:global-block-compat')->update($update);
      }
    }

    if (isset($update['meta'])) {
      $this->meta = $update['meta'];
    }

    if ( isset( $update['settings'] ) ) {
      if ( ! current_user_can( 'unfiltered_html' ) ) {
        unset( $update['settings']['customJS'] );
        unset( $update['settings']['customCSS'] );
      }
      $this->data['settings'] = array_merge( $this->data['settings'], cs_sanitize( $update['settings'] ) );
    }

    if ( isset( $update['elements'] ) ) {
      $this->purgeElementData();
      $this->data['elements'] = cs_sanitize( $update['elements'] );
    }

    // Clone properties
    if ( isset( $clone_document ) ) {
      $clone = $clone_document->serialize();
      $this->data['settings'] = $clone['data']['settings'];
      $this->data['elements'] = $this->removeExportFromElements(
        $clone['data']['elements']
      );
    }

    if ( isset( $update['title'] ) ) {
      $title = cs_sanitize( $update['title'] );
      if ($title) {
        $this->title = $title;
        $this->data['settings']['general_post_title'] = $title;
      }
    }

    return $this;

  }
  public function createSaveUpdate($update) {
    $update['post_title'] = $this->data['settings']['general_post_title'];
    $update['post_name']  = $this->data['settings']['general_post_name'];
    unset( $this->data['settings']['general_post_title'] );
    unset( $this->data['settings']['general_post_name'] );
    return $update;
  }

  public function transformElements() {
    // Transform on one ID
    if (!empty($this->componentOutputId)) {
      return $this->transformElementsFromId($this->componentOutputId);
    }

    // Default document
    return $this->data['elements'];
  }

  private function transformElementsFromId($id) {
    $component = null;

    foreach ($this->data['elements'] as $elId => $element) {
      if (
        empty($element['_c_id'])
        || $element['_c_id'] !== $id
      ) {
        continue;
      }

      $component = $element;
      break;
    }

    // Component parameters for output
    if (!empty($this->componentOutputParameters)) {
      $component['_p_data'] = $this->componentOutputParameters;
      $component['_p_local'] = $this->componentOutputParameters;
    }

    $component['_modules'] = $this->getComponentAsTree($component, $this->data['elements']);

    $elements = [ $component ];
    $output = [
      '_type' => 'root',
      '_p_data' => $this->componentOutputParameters,
      '_modules' => [
        [
          '_type'    => 'region',
          '_region'  => 'content',
          '_modules' => $elements,
        ]
      ]
    ];

    return $output;
  }

  private function getComponentAsTree($component, $doc) {
    $modules = [];

    if (empty($component['_modules'])) {
      return $modules;
    }

    foreach ($component['_modules'] as $mod) {
      $docMod = $doc[$mod];
      $docMod['_modules'] = $this->getComponentAsTree($docMod, $doc);
      $modules[] = $docMod;
    }

    return $modules;
  }

  // Removes component export ids and _c_id values
  // so duplication works as expected
  // uses $nonCopyable
  public function removeExportFromElements( $elements ) {
    foreach ($elements as $key => &$element) {
      foreach ($this->nonCopyable as $dontCopy) {
        if (!isset($element[$dontCopy])) {
          continue;
        }

        unset($element[$dontCopy]);
      }
    }

    return $elements;
  }

  public function getScopedElementData( $id, $slotIds ) {

    $result = [];

    $isPrefab = isset( $this->data['elements'][$id] ) && isset( $this->data['elements'][$id]['_c_prefab'] ) && $this->data['elements'][$id]['_c_prefab'];

    $walk = function ($id) use (&$result, &$walk, $slotIds, $isPrefab) {
      $element = $this->data['elements'][$id];

      unset( $element['_parent'] );
      $result[$id] = $element;

      if ( isset($element['_modules']) ) {
        if ( ! $isPrefab && in_array( $id, $slotIds, true ) ) {
          $result[$id]['_modules'] = [];
        } else {
          foreach ($element['_modules'] as $child) {
            $walk($child);
          }
        }
      }

    };


    $walk($id);

    return $result;
  }

  public function getStylePriority() {
    return [5,7];
  }

  public function getInitialElements() {

    $elements = [
      'e0' => [
        '_type' => 'root',
        '_modules' => []
      ]
    ];

    $count = 0;

    $regions = ['content'];

    foreach ($regions as $region) {
      $id = 'e' . ++$count;
      $elements[$id] = [
        '_type' => 'region',
        '_region' => $region,
        '_modules' => []
      ];
      $elements['e0']['_modules'][] = $id;
    }

    return $elements;

  }

  public function getSettingControls() {
    return [
      [
        'type'  => 'group',
        'label' => __('General', 'cornerstone'),
        'controls' => [
          [
            'key' => 'general_post_title',
            'type' => 'text',
            'label' => __( 'Title', 'cornerstone' ),
          ], [
            'key' => 'general_post_name',
            'type' => 'text',
            'label' => __( 'Name', 'cornerstone' ),
          ]
        ]
      ], [
        'type'  => 'group',
        'label' => __('Element Library Exports', 'cornerstone'),
        'controls' => [
          [
            'key' => 'library_group',
            'type' => 'text',
            'label' => __( 'Group Name', 'cornerstone' ),
            'options' => [
              'placeholder' => 'Components'
            ]
          ]
        ]
      ]
    ];

  }

  public function getComponentMap() {
    $meta = cs_get_serialized_post_meta( $this->id, '_cs_component_map', true );

    if ( ! $meta) {
      $mapper = Factory::create(ComponentMap::class);
      $meta = $mapper->setElements($this->data['elements'])->run();
      cs_update_serialized_post_meta( $this->id, '_cs_component_map', $meta );
    }

    return $meta ? $meta : [];

  }


  public function is_cornerstone_content() {
    return true;
  }

  public function isLegacy() {
    return false;
  }

  public function getResponsiveText() {
    return '';
  }

  /**
   * For output of shortcode
   */
  public function setComponentOutputId($id = "") {
    $this->componentInstanceId = apply_filters("cs_component_instance_id", 1);
    $this->componentOutputId = $id;
  }

  public function setComponentOutputParameters($pData = []) {
    $this->componentOutputParameters = $pData;
  }

}
