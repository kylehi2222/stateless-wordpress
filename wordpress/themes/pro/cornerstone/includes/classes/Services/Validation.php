<?php

namespace Themeco\Cornerstone\Services;

use Themeco\Cornerstone\Util\AdminAjax;

class Validation implements Service {
  private $ajaxValidation;
  private $ajaxRevoke;
  private $ajaxRefresh;
  private $ajaxInstall;
  private $ajaxActivate;
  public $code;

  public function __construct(
    AdminAjax $ajaxValidation,
    AdminAjax $ajaxRevoke,
    AdminAjax $ajaxRefresh,
    AdminAjax $ajaxInstall,
    AdminAjax $ajaxActivate
  ) {
    $this->ajaxValidation = $ajaxValidation;
    $this->ajaxRevoke = $ajaxRevoke;
    $this->ajaxRefresh = $ajaxRefresh;
    $this->ajaxInstall = $ajaxInstall;
    $this->ajaxActivate = $ajaxActivate;
  }

  public function setup() {

		if ( ! is_admin() ) return;

    $this->ajaxValidation->setAction( 'validation' )->setHandler( [ $this, 'ajaxValidationHandler'] )->start();
    $this->ajaxRevoke->setAction( 'validation_revoke' )->setHandler( [ $this, 'ajaxRevokeHandler'] )->start();
    $this->ajaxRefresh->setAction( 'validation_refresh' )->setHandler( [ $this, 'ajaxRefresh'] )->start();
		add_action( 'admin_enqueue_scripts', array( $this, 'add_script_data' ), -100 );

    $this->ajaxInstall->setAction( 'extensions_install' )->setHandler( [ $this, 'ajax_tgmpa_install_plugin'] )->start();
    $this->ajaxActivate->setAction( 'extensions_activate' )->setHandler( [ $this, 'ajax_activate_plugin'] )->start();


    // This one is unused currrently
    //$this->ajaxValidation->setAction( 'extensions_deactivate' )->setHandler( [ $this, 'ajax_deactivate_plugin'] )->start();
	}

	public function add_script_data() {
		cornerstone( 'Admin' )->add_script_data( 'cs-validation', array( $this, 'script_data' ) );
		cornerstone( 'Admin' )->add_script_data( 'cs-validation-revoke', array( $this, 'script_data_revoke' ) );

		cornerstone( 'Admin' )->add_script_data( 'x-extension', [ $this, 'extensions_data' ] );
	}

	public function script_data() {
		return array(
			'verifying'   => csi18n('admin.validation-verifying'),
			'error'       => csi18n('admin.validation-couldnt-verify'),
			'notices'     => array(
				'validation-complete' => csi18n('admin.validation-congrats'),
			),
			'errorButton' => csi18n('admin.validation-go-back'),
		);
	}

	public function script_data_revoke() {
		return array(
			'confirm'  => csi18n('admin.validation-revoke-confirm'),
			'accept'   => csi18n('admin.validation-revoke-accept'),
			'decline'  => csi18n('admin.validation-revoke-decline'),
			'revoking' => csi18n('admin.validation-revoking'),
			'notices'  => array(
				'validation-revoked' => sprintf( csi18n('admin.validation-revoked'), 'https://theme.co/account/dashboard/' )
			)
		);
	}

  /**
  * For extension installing
  */
  public function extensions_data() {
    return [
      'maxPlugins' => array_values(apply_filters("cs_max_get_plugins", [])),
      'pluginsURI'          => admin_url( 'plugins.php' ),
      'error'               => __( 'Error encountered.', '__x__' ),
      'activate'            => __( 'Activate', '__x__' ),
      'activated'           => __( 'Installed & Activated', '__x__' ),
      'errorBack'           => __( 'Go Back', '__x__' ),
      'installing'          => __( 'Installing&hellip;', '__x__' ),
      'activating'          => __('Activating&hellip;', '__x__' ),
      'waiting-to-install'  => __( 'Waiting to install&hellip;', '__x__' ),
      'waiting-to-activate' => __( 'Waiting to activate&hellip;', '__x__' ),
    ];
  }

	public function ajaxValidationHandler() {

		if ( ! current_user_can( 'manage_options' ) || ! isset( $_POST['code'] ) || ! $_POST['code'] ) {
			wp_send_json_error( array( 'message' => 'No purchase code specified.' ) );
			return;
		}

		$this->code = sanitize_text_field( $_POST['code'] );

		$validator = tco_common()->validator( $this->code, 'cornerstone' );

		$validator->run();

		if ( $validator->has_connection_error() ) {
			wp_send_json_error( array( 'message' => $validator->connection_error_details() ) );
			return;
		}

		$response = $this->get_validation_response( $validator );

		if ( isset( $response['complete'] ) && $response['complete'] ) {
			$this->update_validation( $this->code );
		} else {
			$this->update_validation( false );
		}

		wp_send_json_success( $response );

	}

	private function getThemecoDomain() {
		return !defined("THEMECO_DOMAIN")
			? "https://theme.co"
			: \THEMECO_DOMAIN;
	}

	public function get_validation_response( $validator ) {
		$domain = $this->getThemecoDomain();

		// Purchase code is not valid
		if ( ! $validator->is_valid() ) {
			return array(
				'message' => csi18n('admin.validation-msg-invalid'),
				'button'  => csi18n('admin.validation-go-back'),
				'dismiss' => true,
			);
		}

		// Valid, but the purchase code isn't associated with an account.
		if ( ! $validator->is_verified() ) {
      return array(
        'message' => csi18n('admin.validation-msg-new-code'),
        'button'  => csi18n('admin.validation-login'),
        'url'     => add_query_arg( $this->out_params(), $domain . '/docs/product-validation/' )
      );
    }

    // Purchase code linked to an account, but doesn't have a site
    if ( ! $validator->has_site() ) {
      return array(
        'message' => csi18n('admin.validation-msg-cant-link'),
        'button'  => csi18n('admin.validation-manage-licenses'),
        'url'     => $domain . '/account/dashboard/',
        'dismiss' => true,
        'newTab'  => true
      );
    }

    // Purchase code linked, and site exists, but doesn't match this site.
    if ( ! $validator->site_match() ) {
      return array(
        'message' => csi18n('admin.validation-msg-in-use'),
        'button'  => csi18n('admin.validation-manage-licenses'),
        'url'     => $domain . '/account/dashboard/',
        'dismiss' => true,
        'newTab'  => true
      );
    }

    return array(
      'complete' => true,
      'message' => csi18n('admin.validation-congrats')
    );

  }

  public function out_params() {
    return array(
      'code'        => $this->code,
      'product'     => 'cornerstone',
      'siteurl'     => tco_common()->get_site_url(),
      'return-url'  => esc_url( admin_url( 'admin.php?page=cornerstone-home' ) )
    );
  }

  public function ajaxRevokeHandler() {

  	if ( ! current_user_can( 'manage_options' ) ) {
  		wp_send_json_error();
      return;
  	}

    $this->update_validation( false );
    wp_send_json_success();

  }

  public function update_validation( $code ) {

    if ( $code ) {
      update_option( 'cs_product_validation_key', $code );
    } else {
      delete_option( 'cs_product_validation_key' );
    }

    tco_common()->updates()->refresh();

  }

  public function preload_key() {
    $key = '';
    if ( isset( $_REQUEST['tco-key'] ) ) {
      $key = esc_html( $_REQUEST['tco-key'] );
    }
    return $key;
  }

  /**
  * Refresh packages and max
  */
  public function ajaxRefresh() {
    add_filter( 'themeco_update_api', function($args) {
      $args['api-key'] = get_option("cs_product_validation_key", "");
      return $args;
    });
    tco_common()->updates()->refresh(true);

    wp_send_json_success([]);
  }

  /**
  * Description overlay for plugins
  */
  public function preview_overlay( $box_class ) {
    $no = tco_common()->admin_icon( 'no' );
    $how = _e( 'How do I unlock this feature?', '__x__' );
    $howToUnlock = printf( __('overview', 'how-do-i-unlock' ), 'data-tco-focus="validation-input"');

    echo <<<HTML
      <div class="tco-overlay tco-overlay-box-content">
        <a class="tco-overlay-close" href="#" data-tco-toggle="{$box_class} .tco-overlay">{$no}</a>
        <h4 class="tco-box-content-title">{$how}</h4>
        <p>{$howToUnlock}</p>
      </div>
HTML;
  }

  /**
   * Install plugin
   */
  public function ajax_tgmpa_install_plugin() {

    $plugin = isset( $_POST['slug'] ) ? $_POST['slug'] : null;
    do_action("cs_tgma_install_plugin", $plugin);

  }

  /**
   * Activate plugin
   */
  public function ajax_activate_plugin() {

    if ( ! current_user_can( 'activate_plugins' ) || ! isset( $_POST['plugin'] ) || ! $_POST['plugin'] ) {
      wp_send_json_error( array( 'message' => 'No plugin specified' ) );
      return;
    }

    do_action("cs_tgma_activate_plugin", $_POST['plugin']);

  }

  /**
   * Deactivate plugin
   * not used currently
   */
  public function ajax_deactivate_plugin() {

    if ( ! current_user_can( 'activate_plugins' ) || ! isset( $_POST['plugin'] ) || ! $_POST['plugin'] ) {
      wp_send_json_error( array( 'message' => 'No plugin specified' ) );
    }

    // @TODO
    //wp_send_json_error( array( 'message' => 'No plugin specified' ) );

    //$deactivate = \deactivate_plugin( $_POST['plugin'] );;

    //if ( is_wp_error( $deactivate ) ) {
      //wp_send_json_error( array( 'message' => $deactivate->get_error_message() ) );
    //}

    //wp_send_json_success( array( 'plugin' => $_POST['plugin'] ) );

  }

}
