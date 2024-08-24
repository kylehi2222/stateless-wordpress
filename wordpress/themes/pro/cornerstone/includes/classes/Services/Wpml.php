<?php

namespace Themeco\Cornerstone\Services;

use Themeco\Cornerstone\Util\Endpoint;
use Themeco\Cornerstone\Documents\Document;

class Wpml implements Service {

  protected $previous_lang;

  // Map of post to original language data
  protected $postTypeMigrationData = [];

  protected $controllerTranslationEndpoint;
  private $wpml_lang;
  private $wpml_trid;

  public function __construct(Endpoint $controllerTranslationEndpoint) {
    $this->controllerTranslationEndpoint = $controllerTranslationEndpoint;
  }

  public function setup() {

    if ( ! $this->is_active() ) {
      return;
    }

    $this->controllerTranslationEndpoint->config( [
      'requestKey' => 'cs-translation',
      'handler'    => [ $this, 'createTranslation' ]
    ])->start();

    add_action( 'option_wpml_registered_endpoints', array( $this, 'unset_endpoint' ) );

    add_action( 'cs_before_preview_frame', array( $this, 'before_preview_frame' ) );
    add_filter( 'cs_locate_wpml_language', array( $this, 'locate_wpml_language'), 10, 2);
    add_filter( 'cs_global_block_id', array($this,  'icl_global_block_id' ) );

    add_filter('cs_before_preview_frame_filter', [$this, 'before_preview_frame_filter'] );

    add_filter( 'wpml_enqueue_browser_redirect_language', array( $this, 'disable_browser_redirect' ) );


    add_filter( 'pre_get_posts', array( $this, 'pre_get_posts' ) );

    if ( apply_filters ( 'cs_enable_wpml_legacy_filters', false ) ) {

      add_filter( 'the_title', array( $this, 'filter_title' ), 99, 2 );
      add_filter( 'the_permalink', array( $this, 'filter_permalink' ) );

    }

    // Prevent '_cornerstone_data' from being duplicated
    add_action( 'save_post', array( $this, 'save_post' ), -100 );

    // Detect and filter to correct translation
    add_filter("cs_detect_assigned_document", [ $this, 'filterAssignedDoc' ], -100);

    add_filter("cs_filter_app_url", [ $this, 'filterAppURL' ], -100);
    add_filter("cs_app_url_base", [ $this, 'filterUrlBase' ], 0);

    add_action("cs_before_save_request", [ $this, 'preventDuplicatePageSave' ]);

    add_action('cs_before_migrate_untyped_layouts_grab', [$this, 'beforeMigrateUntypedLayoutsNonDefault']);

    add_action("cs_before_migrate_untyped_layouts", [ $this, 'setTranslationModeLayouts' ]);

    // WooCommerce shop page URL change per translation setting of document
    add_filter('cs_document_layout_archive-wc_builder_info', [ $this, 'filterWCArchiveBuilderInfo'], 0, 2 );

    add_filter('cs_url_to_validate', function($url) {
      // We use raw option because WPML will overwrite
      // And this causes issues on subdomain mode
      if (!is_multisite()) {
        return esc_attr( trailingslashit( get_option('home') ) );
      }

      // Multisite doesn't work properly anyway so send the normal URL
      return $url;
    });

    //add_action("cs_before_migrate_untyped_ignore", [ $this, "beforeMigrateUntypedLayoutsIgnoreList" ], 0, 2 );

    //add_action("cs_after_migrate_untyped_layouts_post", [ $this, 'afterMigrateUntypedLayouts' ], 0, 1 );

    //add_action("cs_after_migrate_untyped_layouts", [ $this, 'afterMigrateUntypedLayouts' ], 0, 1);

    // PHP 8 media issue
    // add_filter('wpml_media_content_for_media_usage', '__return_false');
  }

  public function is_active() {
    return class_exists( 'SitePress' );
  }

  public function locate_wpml_language( $lang, $post ) {
    global $sitepress;
    $language_details = $sitepress->get_element_language_details( $post->ID, 'post_' . $post->post_type );
    if ($language_details) {
      $lang = $language_details->language_code;
    }
    return $lang;
  }

  public function before_preview_frame( $state ) {

    if ( isset( $state['lang']) && ! isset( $_REQUEST['lang']) ) {
      $_REQUEST['lang'] = $state['lang'];
    }

    if ( isset( $_REQUEST['lang'] ) &&
      ( '3' === \wpml_get_setting_filter(false, 'language_negotiation_type')
      || isset( $state['lang'] ) )
    ) {
      add_action('wp_loaded', array( $this, 'set_preview_lang' ), 11 );
    }
  }


  /**
   * Filter url so we don't encounter redirect
   * None of disable redirect function so I think it's internal
   * this filters the URL so this doesn't happen
   */
  public function before_preview_frame_filter($state) {
    $post = get_post($state['documentId']);

    // This only happens on posts in a draft state
    if ($post->post_status !== "draft") {
      return $state;
    }

    // Get default language as that what redirects to
    // for draft pages and cs-render
    $translated = $this->getTranslatedObjectId(
      $state['documentId'],
      $state['docType'],
      $this->get_default_language()
    );

    // Found translation
    if ($translated && $translated !== $state['documentId']) {
      $state['url'] = get_option("home") . "/" . '?page_id=' . $translated . '&lang=' . $state['lang'] . '/';
    }

    // Filter
    return $state;
  }

  public function set_preview_lang() {
    global $sitepress;
    $sitepress->switch_lang($_REQUEST['lang']);
  }


  public function switch_lang( $lang = 'all' ) {

    if ( ! $this->is_active() ) {
      return;
    }

    global $sitepress;
    $this->previous_lang = $sitepress->get_current_language();

    $sitepress->switch_lang($lang);

  }

  public function switch_back() {

    if ( ! $this->is_active() ) {
      return;
    }

    global $sitepress;
    $sitepress->switch_lang( $this->previous_lang );

  }

  public function get_previous_lang() {
    return $this->previous_lang;
  }


  public function pre_get_posts( $query ) {

    global $sitepress;

    if ( ! is_callable( array( $sitepress, 'switch_lang' ) ) || ! is_callable( array( $sitepress, 'get_current_language' ) ) ) {
      return $query;
    }

    if ( isset( $query->query_vars['cs_all_wpml'] ) && $query->query_vars['cs_all_wpml'] ) {
      return $query;
    }

    // $sitepress->switch_lang( $sitepress->get_current_language() ); //Make sure that even custom query gets the current language

    $query->query_vars['suppress_filters'] = false;

    return $query;

  }

  //WPML Post object usable by multiple filters
  private function wpml_post() {

    global $post, $sitepress;

    if ( ! $post || ! function_exists( 'icl_object_id' ) || ! is_callable( array( $sitepress, 'get_current_language' ) ) ) {
      return;
    }

    return get_post( icl_object_id( $post->ID, 'post', false, $sitepress->get_current_language() ) );
  }

  public function icl_global_block_id ( $ID ) {

    if( !$this->is_active() ) return $ID;

    global $sitepress;

    $global_block_id = icl_object_id( $ID, 'cs_global_block', false, $sitepress->get_current_language() );

    return get_post( empty( $global_block_id ) ? apply_filters('cs_global_block_linked_id', $ID ) : $global_block_id )->ID;

  }

  public function filter_title( $title, $id = null ) {

    $wpml_post = $this->wpml_post();

    return ( ! is_a( $wpml_post, 'WP_Post' ) || $wpml_post->ID !== $id ) ? $title :
      // Let's apply the_title filters (apply_filters causes loop)
      trim( convert_chars( wptexturize( esc_html( $wpml_post->post_title ) ) ) );
  }

  public function filter_permalink( $permalink ) {

    $wpml_post = $this->wpml_post();

    if ( is_a( $wpml_post, 'WP_Post' ) ) {
      $permalink = get_permalink( $wpml_post->ID );
    }

    return $permalink;

  }

  public function get_language_data_from_post( $post, $all = false ) {
    if ( is_int( $post ) ) {
      $post = get_post( $post );
    }
    return $this->get_language_data( $post->ID, $post->post_type, $all );
  }

  public function get_language_data( $original_id, $object_type = 'post', $all = false ) {

    if ( ! $this->is_active() ) {
      return array();
    }

    $languages = $this->get_languages();

    global $sitepress;
    $details = $sitepress->get_element_language_details( $original_id );

    if ( ! $details ) {

      $default_language = $sitepress->get_default_language();

      $output = array(
        'code' => $default_language,
        'source' => $original_id,
        'fallback' => array_values( array_diff( array_keys( $languages ), array( $default_language ) ) )
      );

      if ( $all ) {
        $output['missing'] = $output['fallback'];
        $output['translations'] = array( $default_language => $original_id );
      }

      return $output;

    }

    $translations = (array) $sitepress->get_element_translations( $details->trid, $object_type );

    $available = array();
    $is_source = false;
    $source = null;

    foreach ($translations as $translation ) {
      $available[$translation->language_code] = (int) $translation->element_id;
      if ( $translation->original ) {
        if( (int) $original_id === (int) $translation->element_id ) {
          $is_source = true;
        } else {
          $source = (int) $translation->element_id;
        }
      }
    }

    $output = array(
      'code' => $details->language_code,
      'source' => $source,
      'fallback' => array()
    );

    if ( $all ) {
      $output['missing'] = array();
      $output['translations'] = $available;
    }

    foreach ( $languages as $code => $language ) {
      if ( ! isset( $available[$code] ) ) {
        if ( $is_source ) {
          $output['fallback'][] = $code;
        }
        if ( $all ) {
          $output['missing'][] = $code;
        }
      }
    }


    return $output;

  }


  public function get_languages() {
    if ( ! $this->is_active() ) {
      return array();
    }
    return apply_filters('wpml_active_languages', array() );
  }

  public function get_default_language() {
    global $sitepress;
    return $this->is_active() ? $sitepress->get_default_language() : null;
  }

  public function appData() {
    if ( ! $this->is_active() ) return null;
    // I coudln't find a clean way to use the current language from the "backend" so unfortunately it always uses the default lang
    return [
      'defaultLang'        => $this->get_default_language(),
      'languages'          => $this->get_languages(),
      'translateableTypes' => $this->get_translateable_post_types(),
    ];
  }

  public function get_translateable_post_types() {

    $output = array( 'page', 'post' );

    if ( $this->is_active() ) {

      global $sitepress;
      $types = $sitepress->get_setting('custom_posts_sync_option');

      foreach ($types as $key => $value) {
        if ( $value ) {
          $output[] = $key;
        }
      }

    }

    return $output;
  }

  public function get_element_language_details( $post_id ) {

    if ( ! $this->is_active() ) {
      return null;
    }

    global $sitepress;
    return (array) $sitepress->get_element_language_details( $post_id );

  }

  public function get_language_label( $code ) {
    // @TODO In createTranslation I believe that code runs before this
    // can properly get the wpml_active_languages?
    // Load languages / make sure WPML is loaded
    $languages = $this->get_languages();
    foreach ($languages as $lang_code => $value) {
      if ( $code === $lang_code ) {
        return isset( $value['translated_name'] ) ? $value['translated_name'] : '';
      }
    }
    return $code;
  }

  public function before_get_permalink() {

    if ( ! $this->is_active() ) {
      return;
    }

    global $wpml_url_filters;

    $wpml_url_filters->remove_global_hooks();
    if ( $wpml_url_filters->frontend_uses_root() === true ) {
    	remove_filter( 'page_link', array( $wpml_url_filters, 'page_link_filter_root' ), 1, 2 );
    } else {
    	remove_filter( 'page_link', array( $wpml_url_filters, 'page_link_filter' ), 1, 2 );
    }

  }

  public function after_get_permalink() {

    if ( ! $this->is_active() ) {
      return;
    }

    global $wpml_url_filters;

    $wpml_url_filters->add_global_hooks();

    if ( $wpml_url_filters->frontend_uses_root() === true ) {
    	add_filter( 'page_link', array( $wpml_url_filters, 'page_link_filter_root' ), 1, 2 );
    } else {
    	add_filter( 'page_link', array( $wpml_url_filters, 'page_link_filter' ), 1, 2 );
    }

  }

  public function get_source_id_for_post( $post_id, $post_type = false ) {

    if ( ! $this->is_active() ) {
      return $post_id;
    }

    if ( ! $post_type ) {
      $post = get_post( $post_id );
      $post_type = $post->post_type;
    }

    global $sitepress;
    $details = (array) $sitepress->get_element_language_details( $post_id );

    if ( ! $details || ! isset( $details['source_language_code'] ) ) {
      return $post_id;
    }

    return apply_filters( 'wpml_object_id', $post_id, $post_type, true, $details['source_language_code'] );

  }

  public function unset_endpoint( $endpoints ) {
    unset($endpoints['cornerstone-endpoint']);
    return $endpoints;
  }

  public function save_post() {
    global $sitepress_settings;

    if ( $sitepress_settings && isset( $sitepress_settings['translation-management'] ) && isset( $sitepress_settings['translation-management']['custom_fields_translation'] ) ) {
      unset($sitepress_settings['translation-management']['custom_fields_translation']['_cornerstone_data']);
    }
  }

  public function disable_browser_redirect ( $enable ) {

    return did_action('cs_before_preview_frame') ? false : $enable;

  }

  public function createTranslation( $data ) {

    if ( ! isset( $data['source'] ) ) {
      return new \WP_Error( 'cornerstone', 'Source missing.' );
    }

    if ( ! isset( $data['lang'] ) ) {
      return new \WP_Error( 'cornerstone', 'Target language missing.' );
    }

    $source_post = get_post( $data['source'] );

    if ( ! is_a( $source_post, 'WP_POST' ) ) {
      return new \WP_Error( 'cornerstone', 'Source invalid.' );
    }

    if ( ! in_array( $source_post->post_type, $this->get_translateable_post_types(), true ) ) {
      return new \WP_Error( 'cornerstone', 'WPML does not allow this post type (' . $source_post->post_type . ') to be translated.' );
    }

    if ( ! function_exists('wpml_load_post_translation') ) {
      return new \WP_Error( 'cornerstone', 'WPML not active' );
    }

    global $sitepress;
    global $iclTranslationManagement;

    $copy_from = isset( $data['copyFrom'] ) && $data['copyFrom'] ? $data['copyFrom'] : null;


    // Valid post type
    //$post_type = in_array($source_post->post_type, ['post', 'page'])
      //? 'post_' . $source_post->post_type
      //: $source_post->post_type;

    $post_type = 'post_' . $source_post->post_type;


    // Setup new post
    $lang_label = $this->get_language_label( $data['lang'] );
    $post_title = sprintf( csi18n('common.amended-title'), $source_post->post_title,  $lang_label);

    // Args for new post to create
    $args = array(
      'post_type' => $source_post->post_type,
      'post_status' => $source_post->post_status,
      'post_title' => $post_title,
    );

    // Copy slug for usability
    if (!empty($source_post->slug)) {
      $args['slug'] = $source_post->slug . '-' . $lang_label;
    }


    // Set trid
    $this->wpml_lang = $data['lang'];
    //$details = $this->get_element_language_details( $source_post->ID );


    // Translation ID grabber
    $this->wpml_trid = apply_filters(
      'wpml_element_trid',
      null,
      $source_post->ID,
      $post_type
    );

    // Translation details like language code
    $details = apply_filters( 'wpml_post_language_details', null, $source_post->ID );

    $source_language = isset( $details['language_code'] ) ? $details['language_code'] : null;

    //
    // trid will not exist if the header/footer has not been saved at least once since
    // WPML was activated. If this is the case, we could trigger a save here to generate
    // the source trid before continuing
    //

    if ( ! $this->wpml_trid ) {


      $this->wpml_trid = null; // Try to populate this

      return new \WP_Error( 'cornerstone', 'No Translation ID found. Please save before creating a translation and ensure that this is a post type WPML can translate' );
    }

    if ( $this->wpml_trid ) {

      $id = wp_insert_post( $args, true );

      if ( is_wp_error( $id ) ) {
        return $id;
      }

      // Duplicate content
      if ($copy_from) {
        $doc = Document::locate($id);
        $doc->update([
          'title' => $post_title,
          'clone' => $copy_from,
        ]);
        $doc->save();
      }

      global $wpml_post_translations;

  		$sitepress->set_element_language_details (
  			$id,
  			'post_' . $source_post->post_type,
  			$this->wpml_trid,
  			$this->wpml_lang,
  			$source_language
  		);

      $settings = get_option( 'icl_sitepress_settings' );
  		$translation_sync = new \WPML_Post_Synchronization( $settings, $wpml_post_translations, $sitepress );
  		$original_id      = $wpml_post_translations->get_original_element( $id );
      $new_post = (array) get_post($id);
  		$translation_sync->sync_with_translations( $original_id ? $original_id : $id, $new_post );
  		$translation_sync->sync_with_duplicates( $id );
  		if ( ! function_exists( 'icl_cache_clear' ) ) {
  			require_once WPML_PLUGIN_PATH . '/inc/cache.php';
  		}
  		icl_cache_clear( $source_post->post_type . 's_per_language', true );
  		wp_defer_term_counting( false );
  		if ( $source_post->post_type !== 'nav_menu_item' ) {
  			do_action( 'wpml_tm_save_post', $id, get_post( $id ), false );
  		}

      // Flush object cache.
  		$cache_groups = array( 'ls_languages', 'element_translations' );
      foreach ( $cache_groups as $group ) {
        $cache = new \WPML_WP_Cache( $group );
        $cache->flush_group_cache();
      }

  		do_action( 'wpml_after_save_post', $id, $this->wpml_trid, $this->wpml_lang, $source_language );

      return array( 'id' => $id );

    }

    return new \WP_Error( 'cornerstone', 'Could not locate a translation for the source post.' );

  }

  /**
   * Get wpml_object_id for given data
   * Returns zero on no translation
   */
  private function getTranslatedObjectId($id, $docType, $lang) {
    // Attempt to find translation
    // Fallback to what was passed after
    return (int) apply_filters(
      'wpml_object_id',
      $id,
      Document::resolvePostTypeForDocType( $docType ),
      false,
      $lang
    );
  }

  /**
   * Filter Assignment to be proper translation page
   *
   * @param array $input ['id', 'type']
   */
  public function filterAssignedDoc($input) {
    list ($type, $assignment) = $input;

    // Find master post id to get proper translation
    $master = apply_filters( 'wpml_master_post_from_duplicate', $assignment );

    // Not passed master post id
    $assignmentToCheck = !empty($master)
      ? $master
      : $assignment;

    // Attempt to find translation
    // Fallback to what was passed after
    $translated = $this->getTranslatedObjectId(
      $assignmentToCheck,
      $type,
      apply_filters( 'wpml_current_language', null )
    );

    // Filter input or keep the same
    $input[1] = empty($translated)
      ? $assignment
      : $translated;

    return $input;
  }

  /**
   * or /es/cornerstone
   * which won't work
   */
  public function filterAppURL($url) {
    // We use get_option because WPML overwrites home_url()
    $url = get_option('home') . '/' . apply_filters('cs_app_slug', 'cornerstone');
    return $url;
  }

  /**
   * If the language default is to redirect to a directory
   * the url base will be wrong
   * this fixes that and usually just points to /
   */
  public function filterUrlBase($result) {
    $directory_for_default_language = apply_filters( 'wpml_sub_setting', false, 'urls', 'directory_for_default_language' );

    // Not using default directory
    if (empty($directory_for_default_language)) {
      return $result;
    }

    $parsed = parse_url(get_option("home") . "/");
    $urlBase = empty($parsed['path'])
      ? "/"
      : $parsed['path'];
    return $urlBase;
  }

  /**
   * Hack to remove WPML page builder settings
   * @TODO how do we really want to handle this?
   */
  public function preventDuplicatePageSave() {
    // To turn off and dev
    if (apply_filters("cs_wpml_enable_page_builder_integration", false)) {
      return;
    }

    // Prevent CS string translated for now
    remove_all_actions('wpml_page_builder_string_translated');
  }

  /**
   * Set translation modes of our layout types
   */
  public function setTranslationModeLayouts() {
    $layoutTypes = [
      'cs_layout_archive',
      'cs_layout_single',
      'cs_layout_archive_wc',
      'cs_layout_single_wc',
    ];

    $translationMode = 'translate';

    foreach ($layoutTypes as $type) {
      do_action( 'wpml_set_translation_mode_for_post_type', $type, $translationMode );
    }
  }


  /**
   * Figure out which languages need filter suppression
   */
  public function beforeMigrateUntypedLayoutsNonDefault($ids = []) {
    if (!apply_filters("cs_wpml_use_untyped_migration", true)) {
      return;
    }

    // Nothing to do
    if (!$this->hasLegacyLayoutTranslations()) {
      return;
    }

    $this->removeWPMLActions();

    if (!defined("CS_MIGRATE_UNTYPED_USE_RAW_UPDATE")) {
      define("CS_MIGRATE_UNTYPED_USE_RAW_UPDATE", true);
    }

    $this->cleanupCSLayoutsNotMigrated();

    $this->postTypeMigrationData = $ids;
  }

  // Remove all WPML filters
  // This is so WPML doesn't move the icl_translations
  // table by itself which won't move stuff correctly
  // all the time
  public function removeWPMLActions() {
    remove_all_filters("pre_get_posts");
    remove_all_filters("pre_get_post");
    remove_all_actions("save_post");
    remove_all_actions("pre_post_update");
  }

  /**
   * Is post in different then default language
   */
  public function isDifferentLanguage($id, $postType) {
    // Translation ID grabber
    $wpml_trid = apply_filters(
      'wpml_element_trid',
      null,
      $id,
      "post_" . $postType
    );

    // This one does not give you source_language_code
    //$details = apply_filters( 'wpml_post_language_details', null, $id );

    $details = (array) apply_filters( 'wpml_element_language_details', null, [
      'element_id' => $id,
      'element_type' => 'post_' . $postType,
    ]);

    $defaultLang = apply_filters( 'wpml_default_language', null );

    // Not even setup or no trid for this post
    if (
      empty($defaultLang)
      || empty($wpml_trid)
      || empty($details)
      // If not the original post but a translation
      || empty($details['source_language_code'])
    ) {
      return false;
    }

    return true;
  }

  /**
   * Saving ignore list did not use this method
   */
  public function beforeMigrateUntypedLayoutsIgnoreList($defaultVal, $id) {
    return false;
  }

  /**
   * This essentially runs the same functions
   * just after the post types
   */
  public function afterMigrateUntypedLayouts($ids = []) {
    //$this->cleanupCSLayoutsNotMigrated();
  }


  /**
   * Legacy translation detection
   */
  public function hasLegacyLayoutTranslations() {
    global $wpdb;

    $table = esc_sql("{$wpdb->prefix}icl_translations");

    // Grab Check if has a count
    $sql = "SELECT COUNT(element_id) as count
      FROM {$table}
      WHERE element_type = 'post_cs_layout'
    ";

    $results = $wpdb->get_results($sql, ARRAY_A);

    return !empty($results[0]['count'])
      && (int)$results[0]['count'] !== 0;
  }

  /**
   * Raw SQL updates of icl_translations table
   * Runs before post type change as
   * changing post type before caused issues
   */
  public function cleanupCSLayoutsNotMigrated() {
    if (!apply_filters("cs_wpml_use_untyped_migration", true)) {
      return;
    }

    global $wpdb;

    $table = esc_sql("{$wpdb->prefix}icl_translations");

    // Grab by legacy post type
    $sql = "SELECT element_id
      FROM {$table}
      WHERE element_type = 'post_cs_layout'
    ";

    $results = $wpdb->get_results($sql);

    foreach ($results as $result) {
      $id = $result->element_id;

      $doc = Document::locate( $id );

      // No doc found, but translation
      // data there
      if (empty($doc)) {
        continue;
      }

      $newType = Document::resolvePostTypeForDocType($doc->getDocType());

      // Post type is already changed at this point
      $type = $newType;

      // Always needed for this table
      $type = "post_" . $type;

      // Update icl_translations
      $sql = "UPDATE {$table}
        SET element_type = %s
        WHERE element_id = %d
        AND element_type = 'post_cs_layout'
      ";

      $prepared = $wpdb->prepare($sql, $type, $id);

      // Run update query
      $wpdb->query($prepared);
    }
  }

  // Adds in a proper documentUrl for the translations shop page
  public function filterWCArchiveBuilderInfo($builderInfo, $doc) {
    $langInfo = $this->get_element_language_details($doc->id());

    if (empty($langInfo)) {
      return $builderInfo;
    }

    // Grab Shop ID based on document language code
    $this->switch_lang($langInfo['language_code']);
    $shop_id = wc_get_page_id( 'shop' );
    $this->switch_back();

    $builderInfo['documentUrl'] = get_permalink($shop_id);

    return $builderInfo;

  }
}
