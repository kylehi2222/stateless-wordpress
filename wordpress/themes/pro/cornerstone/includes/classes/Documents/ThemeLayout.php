<?php

namespace Themeco\Cornerstone\Documents;

class ThemeLayout extends Layout {

  protected $type = 'layout';
  protected $conditionals;

  public function defaultSettings() {
    return array (
      'customCSS' => '',
      'customJS' => '',
      'assignments' => [],
      'layout_type' => 'single',
      'assignment_priority' => 0,
      'header_enabled' => true,
      'footer_enabled' => true,
      'general_title'  => $this->title, // Will always be "Untitled" until post is loaded
    );
  }

  public function getGeneralControls() {
    return [
      [
        'key' => 'header_enabled',
        'type' => 'toggle',
        'label' => __( 'Header', 'cornerstone' ),
      ], [
        'key' => 'footer_enabled',
        'type' => 'toggle',
        'label' => __( 'Footer', 'cornerstone' ),
      ]
    ];
  }

  public function getDefaultPreviewUrl(  ) {

    $front_page = $this->conditionals->maybe_get_front_page($this->data['settings']['layout_type']);

    if ( $front_page ) {
      return get_permalink( $front_page );
    }

    if ( $this->data['settings']['layout_type'] === 'single' ) {
      $posts = get_posts( ['numberposts' => 1 ] );
      if (!empty($posts[0])) {
        return get_permalink( $posts[0]->ID );
      }
    }

    return home_url();
  }

  public function getDocType() {
    $settings = $this->settings();

    if ( ! isset( $settings['layout_type'] ) ) {
      return 'layout:unknown';
    }
    return 'layout:'. $settings['layout_type'];
  }

  public function getStylePriority() {
    return [10,70];
  }

}
