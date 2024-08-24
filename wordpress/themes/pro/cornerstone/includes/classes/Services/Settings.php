<?php

namespace Themeco\Cornerstone\Services;

use Themeco\Cornerstone\Util\AdminAjax;

class Settings implements Service {

  protected $settings;

  protected $permissions;
  protected $ajaxClean;
  protected $ajaxSave;

  public function __construct(AdminAjax $ajaxClean, AdminAjax $ajaxSave, Permissions $permissions) {
    $this->permissions = $permissions;
    $this->ajaxClean = $ajaxClean;
    $this->ajaxSave = $ajaxSave;
  }

  public function setup() {
    $this->ajaxClean->setAction( 'dashboard_clear_system_cache' )->setHandler( [ $this, 'ajaxClean'] )->start();
    $this->ajaxSave->setAction( 'dashboard_save_settings' )->setHandler( [ $this, 'ajaxSave'] )->start();

    // API
    add_filter("cs_app_slug", [$this, 'appSlug']);
    add_filter("cs_app_url", [$this, 'appUrl']);
  }

  public function defaults() {
    return [
      'custom_app_slug'  => '',
      'themeless'        => true
    ];
  }

  public function controls() {
    return [
      [
        'key' => 'custom_app_slug',
        'type' => 'text',
        'title'       => __( 'Custom Path', 'cornerstone' ),
        'description' => __( 'Change the path used to load the main interface.', 'cornerstone' ),
        'options' => array(
          'placeholder' => apply_filters( 'cornerstone_default_app_slug', 'cornerstone' )
        ),
      ]
    ];
  }

  public function getAll() {
    if ( ! isset( $this->settings ) ) {
      $this->settings = wp_parse_args( get_option( 'cornerstone_settings', array() ), $this->defaults() );
    }
    return $this->settings;
  }

  public function get( $name ) {
    $all = $this->getAll();
    return isset( $all[$name] ) ? $all[$name] : null;
  }

  public function update($data) {
    $this->getAll();

    if ( isset( $data['custom_app_slug'] ) ) {
      $this->settings['custom_app_slug'] = sanitize_title_with_dashes( $data['custom_app_slug'] );
    }

		if ( isset( $data['themeless'] ) ) {
			$this->settings['themeless'] = is_bool($data['themeless']) ? $data['themeless'] : $data['themeless'] === 'true';
		}


		update_option( 'cornerstone_settings', $this->settings );
  }

  public function appSlug() {

    $customSlug = $this->get('custom_app_slug');
    $slug = apply_filters( 'cornerstone_default_app_slug', 'cornerstone' );

    if ( ! empty( $customSlug ) ) {
      $slug = sanitize_title_with_dashes( $customSlug );
    }

    return $slug;

  }

  public function appUrl() {
    $url = untrailingslashit( home_url( $this->appSlug() ) );

    // See WPML::filterAppURL
    // what it does there might be what we always want it to do
    $url = apply_filters( 'cs_filter_app_url', $url );

    return $url;
  }

  public function ajaxClean() {

		if ( ! current_user_can( 'manage_options' ) ) {
			return wp_send_json_error();
		}

    do_action( 'cs_purge_tmp' );
    do_action( 'cs_purge_all' );

		return wp_send_json_success();

	}

	public function ajaxSave( $data ) {

		if ( ! current_user_can( 'manage_options' ) ) {
			return wp_send_json_error();
		}

    // Update permissions
    if ( isset( $data['permissions'] ) ) {

      $permissions = json_decode( wp_unslash($data['permissions']), true );

      if ( is_null( $permissions ) ) {
        return wp_send_json_error(array('Unable to decode permissions', $data['permissions']));
      }


      $save_permissions = cornerstone('Permissions')->updateStoredPermissions( $permissions );

      if ( is_wp_error( $save_permissions ) ) {
        return wp_send_json_error( $save_permissions );
      }

      unset($data['permissions']);

    }

    // Settings
    $settings = apply_filters("cs_extension_settings", []);

    // Update extension settings
    if(isset($data['extensionOptions'])) {
      $data['extensionOptions'] = json_decode( wp_unslash($data['extensionOptions']), true );

      // Loop and update extension settings
      foreach ($data['extensionOptions'] as $key => $value) {
        // Check valid
        if (!isset($settings[$key])) {
          trigger_error("Extension option not set in filter " . $key);
          continue;
        }

        // Update option
        update_option($key, $value, true);
      }
    }

		if ( is_wp_error( $data ) ) {
			return wp_send_json_error( $data );
		}

		cornerstone('Settings')->update( $data );


		return wp_send_json_success();

	}

}
