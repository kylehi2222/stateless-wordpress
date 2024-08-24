<?php

namespace Themeco\Cornerstone\Documents;

use Themeco\Cornerstone\Services\Env;
use Themeco\Cornerstone\Services\Elements;
use Themeco\Cornerstone\Services\Conditionals;
use Themeco\Cornerstone\Services\Permissions;
use Themeco\Cornerstone\Services\Wpml;
use Themeco\Cornerstone\Elements\ElementData;
use Themeco\Cornerstone\Util\Factory;

abstract class Document implements IDocumentSettings {


  protected $permissionContext;
  protected $id = null;
  protected $title;
  protected $data;
  protected $dirty;
  protected $elementData = null;
  protected $post;
  protected $env;
  protected $wpml;
  protected $permissions;
  protected $migrations;
  protected $type;
  protected $conditionals;

  public function __construct(Env $env, Permissions $permissions, Wpml $wpml, Elements $elements, Conditionals $conditionals) {
    $this->env = $env;
    $this->permissions = $permissions;
    $this->wpml = $wpml;
    $this->migrations = $elements->migrations();
    $this->conditionals = $conditionals;
  }

  // Ensure basic properties are setup
  // Will be followed by "load" to finish setup from an existing document, or by calls to set properties
  public function initialize() {
    $this->title = csi18n('common.untitled');
    $this->data = $this->setInitialData();
    return $this;
  }

  public function setPost( $post ) {
    $this->id = $post->ID;
    $this->title = $post->post_title;
    $this->post = $post;
    return $this;
  }

  public function load($post) {
    $this->setPost($post);
    $this->setPostData( $this->readPostData() );
    return $this;
  }

  public function decodePostContent() {
    return cs_maybe_json_decode( apply_filters("cs_{$this->type}_load_content", apply_filters('cs_document_load_content', $this->post->post_content, $this ) ) );
  }

  public function encodePostContent($data) {
    return apply_filters( "cs_{$this->type}_update_content", apply_filters('cs_document_update_content', wp_slash( cs_json_encode( $data ) ), $this ) );
  }

  public function setPostData( $postData ) {
    list($elements, $settings) = $postData;

    $this->data = [
      'elements' => $elements,
      'settings' => apply_filters( 'cs_' . $this->type . '_load_settings', apply_filters( 'cs_document_load_settings', array_merge( $this->data['settings'], $settings ), $this ), $this->post, $this)
    ];

  }

  public function isAllowed( $permission = '') {
    return $this->permissions->userCan( $this->permissionContext . ($permission ? '.' . $permission : ''));
  }

  public function getStylePriority() {
    return [10, 12];
  }

  public function getDefaultPreviewUrl() {
    return home_url();
  }

  public function serialize() {
    return [
      'id'          => $this->id,
      'title'       => $this->title,
      'type'        => $this->type,
      'data'        => [
        'elements' => $this->transformElements(),
        'settings' => $this->settings()
      ],
      'docTypeName' => $this->getDocType(),
      'builder'     => apply_filters( 'cs_document_builder_info', $this->populateBuilderInfo(), $this )
    ];
  }

  public function populateBuilderInfo() {
    $builderInfoBuilt = array_merge([
      'language' => $this->wpml->get_language_data_from_post( $this->post, true ),
      'modified' => date_i18n( get_option( 'date_format' ), strtotime( $this->post->post_modified ) )
    ], $this->builderInfo() );

    // Filter builder info
    $builderInfoBuilt = apply_filters('cs_document_builder_info', $builderInfoBuilt, $this);
    $builderInfoBuilt = apply_filters("cs_document_{$this->type}_builder_info", $builderInfoBuilt, $this);

    // Layout type / builder info type
    // Used by WPML
    $builderInfoType = $this->builderInfoType();

    if (!empty($builderInfoType)) {
      $builderInfoBuilt = apply_filters("cs_document_{$this->type}_{$builderInfoType}_builder_info", $builderInfoBuilt, $this);
    }

    return $builderInfoBuilt;
  }

  // Specific document type builder info
  public function builderInfoType() {
    return '';
  }

  public function transformSaveData($data) {
    return $data;
  }

  public function purgeElementData() {
    $this->elementData = null;
  }

  public function createSaveUpdate($data) {
    return $data;
  }

  public function save() {

    $update = $this->createSaveUpdate(array(
      'post_title'   => $this->title,
      'post_type'    => self::resolvePostTypeForDocType( $this->getDocType() ),
      'post_status'  => 'tco-data',
      'post_content' => $this->encodePostContent( $this->transformSaveData( $this->data ) )
    ));

    if ( is_int( $this->id ) ) {
      $update['ID'] = $this->id;
    }

    do_action( 'cs_before_save_json_content' );
    $id = is_int( $this->id ) ? wp_update_post( $update ) : wp_insert_post( $update );
    do_action( 'cs_after_save_json_content' );

    if ( 0 === $id || is_wp_error( $id ) ) {
      throw new \Exception( "Unable to update {$this->type}: $id" );
    }

    if ( ! isset( $this->id ) ) {
      $this->id = $id;
    }

    do_action("cs_save_document", $this );
    do_action("cs_save_{$this->type}", $this->id );

    // To clear up other tss it is use
    // @TODO only purge pages that HAVE this component
    if ($this->type === "component") {
      do_action("cs_purge_tmp");
    }

    delete_post_meta( (int) $id, '_cs_generated_tss');

    // load a fresh instance
    return self::locate( (int) $id )->serialize();

  }

  public function delete() {

    do_action("cs_delete_document", $this );
    do_action("cs_delete_{$this->type}", $this->id );
    do_action("cornerstone_delete_{$this->type}", $this->id ); // hook renamed. keeping for backwards compatibility

    // Run actual delete / trash
    // @see https://codex.wordpress.org/Trash_status
    if (!wp_trash_post( $this->id )) {
      throw new \Exception( __('Failed to trash', 'cornerstone') );
    }

    return ['deleted' => $this->id];

  }

  public function data() {
    return $this->data;
  }

  public function settings() {
    return $this->data['settings'];
  }

  public function type() {
    return $this->type;
  }

  /**
   * Sub type, type without the prefix (content:)
   */
  public function subType() {
    return preg_replace('/:.*/', '', $this->type);
  }

  /**
   * Publish status for content being 'publish'
   * or 'tco-data' for our custom layouts
   */
  public function getPublishStatusType() {
    return $this->subType() === 'content'
      ? 'publish'
      : 'tco-data';
  }

  public function title() {
    return $this->title;
  }

  public function id() {
    return $this->id;
  }

  public function get_id() {
    return $this->id;
  }

  public function setInitialData() {
    return [
      'elements' => $this->getInitialElements(),
      'settings' => $this->getInitialSettings()
    ];
  }

  public function getInitialElements() {
    return [];
  }

  public function getInitialSettings() {
    return apply_filters( 'cs_' . $this->type . '_default_settings', apply_filters( 'cs_document_default_settings', $this->defaultSettings(), $this ), $this );
  }

  abstract public function defaultSettings();
  abstract public function readPostData();

  abstract public function getDocType();
  abstract public function builderInfo();
  abstract public function update( $update );
  abstract public function transformElements();

  public static function create($type = '') {
    return self::instanceFromDocType( $type )->initialize();
  }

  public static function resolve( $post ) {
    return self::locate( $post );
  }

  /**
  * @return Document
  */
  public static function locate( $post ) {
    // Document from filter
    $docFromFilter = apply_filters("cs_document_locate", $post);
    if (!empty($docFromFilter) && is_a($docFromFilter, Document::class)) {
      return $docFromFilter;
    }

    $post = self::normalizePost( $post );
    if ( ! is_a( $post, 'WP_Post' ) ) return null;
    return self::instanceFromPostInfo($post)->initialize()->load( $post );
  }


  public static function instanceFromPostInfo( $post ) {

    switch ($post->post_type) {
      case 'cs_global_block':
        return self::instanceFromDocType('custom:component'); // The Component class will swap for GlobalBlock if it has the old format
      case 'cs_header':
        return self::instanceFromDocType('layout:header');
      case 'cs_footer':
        return self::instanceFromDocType('layout:footer');
      case 'cs_layout':
      case 'cs_layout_single':
      case 'cs_layout_archive':
      case 'cs_layout_single_wc':
      case 'cs_layout_archive_wc':
        return self::instanceFromDocType('layout');
    }

    return Factory::create(Content::class);

  }
  // Instantiate a Document subclass based on a type
  // Post type is used for detection from existing content
  // Named types are used when creating new documents
  public static function instanceFromDocType( $type ) {

    switch ($type) {
      case 'custom:component':
        return Factory::create(Component::class); // The Component class will swap for GlobalBlock if it has the old format
      case 'layout:header':
        return Factory::create(Header::class);
      case 'layout:footer':
        return Factory::create(Footer::class);
      case 'custom:global-block-compat':
        return Factory::create(GlobalBlock::class);  // The Component class will short circuit to this path if it detects the old format
    }


    if ( strpos( $type, 'layout') === 0 ) {
      return Factory::create(ThemeLayout::class);
    }
    return Factory::create(Content::class);
  }

  public static function normalizePost( $post ) {
    if ( is_a( $post, 'WP_Post' ) ) return $post;
    $toInt = (int) $post;
    if ($toInt > 0) return get_post($toInt);
    return null;
  }

  public function getCustomCss() {
    if ( isset( $this->data['settings']['customCSS'] ) ) {
      return $this->data['settings']['customCSS'];
    }
    return '';
  }

  public function getCustomJs() {
    if ( isset( $this->data['settings']['customJS'] ) ) {
      return $this->data['settings']['customJS'];
    }
    return '';
  }

  public function getElementData() {
    if ( is_null( $this->elementData ) ) {
      $this->elementData = Factory::create(ElementData::class)->set($this->id(),$this->transformElements());
    }
    return $this->elementData;
  }

  public function getReferencedComponents() {
    return $this->getElementData()->getReferencedComponents();
  }

  public static function resolvePostTypeForDocType( $type ) {

    switch ( $type ) {
      case 'layout:header':
        return 'cs_header';
      case 'layout:footer':
        return 'cs_footer';
      case 'layout:single':
        return 'cs_layout_single';
      case 'layout:archive':
        return 'cs_layout_archive';
      case 'layout:single-wc':
        return 'cs_layout_single_wc';
      case 'layout:archive-wc':
        return 'cs_layout_archive_wc';
      case 'custom:component':
        return 'cs_global_block';
      default:
        $parts = explode(':', $type);
        if ($parts[0] === 'content') {
          return $parts[1];
        }
        return null;
    }

  }

  /**
   * Clone documents main data and setting
   */
  public function cloneDoc() {
    return [$this->data['elements'], $this->data['settings']];
  }

  /**
   * Save thumbnail if supported by post type
   */
  protected function saveThumbnail($post, $settings) {
    // Update featued image / Thumbnail
    if (
      !empty($post)
      && post_type_supports($post->post_type, 'thumbnail')
      && isset($settings['general_featured_image'])
    ) {
      $image = $settings['general_featured_image'];

      // If empty needs to call separate function
      if (empty($image)) {
        delete_post_thumbnail($this->id);
      } else {
        // Set post thumbnail
        set_post_thumbnail(
          $this->id,
          (int)$image
        );
      }
    }
  }

  /**
   * Grab and set thumbnail to general_featured_image
   */
  protected function grabThumbnail($post, $settings) {
    // Grab thumbnail ID
    if (post_type_supports($post->post_type, 'thumbnail')) {
      $settings['general_featured_image'] = get_post_thumbnail_id($this->post->ID);

      // If valid add in :full
      if (!empty($settings['general_featured_image'])) {
        $settings['general_featured_image'] .= ':full';
      } else {
        // Cant use 0 which is the empty value
        $settings['general_featured_image'] = '';
      }
    }

    return $settings;
  }

}
