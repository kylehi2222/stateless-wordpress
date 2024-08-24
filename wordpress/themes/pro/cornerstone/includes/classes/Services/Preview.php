<?php

namespace Themeco\Cornerstone\Services;

use Exception;
use Themeco\Cornerstone\Plugin;
use Themeco\Cornerstone\Util\Endpoint;
use Themeco\Cornerstone\Util\CssAsset;
use Themeco\Cornerstone\Preview\Renderer;
use Themeco\Cornerstone\Preview\PreviewState;

class Preview implements Service {

  protected $state = null;
  protected $zones = array();
  protected $frame = null;
  protected $timestamp = null;
  protected $overlays = array();

  protected $plugin;
  protected $app;
  protected $preferences;
  protected $cssEndpoint;
  protected $markupEndpoint;
  protected $renderer;
  protected $previewState;
  protected $permissions;
  protected $tss;
  protected $http;
  protected $cssAsset;
  protected $styling;
  protected $resolver;
  protected $queried_object;
  protected $initial_render_content;

  public function __construct(Plugin $plugin, App $app, Preferences $preferences, Endpoint $cssEndpoint, Endpoint $markupEndpoint, Renderer $renderer, PreviewState $previewState, Permissions $permissions, Tss $tss, Http $http, CssAsset $cssAsset) {
    $this->plugin = $plugin;
    $this->app = $app;
    $this->preferences = $preferences;
    $this->cssEndpoint = $cssEndpoint;
    $this->markupEndpoint = $markupEndpoint;
    $this->renderer = $renderer;
    $this->previewState = $previewState;
    $this->permissions = $permissions;
    $this->tss = $tss;
    $this->http = $http;
    $this->cssAsset = $cssAsset;
  }

  public function setup() {

    if (defined('CS_APP_DEV_TOOLS') && CS_APP_DEV_TOOLS && isset( $_REQUEST['cs-render-test'])) {
      $test_state = \apply_filters('_cs_test_preview_state', false);
      if ($test_state) {
        $_POST['cs_preview_state'] = $test_state;
        $_POST['cs_preview_time'] = 1;
        $_POST['_cs_nonce'] = $this->http->createNonce();
      }
    }

    add_action('init', [ $this, 'maybe_start'], 100);
  }

  public function maybe_start() {
    $this->cssEndpoint->config( [
      'requestKey' => 'cs-css',
      'handler'    => [ $this, 'handleCss' ]
    ])->start();

    $this->markupEndpoint->config( [
      'requestKey' => 'cs-render',
      'handler'    => [ $this, 'handleMarkup' ]
    ])->start();

    if ( ! \is_user_logged_in() || ! isset( $_POST['cs_preview_state'] ) || ! $_POST['cs_preview_state'] || ! isset( $_POST['cs_preview_time'] )) {
      return;
    }

    // Nonce verification
    if ( ! isset( $_POST['_cs_nonce'] ) || ! \wp_verify_nonce( $_POST['_cs_nonce'], 'cornerstone_nonce' ) ) {
      echo -1;
      die();
    }

    // Start rendering
    $this->styling = $this->plugin->service('Styling');
    $this->resolver = $this->plugin->service('Resolver');

    // Excerpt post type support, causes issues with Components initial load
    $this->plugin->service("FrontEnd")->untrack_excerpt();

    $this->timestamp = $_POST['cs_preview_time'];

    $this->state = $this->previewState->init($_POST['cs_preview_state'], [
      'decode' => true,
      'gzip'   => ( isset( $_POST['cs_preview_gzip'] ) && $_POST['cs_preview_gzip'] === 'gzip' )
    ])->raw();

    $this->previewState->preload();

    add_filter( 'pre_handle_404', '__return_true' );

    do_action('cs_before_preview_frame', $this->state);

    // Filter preview frame state
    // See Wpml::before_preview_frame_filter
    $this->state = apply_filters('cs_before_preview_frame_filter', $this->state);


    $typeHook = $this->previewState->getDocTypeHook();
    if ( $typeHook ) {
      do_action('cs_before_preview_frame_' . $typeHook );
    }


    add_filter( 'show_admin_bar', '__return_false' );
    add_action( 'template_redirect', array( $this, 'load' ), 0 );
    add_action( 'template_redirect', [$this, 'setup_content_or_component'], 9999999 );
    add_action( 'cs_late_template_redirect', array( $this, 'load_late' ), 10000 );
    add_action( 'shutdown', array( $this, 'frame_signature' ), 1000 );
    add_filter( 'wp_die_handler', array( $this, 'remove_preview_signature' ) );



    add_filter('cs_register_document_styles', function($register, $document ) {
      $id = $document->id();
      if (isset($this->state['documentId']) && (int) $id === (int) $this->state['documentId']) {
        $priority = $document->getStylePriority()[0];
        $this->styling->addStyles( "$id-generated", '', $priority);
        $this->styling->addStyles( "$id-element-css", '', $priority + 1);
        return false;
      }
      return $register;
    }, 10, 3 );

  }

  public function handleMarkup( $data ) {
    return $this->renderer->render( $data );
  }

  public function handleCss( $data ) {

    if ( isset( $data['type']) && $data['type'] === 'generate-theme-options') {
      $this->previewState->init($data['previewState'])->preload();
      return $this->tss->generateGlobalCss();
    }

    if ( isset( $data['type']) && $data['type'] === 'post-process-css') {
      $this->previewState->init($data['previewState'])->preload();
      return array_map(function($item) {
        return cs_dynamic_content( $item['css'] );
      }, $data['items']);
    }


    return '';
  }

  public function setup_content_or_component() {

    if ( ! isset( $this->state['documentId'] ) ) {
      return;
    }

    if ( !empty( $this->state['docTypeInfo']) && !empty( $this->state['docTypeInfo']['regions']) && in_array( 'content', $this->state['docTypeInfo']['regions'], true ) ) {
      add_filter(
        'the_content',
        [ $this, 'output_content_zone' ],
        apply_filters('cs_preview_output_zone_priority', -9999999)
      );
    }

    $this->resolver->loadDocument( $this->state['documentId'] );

    if ($this->previewState->isComponent()) {

      remove_all_filters('template_include');
      remove_action( 'x_after_site_end', 'x_legacy_header_widget_areas' );
      remove_action( 'x_after_site_end', 'x_scroll_top_anchor' );
      add_action('cs_output_header', '__return_false' );
      add_action('cs_output_footer', '__return_false' );
      add_filter('template_include', array( cornerstone('FrontEnd'), 'setup_after_template_include' ), 99998 );
      add_filter('template_include', function() {
        return $this->plugin->path .'/includes/views/app/preview-components.php';
      } );

      add_action( 'wp_enqueue_scripts', function() {
        $css = '.cs-component-builder { font-size: ' . get_option( 'x_content_font_size_rem', '1' ) . 'rem; }';
        $this->styling->addStyles( 'component-preview', $css, 1000);
      });

      add_filter('builder_class', function() {
        return 'cs-content cs-component-builder x-global-block x-global-block-' . $this->state['documentId'];
      }, 11 );

    }



  }

  public function load() {

    nocache_headers();
    $this->queried_object = $this->detect_queried_object();

    add_action( 'wp_footer', [ $this, 'output_initial_render'], 2000 );

    $regions = ! empty( $this->state['docTypeInfo'] ) && ! empty( $this->state['docTypeInfo']['regions'] ) ?  $this->state['docTypeInfo']['regions'] : [];

    $zones = apply_filters('cs_preview_zones', ['x_before_site_end', 'x_after_site_end', 'cs_deferred' ] );

    if ( in_array( 'layout', $regions, true ) ) {
      $zones[] = 'cs_layout';
    }

    if ( in_array( 'footer', $regions, true ) ) {
      $zones[] = 'cs_colophon';
    }

    if ( in_array( 'top', $regions, true ) ) {
      $zones[] = 'cs_masthead';
    }

    if ( in_array( 'left', $regions, true ) || in_array( 'right', $regions, true ) ) {
      $zones[] = 'x_before_site_begin';
    }

    foreach ( $zones as $zone ) {
      add_action( $zone, array( $this, 'zone_output' ) );
    }

    add_filter( 'body_class', array( $this, 'body_class' ) );

    if ($this->preferences->get_preference('react_dev_tools')) {
      add_action( 'wp_head', array( $this, 'react_dev_tools' ), 0 );
    }

    $this->frame = null;

    // Force the current document to resolve

    if ( $this->previewState->isContent() || $this->previewState->isComponent() ) {
      add_action( 'template_redirect', [$this, 'setup_content_or_component'], 9999999 );
    } else {
      $hook_type = $this->previewState->getDocTypeHookWithType();

      // Make sure this layout gets assigned for Assignments.php
      $this->forceSetWPQuery($hook_type);

      add_filter('cs_match_' . $hook_type . '_assignment', function() {
        return $this->state['documentId'];
      } );
    }


    // if ( isset( $state['custom_js'] ) ) {
    //   foreach ($state['custom_js'] as $id => $content) {
    //     if ( $content ) {
    //       $this->plugin->service('EnqueueScripts')->addScriptSafely($id, $content);
    //     }
    //   }
    // }

    add_action( 'wp_enqueue_scripts', array( $this, 'enqueue' ) );
    add_filter( 'post_class', array( $this, 'observe_post_classes' ), 10, 3 );
    add_action( 'wp_footer', array( $this, 'output_observed_overlays' ), 10000 );
    do_action( 'cs_preview_frame_load' );
  }

  public function load_late() {

    if ( $this->permissions->userCan( 'layout') ) {
      add_filter( 'x_masthead_atts',       array( $this, 'nav_overlay_header' ) );
      add_filter( 'x_colophon_atts',       array( $this, 'nav_overlay_footer' ) );
      add_filter( 'cs_masthead_atts',      array( $this, 'nav_overlay_header' ) );
      add_filter( 'cs_colophon_atts',      array( $this, 'nav_overlay_footer' ) );
    }
    add_filter( 'cs_content_atts',      array( $this, 'nav_overlay_content' ), 10, 3 );
  }

  public function zone_output() {
    echo '<div data-cs-zone="' . current_action() . '"></div>';
  }

  public function get_state() {
    return $this->state;
  }

  // available as window.csAppData.preview
  public function data() {
    return apply_filters( 'cs_preview_frame_config', array_merge($this->state, [
      'timestamp'              => $this->timestamp,
      'queriedObject'          => $this->queried_object
    ] ) );
  }

  public function detect_queried_object() {

    $object = get_queried_object();

    if ( is_a( $object, 'WP_Term' ) ) {
      return [
        'type'       => 'term',
        'termId'     => (int) $object->term_id,
        'taxonomyId' => (int) $object->term_taxonomy_id
      ];
    }

    if ( is_a( $object, 'WP_Post' ) ) {
      return [
        'type'   => 'post',
        'postId' => (int) $object->ID
      ];
    }

    if ( is_a( $object, 'WP_Post_Type' ) ) {
      return [
        'type' => 'postType',
        'name' => $object->name
      ];
    }

    if ( is_a( $object, 'WP_User' ) ) {
      return [
        'type' => 'user',
        'id' => (int) $object->ID
      ];
    }

    return null;

  }

  public function frame_signature() {
    echo 'CORNERSTONE_FRAME';
  }

  public function remove_preview_signature( $return = null ) {
    remove_action( 'shutdown', array( $this, 'frame_signature' ), 1000 );
    return $return;
  }

  public function enqueue() {

    if (defined('CS_APP_DEV_TOOLS') && CS_APP_DEV_TOOLS && isset( $_REQUEST['cs-render-test'])) {
      return;
    }

    $this->app->register_app_scripts( true );
    wp_enqueue_script( 'mediaelement' );

    add_filter( 'user_can_richedit', '__return_true' );

    ob_start();

    // WP Editor for preview
    $editorArgs = [
      'quicktags' => false,
      'tinymce'=> [
        'toolbar1' => 'bold,italic,strikethrough,underline,bullist,numlist,forecolor,cs_media,wp_adv',
        'toolbar2' => 'link,unlink,alignleft,aligncenter,alignright,alignjustify,outdent,indent',
        'toolbar3' => 'formatselect,pastetext,removeformat,charmap,undo,redo'
      ],
      'media_buttons' => false,
      'editor_class'  => 'cs-preview-wp-editor',
      'drag_drop_upload' => true
    ];

    // Filter
    // used by GlobalColors
    $editorArgs = apply_filters("cs_wp_editor_args", $editorArgs);

    // Endque preview editor
    wp_editor( '%%PLACEHOLDER%%','cspreviewwpeditor', $editorArgs);

    ob_end_clean();

    // Add preview data so we can use in inline-editing
    wp_register_script( 'cs-preview-data', false, [], CS_VERSION);
    wp_localize_script( 'cs-preview-data', 'csPreviewData', [
      'editor' => $editorArgs,
    ]);
    wp_enqueue_script("cs-preview-data");

    wp_enqueue_script( 'cs-app' );

    $preview_style_asset = $this->cssAsset->get('assets/css/app/preview');
    wp_register_style( 'cs-dashicons', '/wp-includes/css/dashicons.min.css' );
    wp_register_style( 'cs-editor-buttons', '/wp-includes/css/editor.min.css' );


    wp_enqueue_style( 'cs-preview', $preview_style_asset['url'], array(
      'cs-dashicons',
      'cs-editor-buttons',
    ), $preview_style_asset['version'] );

    wp_enqueue_script( 'cs-lottie' );
  }

  public function body_class( $classes ) {
    $classes[] = 'tco-preview';
    return $classes;
  }

  public function react_dev_tools() {
    ?>
    <script>if (window.parent !== window) window.__REACT_DEVTOOLS_GLOBAL_HOOK__ = window.parent.__REACT_DEVTOOLS_GLOBAL_HOOK__;</script>
    <?php
  }


  /**
	 * Replace the page content with a wrapping div that will be re-populated
	 * with our javascript application.
   * This happens through the `the_content` filter but only when no other document is rendering or a layout is rendering
	 */

  public function canOutputContentZone() {

    if ( doing_filter('get_the_excerpt') ) {
      // Never output the content zone while generating an exceprt
      // One potential issue is that if a plugin calls wp_trim_excerpt directly, this won't get triggered
      // and that exceprt may try to output a Cornerstone preview zone
      return false;
    }

    $stack = $this->resolver->getRenderStack();

    try {
      if ( ! empty( $stack ) ) {
        $doc = $this->resolver->getDocument(end($stack));
        $parts = explode(':', $doc->getDocType());
        return $parts[0] === 'layout' && ! in_array( $parts[1], ['header', 'footer']);
      }
    } catch (Exception $e) {

    }



    return true;

  }

	public function output_content_zone( $content ) {

    if ( ! $this->canOutputContentZone( $content ) ) {
      return $content;
    }

    ob_start();
    do_action('cs_content');
    $content = ob_get_clean();

    return cs_tag('div', [
      'id' => 'cs-content',
      'class' => apply_filters( 'builder_class', 'cs-content cs-content-builder' ),
      'data-cs-zone' => 'cs_content'
    ], $content );

  }


  public function detect_content_overlay( $post_id ) {

    if ( isset( $this->overlays[".cs-nav-overlay-post-$post_id"] ) || ( $this->previewState->isContent() && $this->queried_object['type'] === 'post' && (int) $post_id === $this->queried_object['postId'] ) ) {
      return false;
    }

    $post_type = get_post_type( $post_id === get_the_ID() ? null : $post_id );


    if ( !$post_type || !$this->permissions->userCan( "content.$post_type" ) ) {
      return false;
    }

    $post_type_obj = get_post_type_object( $post_type );

    $this->overlays[".cs-nav-overlay-post-$post_id"] = array(
      'action' => array(
        'route'   => "content/$post_id",
        'context' => $post_type_obj->labels->singular_name
      ),
      'label' => sprintf( csi18n( 'common.edit-context' ), $post_type_obj->labels->singular_name, get_the_title( $post_id ) )
    );

    return true;

  }

  public function observe_post_classes( $classes, $class, $post_id ) {
    if ( $this->detect_content_overlay( $post_id ) ) {
      $classes[] = "cs-nav-overlay-post-$post_id";
    }
    return $classes;
  }

  public function output_observed_overlays() {

    if ( count( $this->overlays ) > 0 ) {
      $data = json_encode( $this->overlays );
      echo "<script>window.csAppPreviewOverlays=$data</script>";
    }

  }

  public function output_initial_render() {
    if ($this->state['initialRender']) {
      try {
        $this->initial_render_content = $this->renderer->render([
          'rootElement' => $this->state['rootElement'],
          'config' => [
            'docType'       => $this->state['docType'],
            'documentId'    => $this->state['documentId'],
            'queriedObject' => $this->queried_object
          ],
          'flags' => $this->state['flags']
        ], true );
      } catch( Exception $e ) {
        $this->initial_render_content = [ 'error' => $e->getMessage() ];
      }

      $json = json_encode( $this->initial_render_content );
      $gzip = $this->http->gzip();

      $content = base64_encode( $gzip ? gzcompress( $json ) : $json );

      $atts = cs_atts([
        'data-cs-initial-render' => true,
        'type' => 'text/template',
        'data-cs-gzip' => $gzip
      ]);

      echo "<script $atts >$content</script>";
    }

  }

  public function nav_overlay_header( $atts ) {

    $header = cornerstone('Assignments')->get_last_active_header();

    if ( $header && ! $this->previewState->isHeader() ) {

      $post_type_obj = get_post_type_object( 'cs_header' );

      $atts['data-cs-observeable-nav'] = cs_prepare_json_att( array(
        'action' => array(
          'route'   => '/edit/' . $header->id(),
          'context' => $post_type_obj->labels->singular_name
        ),
        'label' => sprintf( csi18n( 'common.edit' ), $post_type_obj->labels->singular_name )
      ) );
    }

    return $atts;
  }

  public function nav_overlay_footer( $atts ) {

    $footer = cornerstone('Assignments')->get_last_active_footer();

    if ( $footer && ! $this->previewState->isFooter() ) {

      $post_type_obj = get_post_type_object( 'cs_footer' );

      $atts['data-cs-observeable-nav'] = cs_prepare_json_att( array(
        'action' => array(
          'route'   => '/edit/' . $footer->id(),
          'context' => $post_type_obj->labels->singular_name
        ),
        'label' => sprintf( csi18n( 'common.edit' ), $post_type_obj->labels->singular_name )
      ) );
    }

    return $atts;

  }

  public function nav_overlay_content( $atts, $id, $post_type ) {

    if ( $id && $post_type && $this->permissions->userCan( "content.$post_type" ) ) {

      $post_type_obj = get_post_type_object( $post_type );

      $atts['data-cs-observeable-nav'] = cs_prepare_json_att( array(
        'action' => array(
          'route'   => "/edit/$id",
          'context' => $post_type_obj->labels->singular_name
        ),
        'label' => sprintf( csi18n( 'common.edit' ), $post_type_obj->labels->singular_name )
      ) );
    }

    return $atts;

  }

  /**
   * When working on an archive make sure the wp_query is set properly
   * to show the valid layout
   */
  private function forceSetWPQuery($hook_type) {
    global $wp_query;

    // Make sure archives work properly
    if ($hook_type === 'layout-archive') {
      $wp_query->is_archive = true;
      $wp_query->is_singular = false;
      $wp_query->is_404 = false;
    }

    // Make sure single layouts work properly
    if ($hook_type === 'layout-single') {
      $wp_query->is_archive = false;
      $wp_query->is_singular = true;
      //$wp_query->is_404 = false;
    }
  }

}
