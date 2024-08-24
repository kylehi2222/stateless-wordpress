<?php namespace RealTimeAutoFindReplacePro\actions;

/**
 * Class: Custom ajax call
 *
 * @package Admin
 * @since 1.0.0
 * @author M.Tuhin <info@codesolz.net>
 */

if ( ! defined( 'CS_BFRP_VERSION' ) ) {
	die();
}


class BFRP_CustomAjax {

	function __construct() {
		add_action( 'wp_ajax_bfrp_ajax', array( $this, 'bfrp_ajax' ) );
		add_action( 'wp_ajax_nopriv_bfrp_ajax', array( $this, 'bfrp_ajax' ) );
	}


	/**
	 * custom ajax call
	 */
	public function bfrp_ajax() {

		if ( ! isset( $_REQUEST['cs_token'] ) || false === check_ajax_referer( SECURE_AUTH_SALT, 'cs_token', false ) ) {
			wp_send_json(
				array(
					'status' => false,
					'title'  => __( 'Invalid token', 'better-find-and-replace-pro' ),
					'text'   => __( 'Sorry! we are unable recognize your auth! here', 'better-find-and-replace-pro' ),
				)
			);
		}

		if ( ! isset( $_REQUEST['data'] ) && isset( $_POST['method'] ) ) {
			$data = $_POST;
		} else {
			$data = $_REQUEST['data'];
		}

		if ( empty( $method = $data['method'] ) || strpos( $method, '@' ) === false ) {
			wp_send_json(
				array(
					'status' => false,
					'title'  => __( 'Invalid Request', 'better-find-and-replace-pro' ),
					'text'   => __( 'Method parameter missing / invalid!', 'better-find-and-replace-pro' ),
				)
			);
		}
		$method     = explode( '@', $method );
		$class_path = str_replace( '\\\\', '\\', '\\RealTimeAutoFindReplacePro\\' . $method[0] );
		if ( ! class_exists( $class_path ) ) {
			wp_send_json(
				array(
					'status' => false,
					'title'  => __( 'Invalid Library', 'better-find-and-replace-pro' ),
					'text'   => sprintf( __( 'Library Class "%s" not found! ', 'better-find-and-replace-pro' ), $class_path ),
				)
			);
		}

		if ( ! method_exists( $class_path, $method[1] ) ) {
			wp_send_json(
				array(
					'status' => false,
					'title'  => __( 'Invalid Method', 'better-find-and-replace-pro' ),
					'text'   => sprintf( __( 'Method "%1$s" not found in Class "%2$s"! ', 'better-find-and-replace-pro' ), $method[1], $class_path ),
				)
			);
		}

		echo ( new $class_path() )->{$method[1]}( $data );
		exit;
	}

}


