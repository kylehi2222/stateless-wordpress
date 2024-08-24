<?php

namespace Themeco\Cornerstone\Preview;

use Themeco\Cornerstone\Plugin;
use Themeco\Cornerstone\Services\Resolver;
use Themeco\Cornerstone\Services\ThemeOptions;

class PreviewState {

  protected $prefilter_option_updates = array();
  protected $filter_cs_settings = array();
  protected $prefilter_meta_updates = array();

  protected $plugin;
  protected $resolver;
  public $state;

  public function __construct(Plugin $plugin, Resolver $resolver) {
    $this->plugin = $plugin;
    $this->resolver = $resolver;
  }

  public function init( $input, $args = [] ) {


    $defaults = [
      'docType'           => '',
      'fontData'          => [],
      'optionsData'       => [],
      'url'               => home_url(),
      'initialRender'     => false,
      'flags'             => [],
      'elements'          => [],
    ];

    $decoded = (isset( $args['decode'] ) && $args['decode']) ? base64_decode( $input ) : $input;
    $json = (isset( $args['gzip'] ) && $args['gzip']) ? gzdecode( $decoded ) : $decoded;
    $decoded = is_string($json) ? json_decode( $json, true ) : $json;


    if (is_null($decoded)) {
      $error = "Failed to decode preview state";
      trigger_error( $error, E_USER_WARNING );
      $decoded = array( 'error' => $error);
    }



    if (!empty($decoded['url'])) {
      $decoded['url'] = trailingslashit( $decoded['url'] );
    }

    $this->state = array_merge( $defaults, $decoded );


    if ( isset( $this->state ) ) {
      if (did_action('init')) {
        $this->addDocData();
      } else {
        add_action('init', [ $this, 'addDocData']);
      }

    }


    return $this;
  }

  public function addDocData() {

    $updates = array();

    if ( isset( $this->state['settings']['general_page_template'] ) ) {
      $updates['_wp_page_template'] = $this->state['settings']['general_page_template'];
    }

    add_filter( "get_post_metadata", function ( $value, $object_id, $meta_key, $single ) use ($updates){
      if ( isset($this->state['documentId']) && $object_id === (int) $this->state['documentId'] ) {
        if ( isset( $updates[$meta_key] ) ) {
          $value = $updates[$meta_key];
          if ( ! $single ) {
            $value = array( $value );
          }
        }
      }
      return $value;
    }, 10, 4 );


    add_filter( 'cs_get_serialized_post_meta', function ( $value, $post_id, $key ){
      if ( $key === '_cornerstone_settings' && isset($this->state['documentId']) && $post_id === (int) $this->state['documentId'] ) {
        if (is_array($value) && is_array($this->state['settings'])) {
          return array_merge( $value, $this->state['settings'] );
        }
      }
      return $value;
    }, 10, 3);

    add_filter( 'cs_document_load_settings', function ( $settings, $doc) {
      if (isset($this->state['documentId']) && (int) $this->state['documentId'] === (int) $doc->id()) {
        return array_merge( $settings, $this->state['settings'] );
      }
      return $settings;
    }, 10, 3 );

    $doc = $this->resolver->getDocument( $this->state['documentId'] );
    $docType = $doc->getDocType();
    $this->state['docType'] = $docType;
    $this->state['docTypeInfo'] = $this->resolver->getDocTypeInfo( $docType );
  }

  public function isDocBaseType( $base ) {
    return isset( $this->state['docType'] ) && strpos( $this->state['docType'], $base ) === 0;
  }
  public function isContent() {
    return $this->isDocBaseType( 'content' );
  }

  public function isComponent() {
    return isset( $this->state['docType'] ) && $this->state['docType'] === 'custom:component';
  }

  public function isHeader() {
    return isset( $this->state['docType'] ) && $this->state['docType'] === 'layout:header';
  }

  public function isFooter() {
    return isset( $this->state['docType'] ) && $this->state['docType'] === 'layout:footer';
  }

  public function isThemeLayout() {
    if ($this->isHeader() || $this->isFooter() ) return false;
    return $this->isDocBaseType( 'layout' );
  }

  /**
   * For usage with cs_match_*
   * in Assignments
   */
  public function getDocTypeHookWithType() {
    // See Assignments get_active_layout()
    // singles and archive have a slightly different hook format
    if ( $this->isThemeLayout() ) {
      return str_replace(":", '-', $this->state['docType']);
    }

    return $this->getDocTypeHook();
  }

  public function getDocTypeHook() {
    if ( $this->isThemeLayout() ) {
      return 'layout';
    }

    if ( $this->isHeader() ) {
      return 'header';
    }

    if ( $this->isFooter() ) {
      return 'footer';
    }

    if ( $this->isContent() ) {
      return 'content';
    }

    if ( $this->isComponent() ) {
      return 'component';
    }

    return null;

  }

  public function raw() {
    return $this->state;
  }


  public function preload() {

    $this->plugin->service('ThemeOptions')->previewPreFilter($this->state['optionsData']);

    add_filter( 'cs_preload_font_config', function( $result ) {
      if ( isset( $this->state['fontData'] ) && isset( $this->state['fontData']['config'] ) ) {
        return $this->state['fontData']['config'];
      }
      return $result;
    } );

    add_filter( 'cs_preload_font_items', function( $result ) {
      if ( isset( $this->state['fontData'] ) && isset( $this->state['fontData']['items'] ) ) {
        return $this->state['fontData']['items'];
      }
      return $result;
    } );

    add_filter( 'cs_preload_colors', function( $result ) {
      if ( isset( $this->state['colors'] ) && isset( $this->state['colors'] ) ) {
        return $this->state['colors'];
      }
      return $result;
    } );

    $hasContentSettings = $this->isContent() || $this->isComponent();

    if ( ! $hasContentSettings ) {
      return;
    }

    add_action('template_redirect', function() {
      global $post;
      if ( !$post || (int) $post->ID !== (int) $this->state['documentId'] ) {
        return;
      }

      if ( isset( $this->state['settings']['general_post_title'] ) ) {
        $post->post_title = $this->state['settings']['general_post_title'];
      }

      if ( isset( $this->state['settings']['general_allow_comments'] ) ) {
        $post->comment_status = ( $this->state['settings']['general_allow_comments'] ) ? 'open' : 'closed';
      }
    }, -1000);

  }






}
