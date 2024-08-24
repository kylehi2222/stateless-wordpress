<?php

namespace Themeco\Cornerstone\Services;

use Themeco\Cornerstone\Documents\Document;

class Assignments implements Service {

  protected $active_content = null;
  protected $active_header = null;
  protected $active_footer = null;
  protected $active_layout = null;

  public function setup() {
    $this->clear_cached_assignments();
    add_action( 'cs_save_document', [ $this, 'clear_cache_on_save' ] );
    add_action( 'cs_delete_document', [ $this, 'clear_cache_on_save'  ] );
    add_action( 'cs_assign_active_content', [ $this, 'setActiveContentAction' ], 0, 1 );
  }

  /**
   * Set active content action
   * for plugins see custom 404
   */
  public function setActiveContentAction($id) {
    $doc = Document::locate($id);
    if (empty($doc)) {
      return;
    }

    $this->setActiveContent($doc);
  }

  public function setActiveContent($doc) {
    $this->active_content = $doc;
  }

  public function getActiveContent() {
    return $this->active_content;
  }

  public function get_rules( $type ) {

    $posts = cornerstone( 'Locator' )->find_posts( [
      'post_types'   => Document::resolvePostTypeForDocType( $type ),
      'post_status' => 'tco-data'
    ] );


    $sets = [];

    foreach ($posts as $post ) {

      $document = cornerstone('Resolver')->getDocument( $post );
      if ($document && $document->getDocType() === $type ) {
        $settings = $document->settings();

        if (!empty($settings['assignments']) ) {
          $sets[] = [
            'id' => $document->id(),
            'title' => $document->title(),
            'rules' => $settings['assignments'],
            'priority' => (int) $settings['assignment_priority']
          ];
        }
      }

    }

    usort( $sets, [ $this, 'sort_rules' ]);

    return $sets;
  }

  public function sort_rules( $a, $b ) {
    $a_priority = $a['priority'];
    $b_priority = $b['priority'];

    if ($a_priority == $b_priority) {
      // Arrange by id if titles equal
      if ($a['title'] === $b['title']) {
        return ($a['id'] > $b['id']) ? -1 : 1;
      }

      // Sort by title if applicable
      return ($a['title'] < $b['title'])
        ? -1
        : 1;
    }

    // Normal Priority arrangement
    return ($a_priority < $b_priority) ? -1 : 1;
  }

  public function getDocLayoutSetting( $type ) {
    $settings = $this->active_content->settings();

    if ( $type === 'layout:single') {
      return $settings['layoutSingle'];
    }

    if ( $type === 'layout:header') {
      return $settings['layoutHeader'];
    }

    if ( $type === 'layout:footer') {
      return $settings['layoutFooter'];
    }

    return null;
  }

  public function getFirstMatch($type) {

    if ($this->active_content) {
      $explicit = $this->getDocLayoutSetting($type);
      if ( !empty( $explicit ) && $explicit !== 'default' ) {
        return $explicit;
      }
    }

    $rules = $this->getCachedRules( $type );
    $matcher = cornerstone('RuleMatching');

    foreach ($rules as $set) {
      if ($matcher->match( $set['rules'] ) ) {
        return $set['id'];
      }
    }
    return null;
  }

  public function getOptionKey( $type ) {
    return str_replace(":", '_', "cs_assignment_cache_$type");
  }

  public function clear_cached_assignments() {
    delete_option( $this->getOptionKey( 'layout:header' ) );
    delete_option( $this->getOptionKey( 'layout:footer' ) );
    delete_option( $this->getOptionKey( 'layout:single' ) );
    delete_option( $this->getOptionKey( 'layout:archive' ) );
    delete_option( $this->getOptionKey( 'layout:single-wc' ) );
    delete_option( $this->getOptionKey( 'layout:archive-wc' ) );
  }

  public function clear_cache_on_save( $doc ) {
    delete_option( 'x_cache_google_fonts_request' );
    if ( strpos( $doc->type(), 'layout' ) === 0 ) {
      $this->clear_cached_assignments();
    }
  }

  public function getCachedRules( $type ) {
    $option_key = $this->getOptionKey( $type );
    $rules = get_option($option_key);
    if (false === $rules) {
      $rules = $this->get_rules( $type );
      add_option($option_key, $rules); // add_option enables use of autoload which is desirable here
    }
    return $rules;
  }

  public function get_active_header() {

    try {

      $assignment = $this->getFirstMatch( 'layout:header' );

      if ( $assignment === 'none') {
        return null;
      }

      if ( is_null( $assignment ) ) {
        // This filter is useful to provide a fallback for when no conditions match
        $assignment = apply_filters( 'cs_locate_header_assignment', null, null, null ); // params deprecated
      }

      // This filter can be used to force an assignment regardless of what was previously detected
      $assignment = apply_filters( 'cs_match_header_assignment', $assignment, null, null ); // params deprecated

      if ( ! is_null( $assignment ) ) {
        $assigned_document = $this->getAssignedDocument( 'layout:header', (int) $assignment);
        if ($assigned_document) {
          $this->active_header = $assigned_document;
          return $this->active_header;
        }
      }

    } catch( \Exception $e ) {
      trigger_error('Unable to load assigned header ' . $e->getMessage(), E_USER_WARNING);
    }

    return null;
  }

  public function get_active_footer() {

    try {

      $assignment = $this->getFirstMatch( 'layout:footer' );

      if ( $assignment === 'none') {
        return null;
      }

      if ( is_null( $assignment ) ) {
        // This filter is useful to provide a fallback for when no conditions match
        $assignment = apply_filters( 'cs_locate_footer_assignment', null, null, null ); // params deprecated
      }

      // This filter can be used to force an assignment regardless of what was previously detected
      $assignment = apply_filters( 'cs_match_footer_assignment', $assignment, null, null ); // params deprecated

      if ( ! is_null( $assignment ) ) {
        $assigned_document = $this->getAssignedDocument( 'layout:footer', (int) $assignment);
        if ($assigned_document) {
          $this->active_footer = $assigned_document;
          return $this->active_footer;
        }
      }

    } catch( \Exception $e ) {
      trigger_error('Unable to load assigned footer. ' . $e->getMessage(), E_USER_WARNING);
    }

    return null;
  }

  public function detect_theme_layout_type() {

    if (is_singular() || is_404() ) {
      return 'layout:single';
    }

    return 'layout:archive';

  }

  public function getAssignedDocument( $type, $assignment ) {
    // Filters to reassign doc id
    list($type, $assignment) = apply_filters("cs_detect_assigned_document", [$type, $assignment]);

    $doc = cornerstone('Resolver')->getDocument($assignment);

    return $doc && $doc->getDocType() === $type ? $doc : null;
  }

  public function get_active_layout() {

    try {

      $layout_type = apply_filters( 'cs_detect_layout_type', $this->detect_theme_layout_type() );
      $assignment = $this->getFirstMatch( $layout_type );

      // Hook for replacing if hook found
      // See PreviewState::getDocTypeHookWithType
      $hook_type = str_replace(":", '-', $layout_type );

      if (is_null( $assignment ) ) {
        // This filter is useful to provide a fallback for when no conditions match
        $assignment = apply_filters( 'cs_locate_' . $hook_type . '_assignment', null );
      }

      // This filter can be used to force an assignment regardless of what was previously detected
      $assignment = apply_filters( 'cs_match_' . $hook_type . '_assignment', $assignment );

      if ( ! is_null( $assignment ) ) {
        $assigned_document = $this->getAssignedDocument( $layout_type, (int) $assignment);
        if ($assigned_document) {
          $this->active_layout = $assigned_document;
          return $this->active_layout;
        }
      }

    } catch( \Exception $e ) {
      trigger_error('Unable to load assigned layout. ' . $e->getMessage(), E_USER_WARNING);
    }

    return null;
  }

  public function get_last_active_header() {
    return isset( $this->active_header ) ? $this->active_header : null;
  }

  public function get_last_active_footer() {
    return isset( $this->active_footer ) ? $this->active_footer : null;
  }

  public function get_last_active_layout() {
    return isset( $this->active_layout ) ? $this->active_layout : null;
  }
}
