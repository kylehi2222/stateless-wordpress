<?php

// =============================================================================
// FUNCTIONS/GLOBAL/ADMIN/ADDONS/CLASS-ADDONS-UPDATES.PHP
// -----------------------------------------------------------------------------
// Manages updates for X and related plugins.
// =============================================================================

// =============================================================================
// TABLE OF CONTENTS
// -----------------------------------------------------------------------------
//   01. Update API
// =============================================================================

// Update API
// =============================================================================

class X_Validation_Updates {

  //
  // Holds a copy of itself so it can be referenced by the class name.
  //

  private static $instance, $theme_updater, $plugin_updater, $errors;
  private $api_key, $response;

  //
  // Adds a reference of this object to $instance and adds hooks.
  //

  public function __construct() {

    add_filter( 'themeco_update_api', array( $this, 'register' ) );
    add_filter( 'themeco_update_cache', array( $this, 'cache_updates' ), 10, 2 );
    add_action( 'themeco_update_api_response', array( $this, 'update' ) );
    add_action( 'init', array( $this, 'init' ) );

    x_validation()->add_script_data( 'x-automatic-updates', array( $this, 'script_data' ) );
    add_action( 'wp_ajax_x_update_check', array( $this, 'ajax_update_check' ) );
    add_action( 'wp_ajax_x_dismiss_validation_notice', array( $this, 'dismiss_validation_notice' ) );


  }

  public function get_latest_version() {

    $updates = tco_common()->updates()->get_update_cache();

    if ( isset( $updates['themes'] ) && isset( $updates['themes'][X_SLUG] ) && isset( $updates['themes'][X_SLUG]['new_version'] ) ) {
      return $updates['themes'][X_SLUG]['new_version'];
    }

    return X_VERSION;

  }

  public function check_ajax_referer() {
    if ( ! isset( $_REQUEST['_x_nonce'] ) || false === wp_verify_nonce( $_REQUEST['_x_nonce'], 'x-nonce' ) ) {
      wp_send_json_error();
    }
  }

  public function ajax_update_check() {

    $this->check_ajax_referer();

    if ( ! current_user_can( 'update_plugins' ) || ! current_user_can( 'update_themes' ) ) {
      wp_send_json_error();
    }

    delete_site_transient( 'update_themes' );
    tco_common()->updates()->refresh();
    $errors = tco_common()->updates()->get_errors();
    do_action( 'x_addons_update_check' );

    if ( empty( $errors ) ) {
      wp_send_json_success( array(
        'latest' => esc_html( $this->get_latest_version() )
      ) );
    }

    wp_send_json_error( array( 'errors' => $errors ) );

  }

  public function script_data() {
    return array(
      'complete'    => __( 'Nothing to report.', '__x__' ),
      'completeNew' => __( 'New version available!', '__x__' ),
      'error'       => __( 'Unable to check for updates. Try again later.', '__x__' ),
      'checking'    => __( 'Checking&hellip;', '__x__' ),
      'latest'      => esc_html( $this->get_latest_version() )
    );
  }

  public function register( $args ) {

	if ( !isset( $this->api_key ) || !$this->api_key )

		$this->api_key = esc_attr( get_option( 'x_product_validation_key', '' ) );

		$args['api-key'] = $this->api_key;
		$args['xversion'] = X_VERSION;
		return $args;
  }

  public function update( $data ) {

		$this->response = $data;

		if ( isset( $data['plugins'] ) ) {
			$this->cache_extensions( $data['plugins'] );
		}

    if ( isset( $data['error'] ) ) {
      delete_option( 'x_product_validation_key' );
      delete_site_transient( 'update_themes' );
      delete_site_transient( 'update_plugins' );
    }

  }

  public function cache_extensions( $plugins ) {

		$extensions = array();

		foreach ( $plugins as $slug => $plugin ) {

			if ( !isset( $plugin['x-extension'] ) ) continue;

			$extension = array_intersect_key( $plugin, array_flip( array(
				'slug', 'plugin', 'new_version', 'package'
			)));

			$extensions[] = array_merge( $extension, $plugin['x-extension'] );

		}

		update_site_option( 'x_extension_list', $extensions );

  }

  public function cache_updates( $updates, $data ) {

		if ( !isset( $updates['themes'] ) )
			$updates['themes'] = array();

		if ( !isset( $updates['plugins'] ) )
			$updates['plugins'] = array();

		$plugin_updates = array();
		$theme_updates = array();

		if ( isset( $data['plugins'] ) ) {
				foreach ( $data['plugins'] as $slug => $plugin ) {
					unset( $plugin['x-extension'] );
					$plugin_updates[$plugin['plugin']] = $plugin;
				}
		}

		if ( isset( $data['themes'] ) && isset( $data['themes'][X_SLUG]) ) {
				$theme_updates[X_SLUG] = (array) $data['themes'][X_SLUG];
		}

		$updates['themes'] = array_merge( $updates['themes'], $theme_updates );
		$updates['plugins'] = array_merge( $updates['plugins'], $plugin_updates );

		return $updates;

  }

  //
  // This class setup instantiates the theme and plugin updaters based on
  // WordPress permissions.
  //

  public function init() {

    if ( ! is_admin() ) return;

    $plugin_updater = new X_Plugin_Updater;
		$theme_updater = new X_Theme_Updater;

  }

  public function validate( $key ) {

		$this->api_key = $key;
		tco_common()->updates()->refresh( true );

		$errors = tco_common()->updates()->get_errors();

		$valid = ( empty( $errors ) &&  ( !isset( $this->response['error'] ) || '' == $this->response['error'] ) );

		return array(
			'valid'  => $valid,
			'message' => ( $valid) ? __( 'API Key Successfuly Validated!', '__x__' ) : $this->response['error'],
			'verbose' => $errors
		);

  }
  //
  // Override the API key so we can test one specifically.
  //

  public static function validate_key( $key ) {
		return self::$instance->validate( $key );
  }

  //
  // Links to the validation page (output when an update is available and if a
  // user has not yet validated their purchase).
  //

  public static function get_validation_html_theme_main() {
    return sprintf( __( '<a href="%s">Validate %s to enable automatic updates</a>', '__x__' ), x_addons_get_link_home(), X_TITLE );
  }

  public static function get_validation_html_theme_updates() {
    return sprintf( __( '<a href="%s">Validate %s to enable automatic updates</a>', '__x__' ), x_addons_get_link_home(), X_TITLE );
  }

  public static function get_validation_html_theme_update_error() {
    return sprintf( __( '%s is not validated. <a href="%s">Validate %s to enable automatic updates</a>', '__x__' ), X_TITLE, x_addons_get_link_home(), X_TITLE );
  }

  public static function get_validation_html_plugin_main() {
    return sprintf( __( '<a href="%s">Validate %s to enable automatic updates</a>.', '__x__' ), x_addons_get_link_home(), X_TITLE );
  }

  public static function get_validation_html_plugin_updates() {
    return self::get_validation_html_plugin_main();
  }

  public static function get_validation_html_plugin_update_error() {
    return sprintf( __( '%s is not validated. <a href="%s">Validate %s to enable automatic updates.</a>', '__x__' ), X_TITLE, x_addons_get_link_home(), X_TITLE );
  }


  //
  // Cache addons list in a transient.
  //

  public static function get_cached_addons() {

    if ( false === ( $addons = get_site_option( 'x_extension_list', false ) ) ) {

			tco_common()->updates()->refresh();

			if ( false === ( $addons = get_site_option( 'x_extension_list', false ) ) ) {
				return array(
					'error' => true,
					'message' => __( 'Could not retrieve extensions list. For assistance, please start by reviewing our article on troubleshooting <a href="https://theme.co/docs/problems-with-product-validation/">connection issues.</a>', '__x__' ),
					'verbose' => tco_common()->updates()->get_errors()
				);
			}

    }

    return $addons;

  }


  //
  // Upgrader screen message.
  //

  public function upgrader_screen_message( $false, $package, $upgrader ) {

    if ( null === $package ) {

      if ( isset( $upgrader->skin->plugin_info['X Plugin'] ) ) {

        return new WP_Error( 'x_not_valid', self::get_validation_html_plugin_update_error() );

      } else if ( isset( $upgrader->skin->theme_info['Name'] ) && 'X' == $upgrader->skin->theme_info['Name'] ) {

        return new WP_Error( 'x_not_valid', self::get_validation_html_theme_update_error()  );

      }
    }

    return $false;

  }

  public function dismiss_validation_notice() {
    update_option( 'x_dismiss_validation_notice', true );
    wp_send_json_success();
  }

  public static function instance( $light = false ) {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self( $light );
		}
		return self::$instance;
	}

}
