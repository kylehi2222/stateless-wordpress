<?php

namespace Themeco\Cornerstone\Services;

use Themeco\Cornerstone\Plugin;
use Themeco\Cornerstone\Util\Factory;
use Themeco\Cornerstone\Documents\Document;
use Themeco\Cornerstone\Documents\DocumentCache;
use Themeco\Cornerstone\Elements\Renderer;
use Themeco\Cornerstone\Elements\ElementData;

class Resolver implements Service {

  protected $renderStack = [];
  protected $docTypeInfoCache = [];

  protected $plugin;
  protected $elementsService;
  protected $permissions;
  protected $env;
  protected $documentCache;
  protected $assignments;
  protected $populatedDocTypes;
  protected $layout_template;
  protected $allowed;

  public function __construct(Plugin $plugin, Elements $elements, Permissions $permissions, Env $env, DocumentCache $documentCache, Assignments $assignments ) {
    $this->plugin = $plugin;
    $this->elementsService = $elements;
    $this->permissions = $permissions;
    $this->env = $env;
    $this->documentCache = $documentCache;
    $this->assignments = $assignments;
  }

  public function setup() {
    add_action('after_setup_theme', [ $this, 'attach' ] );
  }

  public function attach() {

    if (did_action( 'cs_before_preview_frame_component' ) ) {
      return;
    }

    add_action( 'init', array( $this, 'registerPostTypesAndStatuses' ) );

    if ( $this->env->isSiteBuilder() ) {
      add_action( 'template_redirect', array( $this, 'setup_views' ), 100 );
      add_action( 'cs_late_template_redirect', array( $this, 'setup_header' ) );
      add_action( 'cs_late_template_redirect', array( $this, 'setup_footer' ) );
      add_filter( 'template_include', array( $this, 'resolve_layout_template'), 97 ); // before under construction extension
    }

  }

  public function getDocument( $postOrIdOrDocument, $force = false ) {
    if ($force) {
      return $this->documentCache->get( $postOrIdOrDocument );
    }

    if ( is_a( $postOrIdOrDocument, Document::class ) ) {
      return $postOrIdOrDocument;
    }
    return $this->documentCache->get( $postOrIdOrDocument );
  }

  public function makeElementData($id, $data) {
    return $this->plugin->resolve(ElementData::class)->set($id, $data);
  }

  public function registerPostStatuses() {

  }

  public function registerPostTypesAndStatuses() {

    register_post_status( 'tco-data', [
      'internal'    => true
    ] );

    register_post_type( 'cs_global_block', array(
      'labels'          => array(
        'name'          => csi18n( "common.document.components" ),
        'singular_name' => csi18n( "common.document.component" ),
      ),
      'public'          => false,
      'capability_type' => 'page',
      'supports'        => false
    ) );

    if ( $this->env->isSiteBuilder() ) {

      register_post_type( 'cs_header', array(
        'public'          => false,
        'capability_type' => 'page',
        'supports'        => false,
        'labels'          => array(
          'name'          => csi18n( "common.document.headers" ),
          'singular_name' => csi18n( "common.document.header" ),
        )
      ) );

      register_post_type( 'cs_footer', array(
        'public'          => false,
        'capability_type' => 'page',
        'supports'        => false,
        'labels'          => array(
          'name'          => csi18n( "common.document.footers" ),
          'singular_name' => csi18n( "common.document.footer" ),
        )
      ) );

      register_post_type( 'cs_layout_single', array(
        'public'          => false,
        'capability_type' => 'page',
        'supports'        => false,
        'labels'          => array(
          'name'          => csi18n( 'common.document.singles' ),
          'singular_name' => csi18n( 'common.document.single' ),
        )
      ) );

      register_post_type( 'cs_layout_archive', array(
        'public'          => false,
        'capability_type' => 'page',
        'supports'        => false,
        'labels'          => array(
          'name'          => csi18n( 'common.document.archives' ),
          'singular_name' => csi18n( 'common.document.archive' ),
        )
      ) );

      //
      // Legacy
      //

      register_post_type( 'cs_layout', array(
        'public'          => false,
        'capability_type' => 'page',
        'supports'        => false,
        'labels'          => array(
          'name'          => csi18n( 'common.document.legacy-layouts' ),
          'singular_name' => csi18n( 'common.document.legacy-layout' ),
        )
      ) );
    }

  }


  /**
   * Migration from cs_layout to cs_layout_{type}
   * Pro5 to Pro6
   */
  public function migrateUntypedLayouts() {
    // Before get_posts grab
    do_action("cs_before_migrate_untyped_layouts_grab");

    // ids of cs_layout
    $ids = get_posts([
      'post_type' => 'cs_layout',
      'post_status' => ['tco-data'],
      'fields' => 'ids',
    ]);

    if (empty($ids)) {
      return;
    }

    // Process ids
    // Not used by WPML anymore
    do_action("cs_before_migrate_untyped_layouts", $ids);

    foreach ($ids as $id) {
      $ignore = apply_filters("cs_before_migrate_untyped_ignore", false, $id);

      if ($ignore) {
        continue;
      }

      $doc = Document::locate( $id );
      $newType = Document::resolvePostTypeForDocType($doc->getDocType());

      // Use Raw SQL
      if (
        !defined("CS_MIGRATE_UNTYPED_USE_RAW_UPDATE")
        || !empty(CS_MIGRATE_UNTYPED_USE_RAW_UPDATE)
      ) {
        $this->rawSQLPostTypeUpdate($id, $newType);
        continue;
      }

      $insert = [
        'ID'        => $id,
        'post_type' => $newType,
        'post_status'  => 'tco-data'
      ];

      wp_update_post($insert, true);
    }

    // Post type has changed
    do_action("cs_after_migrate_untyped_layouts");
  }

  public function setup_views() {

    $this->setup_layouts();

    // cs_output_header/cs_output_footer are false when
    // - we are previewing a Global Block
    // - the current Layout has the header/footer disabled
    // - the current legacy page template contains "No Header/Footer"

    if ( apply_filters( 'cs_output_header', true ) ) {
      do_action( 'cs_connect_masthead' );
    }

    if ( apply_filters( 'cs_output_footer', true ) ) {
      do_action( 'cs_connect_colophon' );
    }

  }


  public function setup_layouts() {

    $layout = apply_filters( 'cs_output_layout', true ) ? $this->assignments->get_active_layout() : null;

    if ( ! is_null( $layout ) ) {

      do_action('cs_will_output_layout', $layout );
      do_action('cs_will_output_layout_' . $layout->getDocType(), $layout );

      $layout_settings = $layout->settings();


      if (!$layout_settings['header_enabled']) {
        add_filter('cs_output_header', '__return_false' );
      }

      if (!$layout_settings['footer_enabled']) {
        add_filter('cs_output_footer', '__return_false' );
      }

      if ($layout_settings['layout_type']) {

        $this->loadDocument( $layout );

        if (!did_action('cs_before_preview_frame_layout')) {
          $this->deferRenderDocument( $layout, [ 'layout' => 'cs_layout' ] );
        }

        // these also match variants like single-wc and archive-wc
        if ( strpos( $layout_settings['layout_type'], 'single' ) === 0 ) {
          $this->layout_template = $this->plugin->path . '/includes/views/theming/layout-single.php';
        }

        if ( strpos( $layout_settings['layout_type'], 'archive' ) === 0 ) {
          $this->layout_template = $this->plugin->path . '/includes/views/theming/layout-archive.php';
        }

      }

    } else {
      if ( is_singular() ) {
        add_action( 'cs_layout', [ $this, 'auto_the_content' ] );
      }

      // see ThemeManagement::resolveTemplate for fallback templates
    }

  }

  public function auto_the_content() {

    $element_data = $this->makeElementData('content', [
      [
        '_region'         => 'content',
        '_type'           => 'the-content'
      ]
    ])->decorated();

    echo $this->elementsService->renderElements($element_data);

  }

  public function resolve_layout_template( $template ) {
    return isset($this->layout_template) ? $this->layout_template : $template;
  }

  public function setup_footer() {

    $footer = apply_filters( 'cs_output_footer', true ) ? $this->assignments->get_active_footer() : null;

    if ( is_null( $footer ) ) {
      return;
    }

    do_action('cs_will_output_footer', $footer);

    $this->loadDocument( $footer );

    if (!did_action('cs_before_preview_frame_footer')) {
      $this->deferRenderDocument( $footer, [ 'footer' => 'cs_colophon' ]);
    }

  }

  public function setup_header() {

    $header = apply_filters( 'cs_output_header', true ) ? $this->assignments->get_active_header() : null;

    if ( is_null( $header ) ) {
      return;
    }

    do_action( 'cs_will_output_header', $header );

    $this->loadDocument( $header );

    if (!did_action('cs_before_preview_frame_header')) {
      $this->deferRenderDocument( $header, [
        'top' => 'cs_masthead',
        'left' => 'cs_masthead',
        'bottom' => 'cs_masthead',
        'right' => 'cs_masthead',
      ]);
    }

  }

  public function loadDocument( $document ) {
    $doc = $this->getDocument( $document );
    $this->plugin->service('Tss')->registerDocument( $doc );
    $this->plugin->service('Styling')->addCustomCssFromDocument( $doc );
    $this->plugin->service('EnqueueScripts')->addCustomJsFromDocument( $doc );
  }


  // Prevent a document from ever rendering itself recursively
  public function safeDocRender( $doc, $fn ) {
    if ( in_array( $doc->id(), $this->renderStack, true ) ) {
      return '<!-- render loop -->';
    }
    $this->renderStack[] = $doc->id();
    $result = $fn();
    array_pop( $this->renderStack );
    return $result;
  }

  public function getRenderStack() {
    return $this->renderStack;
  }

  /**
   * Render elements by post ID
   * grab a document by ID and then render
   *
   * @param int $postId
   *
   * @return string
   */
  public function renderContentByPostId($postId) {
    $doc = $this->getDocument( $postId );

    if (is_null($doc)) {
      return '';
    }

    return $this->renderContentFromDocument($doc);
  }

  public function renderContentFromDocument( $doc ) {
    return $this->safeDocRender($doc, function() use ($doc) {

      $output = '';

      try {
        $this->loadDocument( $doc );
        $output = $this->renderDocument($doc);
      } catch ( \Exception $e ) {
        trigger_error( $e->getMessage(), E_USER_WARNING );
      }

      return $output;
    });
  }

  public function setupBarSpaces( $regions ) {

    add_action('wp_body_open', function() use ($regions) {
      // Hook in left and right bar spaces which are output earlier than their bars.
      $bar_space_actions = array(
        'left'  => 'x_before_site_begin',
        'right' => 'x_before_site_begin',
      );

      foreach ( $regions as $region ) {
        foreach ( $region['_modules'] as $element ) {
          if ($element['_type'] === 'bar') {
            if ( isset( $bar_space_actions[ $element['_region']] ) ) {
              if ( 'fixed' === cs_identity_bar_position( $element )) {
                cs_defer_html(
                  $bar_space_actions[ $element['_region'] ],
                  cs_create_bar_space( $this->plugin->service('Tss')->applyTssToElement( $element ) )
                );
              }
            }
          }
        }
      }
    });

  }

  public function deferRenderDocument( $document, $hooks = []) {
    $regions = $document->getElementData()->decorated();

    if ($document->getDocType() === 'layout:header') {
      $this->setupBarSpaces( $regions );
    }

    foreach( $regions as $region ) {
      if ( isset( $hooks[ $region['_region'] ] ) ) {
        cs_action_defer( $hooks[ $region['_region'] ], function() use ($region, $document) {
          echo $this->renderScoped( $region['_modules'], $document );
        }, [], 10, true );
      }
    }
  }

  public function renderDocument( $document ) {
    return $this->renderScoped( $document->getElementData()->decorated(), $document );
  }

  public function renderScoped( $elements, $document ) {
    $frame = cornerstone('Vm')->runtime()->newFrame();
    // PLACEHOLDER - This is where document specific Parameters can be loaded into the stack frame
    // Parameter::defineParametersForRender($data, $frame, $document->id());
    $result = $this->elementsService->renderElements( $elements );
    $frame->dispose();
    return do_shortcode( $result );
  }




  public function getDocumentTypeGroups() {

    $groups = [];
    $content = [];
    $layouts = [];
    $custom = [];

    $isApi = did_action('rest_api_init');

    $contentPostTypes = $this->permissions->getUserPostTypes();

    $builtIn = [];

    if ( $isApi || in_array( 'page', $contentPostTypes, true ) ) {
      $builtIn[] = 'page';
    }

    if ( $isApi || in_array( 'post', $contentPostTypes, true ) ) {
      $builtIn[] = 'post';
    }

    $contentTypes = array_unique( array_merge( $builtIn, $contentPostTypes ) ) ;

    foreach ($contentTypes as $type) {

      $post_type_obj = get_post_type_object( $type );
      if ( ! $post_type_obj ) {
        continue; // old permission of deactivated plugin
      }

      $labels = get_post_type_labels($post_type_obj);

      $content[] = array(
        'value' => 'content:' . $type,
        'postType' => $type,
        'permissionContext' => 'content.' . $type,
        'defaultTemplateGroup' => 'page',
        'labelSingular' => $labels->singular_name,
        'label' => $labels->name
      );

    }

    if ( ($isApi || $this->permissions->userCan( 'layout' ) ) && $this->env->isSiteBuilder() ) {

      $layouts[] = [
        'value' => 'layout:header',
        'postType' => 'cs_header',
        'assignmentContext' => 'any',
        'labelSingular' => csi18n('common.document.header'),
        'label' => csi18n('common.document.headers'),
        'permissionContext' => 'layout',
        'defaultTemplateGroup' => 'header'
      ];

      $layouts[] = [
        'value' => 'layout:footer',
        'postType' => 'cs_footer',
        'assignmentContext' => 'any',
        'labelSingular' => csi18n('common.document.footer'),
        'label' => csi18n('common.document.footers'),
        'permissionContext' => 'layout',
        'defaultTemplateGroup' => 'footer'
      ];

      $layouts[] = [
        'value' => 'layout:single',
        'postType' => 'cs_layout_single',
        'labelSingular' => csi18n('common.document.single'),
        'label' => csi18n('common.document.singles'),
        'permissionContext' => 'layout',
        'defaultTemplateGroup' => 'blog'
      ];

      $layouts[] = [
        'value' => 'layout:archive',
        'postType' => 'cs_layout_archive',
        'labelSingular' => csi18n('common.document.archive'),
        'label' => csi18n('common.document.archives'),
        'permissionContext' => 'layout',
        'defaultTemplateGroup' => 'blog'
      ];

      $layouts = apply_filters('_cs_document_layout_types', $layouts, $this->permissions, $isApi);
    }

    if ( $isApi || $this->permissions->userCan( 'component' ) ) {
      $custom[] = [
        'value' => 'custom:component',
        'postType' => 'cs_global_block',
        'labelSingular' => csi18n('common.document.component'),
        'label' => csi18n('common.document.components'),
        'permissionContext' => 'component'
      ];
    }

    if ( count( $content ) > 0 ) $groups[] = [ 'name' => 'content', 'label' => csi18n('common.document.content'), 'options' => $content ];
    if ( count( $layouts ) > 0 ) $groups[] = [ 'name' => 'layout',  'label' => csi18n('common.document.layouts'), 'options' => $layouts ];
    if ( count( $custom ) > 0 )  $groups[] = [ 'name' => 'custom',  'label' => csi18n('common.document.custom'),  'options' => $custom ];

    return $groups;
  }

  public function getPopulatedDocumentTypeGroups() {
    if ( ! isset( $this->populatedDocTypes ) ) {
      $this->populatedDocTypes = array_map(function( $group ) {
        $group['options'] = array_map(function($option){

          $option['portableSettings'] = [
            'customCSS',
            'customJS',
          ];

        // 'portableSettings' => ['customCSS', 'customJS', 'layout_type', 'header_enabled', 'footer_enabled'],

          $isContent = strpos($option['value'], 'content:') === 0;
          $isLayout =  strpos($option['value'], 'layout:') === 0;
          $isCustom =  strpos($option['value'], 'custom:') === 0;


          if ( $isContent ) {
            $option['portableSettings'][] = 'general_allow_comments';
            $option['portableSettings'][] = 'general_manual_excerpt';
            $option['portableSettings'][] = 'general_page_template';
          }

          if ($isContent || $isCustom) {
            $option['titleKey'] = 'general_post_title';
            $option['regions'] = [ 'content' ];
          }

          if ($isCustom) {
            $option['portableSettings'][] = 'document_visibility';
          }

          if ($isLayout ) {
            $option['titleKey'] = 'general_title';
            $option['regions'] = [ 'layout' ];
            $option['portableSettings'][] = 'layout_type';
            $option['portableSettings'][] = 'header_enabled';
            $option['portableSettings'][] = 'footer_enabled';

            $isHeader = $option['value'] === 'layout:header';
            $isFooter = $option['value'] === 'layout:footer';
            $isSingle = $option['value'] === 'layout:single';
            $isArchive = $option['value'] === 'layout:archive';

            if ($isHeader) {
              $option['portableSettings'][] = 'multi_region';
              $option['regions'] = [ 'top', 'left', 'right', 'bottom' ];
            }

            if ($isFooter) {
              $option['portableSettings'][] = 'multi_region';
              $option['regions'] = [ 'footer' ];
            }

            if ( $isHeader || $isFooter) {
              $option['defaultPreviewUrl'] = home_url();
            }

            if ( $isSingle ) {
              $posts = get_posts( ['numberposts' => 1, 'post_type' => 'post' ] ); // find first post
              if ( empty( $posts ) ) {
                $posts = get_posts( ['numberposts' => 1, 'post_type' => 'page' ] ); // use a page if there are no posts
              }
              $option['defaultPreviewUrl'] = empty($posts[0]) ? home_url() : get_permalink( $posts[0]->ID );
            }

            if ( $isArchive ) {
              $blog_page_id = get_option( 'page_for_posts' );
              $option['defaultPreviewUrl'] = $blog_page_id ? get_permalink( $blog_page_id ) : home_url();
            }

          }

          return apply_filters('_cs_document_layout_types_populate', $option);
        }, $group['options']);
        return $group;
      }, $this->getDocumentTypeGroups() );
    }
    return $this->populatedDocTypes;
  }

  public function findDocTypeInfo( $type ) {
    $groups = $this->getPopulatedDocumentTypeGroups();

    foreach ($groups as $group) {
      foreach( $group['options'] as $option ) {
        if ( $option['value'] === $type ) {
          return $option;
        }
      }
    }
    return null;
  }

  public function getDocTypeInfo( $type ) {
    if ( ! isset( $this->docTypeInfoCache[ $type ] ) ) {
      $this->docTypeInfoCache[ $type ] = $this->findDocTypeInfo( $type );
    }
    return $this->docTypeInfoCache[ $type ];
  }

  /**
   * Get option value for all groups registered as documents
   */
  public function getDocOptionInfo($docKey = 'value') {
    $groups = $this->getDocumentTypeGroups();
    $types = [];
    foreach ($groups as $group) {
      foreach ($group['options'] as $option) {
        // Error check
        if (empty($option[$docKey])) {
          trigger_error('Key not found on group ' . $group . ' : ' . $docKey);
          continue;
        }

        $types[] = $option[$docKey];
      }
    }

    return $types;
  }

  /**
   * Allowed doc types in cornerstone layout:footer format
   */
  public function getAllowedDocTypes() {
    if ( ! isset( $this->allowed ) ) {
      $this->allowed = $this->getDocOptionInfo('value');
    }

    return $this->allowed;
  }

  /**
   * Gets the document option of 'postType'
   */
  public function getAllowedPostTypes() {
    return $this->getDocOptionInfo('postType');
  }

  public function clearDocumentCache($id) {
    $this->documentCache->unset($id);
  }

  /**
   * Use Raw SQL as update
   */
  private function rawSQLPostTypeUpdate($id, $postType) {
    global $wpdb;
    $table = esc_sql("{$wpdb->prefix}posts");

    // Update icl_translations
    $sql = "UPDATE $table
      SET post_type = %s
      WHERE ID = %d
    ";

    $prepared = $wpdb->prepare($sql, $postType, $id);

    // Update Query
    $wpdb->query($prepared);
  }
}
