<?php

namespace Themeco\Cornerstone\Documents;

class GlobalBlock extends Content {

  protected $type = 'component';
  protected $permissionContext = 'component';

  protected $storedSettings = [
    'customCSS',
    'customJS',
  ];


  public function getDocType() {
    return 'custom:component';
  }

  public function isAllowed( $permission = '') {
    return $this->permissions->userCan( $this->permissionContext . ($permission ? '.' . $permission : ''));
  }

  public function defaultSettings() {
    return [
      'customCSS'         => '',
      'customJS'          => '',
      'general_post_title' => $this->title,
      'general_post_name'  => ''
    ];
  }

  public function builderInfo() {
    return [
      'settingControls' => $this->getSettingControls()
    ];
  }

  public function update( $update ) {

    if ( isset( $update['elements'] ) && isset( $update['elements']['e0'] )) { // elements are in "doc" format
      return Document::create('custom:component')->setPost($this->post)->update( $update );
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
      $this->data['elements'] = [ 'data' => cs_sanitize( $update['elements'] ) ];
    }

    if ( isset( $update['clone'] ) ) {
      list($elements, $settings) = self::locate( (int) $update['clone'] )->cloneDoc();
      $this->data['settings'] = $settings;
      $this->data['elements'] = [
        'data' => $elements
      ];
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

  public function readPostData() {
    $elements = $this->loadElementContent();

    // $post_type_obj = get_post_type_object( $this->post->post_type );

    $settings = $this->loadSettingsContent();

    $this->title = $this->post->post_title;
    $settings['general_post_name'] = $this->post->post_name;
    $settings['general_post_title'] = $this->post->post_title;

    return [$elements, $settings];
  }

  public function getStylePriority() {
    return [50, 110];
  }

  public function applyPostTypeSettings( $update ) {

    $update['post_type']   = 'cs_global_block';
    $update['post_status'] = 'tco-data';

    return $update;
  }

  public function builderSettingsControls($post) {

    $controls = [];

    //
    // General
    //


    $controls[] = array(
      'type'  => 'group',
      'label' => __('General', 'cornerstone'),
      'controls' => [
        array(
          'key' => 'general_post_title',
          'type' => 'text',
          'label' => __( 'Title', 'cornerstone' ),
        ),
        array(
          'key' => 'general_post_name',
          'type' => 'text',
          'label' => __( 'Name', 'cornerstone' ),
        )
      ]
    );


    $controls = apply_filters('cs_builder_settings_controls', $controls, $post, $this );

    return $controls;
  }
}
