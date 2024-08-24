<?php

namespace Themeco\Cornerstone\Documents;

abstract class Layout extends Document {

  protected $permissionContext = 'layout';
  protected $type = 'layout';

  public function defaultSettings() {
    return $this->defaultSettingsLayout();
  }
  public function defaultSettingsLayout() {
    return [
      'customCSS'           => '',
      'customJS'            => '',
      'assignments'         => [],
      'layout_type'         => '',
      'assignment_priority' => 0,
      'general_title'       => $this->title, // Will always be "Untitled" until post is loaded
      'general_featured_image' => '',
    ];
  }

  public function builderInfo() {
    return [
      'settingControls'  => $this->getSettingsControls()
    ];
  }

  // Send layout type for builderInfoType
  public function builderInfoType() {
    $settings = $this->settings();

    return cs_get_array_value($settings, 'layout_type', '');
  }

  public function readPostData() {

    $loaded = $this->decodePostContent();
    $regions = isset($loaded['regions']) && is_array($loaded['regions']) ? $loaded['regions'] : [];

    $elements = [];
    foreach ( $regions as $region => $region_elements ) {
      $elements[$region] = $this->migrations->migrate( $region_elements );
    }

    $settings = isset($loaded['settings']) && is_array($loaded['settings']) ? $loaded['settings'] : [];
    $settings['general_title'] = $this->title;

    // Setup and grab thumbnail
    $settings = $this->grabThumbnail($this->post, $settings);

    // Fix for improper settings data
    if (
      $this->post->post_type === "cs_layout_archive"
      && empty($settings['layout_type'])
    ) {
      $settings['layout_type'] = 'archive';
    }

    return [$elements, $settings];

  }

  public function update( $update ) {

    if ( isset( $update['settings'] ) ) {
      if ( ! current_user_can( 'unfiltered_html' ) ) {
        unset( $update['settings']['customJS'] );
        unset( $update['settings']['customCSS'] );
      }
      $this->data['settings'] = array_merge( $this->data['settings'], cs_sanitize( $update['settings'] ) );
      if ( ! isset( $update['title'] ) && ! empty( $this->data['settings']['general_title'] ) ) {
        $update['title'] = $this->data['settings']['general_title'];
      }
    }

    if ( isset( $update['elements'] ) ) {
      $this->purgeElementData();
      $this->data['elements'] = cs_sanitize( $update['elements'] );
    }

    if ( isset( $update['clone'] ) ) {
      list($elements, $settings) = self::locate( (int) $update['clone'] )->cloneDoc();
      $this->data['elements'] = $elements;
      $this->data['settings'] = $settings;
      unset($this->data['settings']['assignments']);
      unset($this->data['settings']['assignment_priority']);
    }

    if ( isset( $update['title'] ) ) {
      $title = cs_sanitize( $update['title'] );
      if ($title) {
        $this->title = $title;
        $this->data['settings']['general_title'] = $title;
      }
    }

    // Save thumbnail db
    $this->saveThumbnail($this->post, $this->data['settings']);

    return $this;
  }

  public function transformSaveData( $data ) {

    $storedSettings = $this->defaultSettings();
    unset($storedSettings['general_title']);
    $storedSettingsKeys = array_keys($storedSettings);

    $settings_update = [];
    foreach ( $storedSettingsKeys as $storedKey ) {
      if (isset($data['settings'][$storedKey])) {
        $settings_update[$storedKey] = $data['settings'][$storedKey];
      }
    }

    $transformed = [
      'settings' => $settings_update,
      'regions'  => $data['elements']
    ];

    return $transformed;
  }

  public function getSettingsControls() {
    $general_controls = $this->getBaseSettingsControls();

    // Thumbnail / featured image
    if (post_type_supports($this->post->post_type, 'thumbnail')) {
      $general_controls[0]['controls'][] = [
        'key' => 'general_featured_image',
        'type' => 'image',
        'label' => __( 'Featured Image', 'cornerstone' ),
      ];
    }

    return $general_controls;
  }

  public function getGeneralControls() {
    return array();
  }

  public function getBaseSettingsControls() {

    return array(
      array(
        'type'  => 'group',
        'label' => __('General', 'cornerstone'),
        'controls' => array_merge(array(
          array(
            'key' => 'general_title',
            'type' => 'text',
            'label' => __( 'Title', 'cornerstone' ),
          ) ),
          $this->getGeneralControls()
        )
      ),
      array(
        'type'  => 'group',
        'label' => __('Assignment', 'cornerstone'),
        'description' => sprintf( __('Set the conditions for when this Layout will be displayed. If there are multiple matches the one with the lowest priority will be used.', 'cornerstone') ),
        'controls' => array(
          array(
            'type' => 'assignments',
            'key'  => 'assignments',
            'label' => __('Conditions', 'cornerstone'),
          ),
          array(
            'type' => 'number',
            'key'  => 'assignment_priority',
            'label' => __('Priority', 'cornerstone')
          )
        )
      )
    );
  }

  public function getRegions() {
    return ['layout'];
  }

  public function transformElements() {

    $requiredRegions = apply_filters('cs_layout_type_required_regions', $this->getRegions(), $this);
    $regionData = isset( $this->data['elements'] ) ? $this->data['elements'] : [];
    $elements = [];

    foreach ($requiredRegions as $region) {
      $elements[] = [
        '_type' => 'region',
        '_region' => $region,
        '_modules' => isset( $regionData[$region] ) ? $regionData[$region] : []
      ];
    }

    return [
      '_type' => 'root',
      '_modules' => $elements
    ];

  }

}
