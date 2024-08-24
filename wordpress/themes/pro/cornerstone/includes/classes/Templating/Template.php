<?php

namespace Themeco\Cornerstone\Templating;

/**
 * _cs_template_type, _cs_template_subtype_* meta keys are no longer used
 */
class Template {

  protected $id = null;
  protected $title;
  protected $type; // element | document
  protected $subType;
  protected $readOnly = false;
  protected $preview = '';
  protected $meta = null;
  protected $thumbnail;

  public function __construct( $type, $subType, $readOnly = false ) {
    $this->type = $type;
    $this->subType = $subType;
    $this->readOnly = $readOnly;
  }

  public function initialize() {
    $this->title = csi18n('common.untitled');
    return $this;
  }

  protected function load( $post ) {

    if ( 'cs_user_templates' === $post->post_type ) {
      $this->id = $post->ID;
      $type = get_post_meta( $post->ID, 'cs_template_type', true );
      $format = ( 'block' !== $type ) ? '%s (Page)' : '%s (Block)';
      $this->title = sprintf( $format, get_post_meta( $post->ID, 'cs_template_title', true ) );
      $this->preview = '';

      $this->meta = $this->migrateMeta( [
        'elements' => get_post_meta( $post->ID, 'cs_template_elements', true )
      ] );
      return $this;
    }

    if ( 'cs_template' !== $post->post_type ) {
      throw new \Exception( 'Attempted to load template from incorrect post_type | ' . $post->ID );
    }

    $this->id = $post->ID;
    $this->title = $post->post_title ? $post->post_title : '';

    $content = cs_maybe_json_decode( $post->post_content );

    if ( is_array( $content ) ) {
      $this->preview = isset( $content['preview'] ) ? $content['preview'] : '';
    }

    return $this;

  }

  public function migrateMeta( $meta ) {
    $migrations = cornerstone('Elements')->migrations();

    if ( isset( $meta['regions'] ) && ! isset( $meta['elements'] ) ) {
      $meta['elements'] = $meta['regions'];
      unset($meta['regions']);
    }

    if ( isset( $meta['atts'] ) ) { // preset
      $elements = $migrations->migrate( [$meta['atts']] );
      $meta['atts'] = $elements[0];
    }

    if ( isset( $meta['elements'] ) ) { // documents / section templates
      if ( strpos( $this->subType, 'layout' ) === 0 ) {
        foreach ( $meta['elements'] as $region => $elements) {
          $meta['elements'][$region] = $migrations->migrate( $elements );
        }
      } else {
        $meta['elements'] = $migrations->migrate( $meta['elements'] );
      }

    }

    return $meta;
  }

  public function save() {

    if ( $this->readOnly ) {
      throw new \Exception('Unable to delete readonly template');
    }

    $identifier = $this->type . '|' . $this->subType;

    // Thumbnail sent from a document type
    // that is being imported as template
    // or normal 'preview' field
    $preview = isset($this->thumbnail)
      ? cs_resolve_image_source($this->thumbnail)
      : sanitize_text_field( $this->preview );

    $args = array(
      'post_title'   => sanitize_text_field( $this->title ),
      'post_type'    => 'cs_template',
      'post_status'  => 'tco-data',
      'post_content' => wp_slash( cs_json_encode( array(
        'identifier' => $identifier,
        'preview'    => $preview,
      ) ) )
    );

    if ( is_int( $this->id ) ) {
      $args['ID'] = $this->id;
    }

    do_action( 'cs_before_save_json_content' );
    $id = wp_insert_post( $args );
    do_action( 'cs_after_save_json_content' );

    if ( ! $id || is_wp_error( $id ) ) {
      throw new \Exception( "Unable to update template: $id" );
    }

    // Index template type
    update_post_meta( $id, '_cs_template_identifier', $identifier );

    if ( ! is_null( $this->meta ) && ! empty( $this->meta ) ) {
      cs_update_serialized_post_meta( $id, '_cs_template_data', $this->meta );
    }

    return $this->serialize();

  }

  public function get_id() {
    return $this->id;
  }

  public function loadMeta() {
    if ( is_null( $this->meta ) && ! $this->readOnly ) {
      $this->meta = $this->migrateMeta( cs_get_serialized_post_meta( $this->id, '_cs_template_data', true ) );
    }
    return $this->meta;
  }

  public function serialize( $withPreview = true ) {
    return [
      'id' => $this->id,
      'title' => $this->title,
      'type' => $this->type,
      'subType' => $this->subType,
      'preview' => $withPreview ? $this->preview : null,
      'meta' => $this->meta
    ];
  }

  public function serializeFull() {
    $this->loadMeta();
    return $this->serialize();
  }

  public function setTitle( $title ) {
    return $this->title = $title;
  }

  public function setPreview( $preview ) {
    return $this->preview = $preview;
  }

  public function setMeta( $meta, $migrate = true ) {
    return $this->meta = $migrate ? $this->migrateMeta( $meta ) : $meta;
  }

  public function setThumbnail($thumbnail) {
    $this->thumbnail = $thumbnail;
  }

  public function delete() {
    if ( $this->readOnly ) {
      throw new \Exception('Unable to delete readonly template');
    }
    do_action('cornerstone_delete_template', $this->id, $this->type );
    return wp_delete_post( $this->id, true );

  }

  public static function normalizePost( $post ) {
    if ( is_a( $post, 'WP_Post' ) ) return $post;
    $toInt = (int) $post;
    if ($toInt > 0) return get_post($toInt);
    return null;
  }

  public static function create( $type, $subType, $readOnly = false ) {
    $instance = self::instanceFromTemplateType( $type, $subType, $readOnly );
    return $instance->initialize();
  }

  public static function migrateInstanceFromTemplateType($type, $subType, $post) {

    $permissions = cornerstone('Permissions');

    if ( $permissions->userCan('template.manage_library') ) {
      $toMigrate = self::instanceFromTemplateType($type, $subType);
      if (!empty($toMigrate)) {
        $toMigrate->initialize()->load( $post );
        $toMigrate->save();
      }
    }

    return self::instanceFromTemplateType($type, $subType);
  }

  public static function instanceFromTemplateType($type, $subType, $readOnly = false) {

    if ($type === 'element') {
      return new self( $type, $subType, $readOnly );
    }

    if ( $type === 'document') {
      $allowed = cornerstone('Resolver')->getAllowedDocTypes();

      if ( in_array( $subType, $allowed ) ) {
        return new self( $type, $subType, $readOnly );
      }

    }


    return null;

  }


  public static function parseIdentifier( $identifier ) {
    $parts = explode('|',$identifier);
    return [$parts[0], $parts[1]];
  }

  public static function instanceFromPostInfo( $post ) {

    if ( $post->post_type === 'cs_user_templates') {
      return self::migrateInstanceFromTemplateType('element','__multi__', $post);
    }

    $content = cs_maybe_json_decode( $post->post_content );

    if ( is_array( $content ) ) {

      if ( isset( $content[ 'identifier' ] ) ) {
        list($base,$sub) = self::parseIdentifier( $content[ 'identifier' ] );
        return self::instanceFromTemplateType($base, $sub );
      }

      if ( isset( $content['type'] ) ) {
        switch ( $content['type'] ) {
          case 'preset':
            if ( isset( $content['subtype'] ) ) {
              return self::migrateInstanceFromTemplateType('element', $content['subtype'], $post);
            }
            break;
          case 'content':
            return self::migrateInstanceFromTemplateType('element','__multi__', $post);
          case 'header':
            return self::migrateInstanceFromTemplateType('document','layout:header', $post);
          case 'footer':
            return self::migrateInstanceFromTemplateType('document','layout:footer', $post);
          case 'layout':
            $meta = cs_get_serialized_post_meta( $post->ID, '_cs_template_data', true );
            if ( isset( $meta['settings'] ) && isset( $meta['settings']['layout_type'] ) ) {
              return self::migrateInstanceFromTemplateType('document', 'layout:' . $meta['settings']['layout_type'], $post);
            }
            break;
        }
      }
    }

    return null;
  }

  public static function locate( $post ) {

    if ( ! cornerstone('Permissions')->userCan('template') ) {
      return null;
    }

    $post = self::normalizePost( $post );

    if ( ! is_a( $post, 'WP_Post' ) ) return null;
    $instance = self::instanceFromPostInfo($post);

    if ($instance) {
      return $instance->initialize()->load( $post );
    }
    return null;
  }

}
