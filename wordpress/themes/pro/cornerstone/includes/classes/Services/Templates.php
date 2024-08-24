<?php

namespace Themeco\Cornerstone\Services;

use Themeco\Cornerstone\Util\Factory;
use Themeco\Cornerstone\Templating\Template;
use Themeco\Cornerstone\Templating\Export;
use Themeco\Cornerstone\Documents\Document;
use Themeco\Cornerstone\Documents\DocumentCache;
use Themeco\Cornerstone\Util\Endpoint;
use Themeco\Cornerstone\Util\Filesystem;

class Templates implements Service {

  protected $permissions;
  protected $imageImportEndpoint;
  protected $exportEndpoint;
  protected $exportAllEndpoint;
  protected $filesystem;
  protected $legacySiteImportEndpoint;
  protected $codebaseBridge;

  public function __construct(
    Permissions $permissions,
    Endpoint $imageImportEndpoint,
    Endpoint $exportEndpoint,
    Endpoint $legacySiteImportEndpoint,
    Endpoint $exportAllEndpoint,
    Filesystem $filesystem,
    CodebaseBridge $codebaseBridge
  ) {
    $this->permissions = $permissions;
    $this->imageImportEndpoint = $imageImportEndpoint;
    $this->exportEndpoint = $exportEndpoint;
    $this->exportAllEndpoint = $exportAllEndpoint;
    $this->filesystem = $filesystem;
    $this->legacySiteImportEndpoint = $legacySiteImportEndpoint;
    $this->codebaseBridge = $codebaseBridge;
  }

  public function setup() {

    $this->exportEndpoint->config( [
      'requestKey' => 'cs-export',
      'handler'    => [ $this, 'exportHandler' ]
    ])->start();

    $this->legacySiteImportEndpoint->config( [
      'requestKey' => 'cs-legacy-site-import',
      'handler'    => [ $this->codebaseBridge->legacyPlugin()->component('Controller_Design_Cloud'), 'import_site' ]
    ])->start();


    $this->imageImportEndpoint->config( [
      'requestKey' => 'cs-upload-image',
      'handler'    => [ $this, 'imageImportHandler' ]
    ])->start();

    // Export All templates
    $this->exportAllEndpoint->config( [
      'requestKey' => 'cs-export-all',
      'handler'    => [ $this, 'exportAllHandler' ]
    ])->start();

    add_action( 'init', function() {
      register_post_type( 'cs_template', array(
        'public'              => false,
        'exclude_from_search' => false,
        'capability_type'     => 'page',
        'supports'            => false
      ) );

      // Classic Cornerstone templates
      register_post_type( 'cs_user_templates', array(
        'public'          => false,
        'capability_type' => 'page',
        'supports'        => false
      ));
    });
  }

  public function query( $data = []) {

    $args = [
      'post_type' => array( 'cs_template', 'cs_user_templates' ), // include old type so it can be accounted for in migration
      'post_status' => array( 'tco-data', 'publish' ),
      'orderby' => 'title',
      'order' => 'ASC',
      'posts_per_page' => apply_filters( 'cs_query_limit', 2500 ),
      'cs_all_wpml' => true
    ];

    if ( isset( $data['type'] ) ) {
      $args['meta_key'] = '_cs_template_identifier';
      $args['meta_value'] = $data['type'];
    }

    $where_filter = null;

    if (isset($data['search'])) {

      $args['suppress_filters'] = false;

      $where_filter = function ($where) use ($data){
        // Search by title and post type.
        global $wpdb;
        $search = '%' .$data['search']. '%';
        $where .= $wpdb->prepare(" AND ({$wpdb->posts}.post_title LIKE %s OR {$wpdb->posts}.post_content LIKE %s)", $search,$search);
        return $where;
      };

      add_filter('posts_where', $where_filter);

    }

    if ( isset( $data['offset'] ) ) {
      $args['offset'] = $data['offset'];
    }

    $posts = get_posts( $args );

    if ( $where_filter ) {
      remove_action('posts_where', $where_filter);
    }

    return array_values(array_filter(array_map(function( $post ){
      try {
        $template = Template::locate( $post );
        if ($template) {
          return $template->serialize();
        }
        // Template::locate accounts for user permissions, and also active plugins like WC
        // it's possible to have a post in the DB but not allow it as a template
        return null;
      } catch (\Exception $e) {

        trigger_error("Unable to read template data " . $e->getMessage() );
      }

    },$posts)));

  }

  public function createExport() {
    return Factory::create(Export::class);
  }

  public function imageImportHandler( $request ) {
    if ( ! $this->permissions->userCan('template.manage_library') ) {
      throw new \Exception( 'Unauthorized' );
    }

    // This can take a long time due to image magick processing
    set_time_limit(1200);

    $list = explode(',', $request->get_param('cs_media_upload_files'));
    $result = [];

    foreach ($list as $hash) {

      try {

        global $wpdb;
        $results = $wpdb->get_results( $wpdb->prepare( "SELECT post_id FROM $wpdb->postmeta WHERE meta_key = '_cs_attachment_import' AND meta_value = %s", $hash ) );

        if ( $results ) {
          $post_id = $results[0]->post_id;
        } else {

          //Fix media import issue due to other 3rd party plugins that uses wp_handle_upload_prefilter
          global $wp_filter;
          unset($wp_filter['wp_handle_upload_prefilter']);

          require_once( ABSPATH . 'wp-admin/includes/image.php' );
          require_once( ABSPATH . 'wp-admin/includes/file.php' );
          require_once( ABSPATH . 'wp-admin/includes/media.php' );

          $post_id = media_handle_upload( 'cs_media_upload_' . $hash, 0, [], [ 'action' => 'wp_handle_upload'] );

          if ( is_wp_error( $post_id ) ) {
            throw new \Exception( $post_id->get_error_message() . ' ' . $hash);
            throw new \Exception( $post_id->get_error_message() );
          }

          update_post_meta( $post_id, '_cs_attachment_import', $hash );

        }

        $result[$hash] = [$post_id,wp_get_attachment_url( $post_id )];

      } catch( \Exception $e) {
        $result[$hash] = [ 'error' => $e->getMessage() ];
      }

    }

    return $result;
  }

  // Export .tco file (zip) and output it
  // via createExport
  public function exportHandler($data) {
    if ( ! $this->permissions->userCan('template.manage_library') ) {
      throw new \Exception( 'Unauthorized' );
    }

    if ( ! isset( $data['ids'] ) ) {
      throw new \Exception('ids not specified');
    }

    $excludeThumbnails = apply_filters('cs_template_export_exclude_thumbnails', false);

    $zip = $this->createExport()
      ->setOption('excludeThumbnails', $excludeThumbnails)
      ->add( $data['ids'] )
      ->organize()
      ->archive();

    if (is_wp_error($zip)) {
      throw new \DomainException($zip->get_error_message());
    }

    $this->filesystem->sendFile($zip);

  }

  /**
   * Export site handler
   */
  public function exportAllHandler($data) {
    if (!$this->permissions->userCan('global.export_documents')) {
      throw new \Exception( 'Unauthorized' );
    }

    // Get all post types in our document system
    $allowed = cornerstone('Resolver')->getAllowedPostTypes();

    // Remove global block otherwise it will add twice
    // through DependencyMapper
    $key = array_search('cs_global_block', $allowed);
    if($key !== false){
      unset($allowed[$key]);
    }

    //$allowed[] = 'cs_template';

    $tcoPosts = get_posts([
      'posts_per_page' => -1,
      'post_type' => $allowed,
      'post_status' => ['any', 'tco-data'],
      'fields' => 'ids',
    ]);

    // testing
    //$tcoPosts = [80];
    //$tcoPosts = [103];

    $export = $this->createExport();
    $export->setOption('fullSite', true);

    // Export doc
    add_filter('cs_export_doc_strategy', function() {
      return 'replace';
    });

    // Add each document to export
    foreach ($tcoPosts as $id) {
      $export->add($id, 'document');
    }

    $export->addMenus();

    // Setup zip file
    $export = $export->organize();
    $zip = $export->archive();

    // Bad zip generation
    if (is_wp_error($zip)) {
      throw new \DomainException($zip->get_error_message());
    }

    // Send file
    // exits after this
    $this->filesystem->sendFile($zip);

  }

}
