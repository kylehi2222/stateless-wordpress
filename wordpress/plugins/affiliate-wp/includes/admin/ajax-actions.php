<?php
/**
 * Admin: Ajax Action Handlers
 *
 * @package     AffiliateWP
 * @subpackage  Admin
 * @copyright   Copyright (c) 2021, Sandhills Development, LLC
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0.0
 */

/**
 * Handles ajax for retrieving a list of users via live search.
 *
 * @since 1.0.0
 */
function affwp_search_users() {
	if ( empty( $_REQUEST['term'] ) ) {
		wp_die( -1 );
	}

	if ( ! current_user_can( 'manage_affiliates' ) ) {
		wp_die( -1 );
	}

	$search_query = htmlentities2( trim( sanitize_text_field( $_REQUEST['term'] ) ) );

	/**
	 * Fires immediately prior to an AffiliateWP user search query.
	 *
	 * @since 2.0
	 *
	 * @param string $search_query The user search query.
	 */
	do_action( 'affwp_pre_search_users', $search_query );

	$args = array(
		'search_columns' => array( 'user_login', 'display_name', 'user_email' )
	);

	$found_users = false;

	if ( mb_strlen( $search_query ) > 2 ) {

		// Add search string to args.
		$args['search'] = '*' . mb_strtolower( $search_query ) . '*';

		// Get users matching search.
		$found_users = get_users( $args );
	}

	$status = isset( $_REQUEST['status'] ) ? mb_strtolower( htmlentities2( trim( $_REQUEST['status'] ) ) ) : 'bypass';

	$user_list = array();

	if ( $found_users ) {

		foreach ( $found_users as $user ) {

			$label     = empty( $user->user_email ) ? $user->user_login : "{$user->user_login} ({$user->user_email})";
			$affiliate = affwp_get_affiliate_by( 'user_id', $user->ID );

			if ( 'bypass' !== $status ) {

				switch ( $status ) {

					case 'none':

						if ( is_wp_error( $affiliate ) ) {

							$user_list[] = array(
								'label'   => $label,
								'value'   => $user->user_login,
								'user_id' => $user->ID
							);

						}

						break;
					case 'any':

						if ( ! is_wp_error( $affiliate ) ) {

							$user_list[] = array(
								'label'   => $label,
								'value'   => $user->user_login,
								'user_id' => $user->ID
							);

						}

						break;
					default:

						if ( ! is_wp_error( $affiliate ) && $status == $affiliate->status ) {

							$user_list[] = array(
								'label'   => $label,
								'value'   => $user->user_login,
								'user_id' => $user->ID
							);

						}

				}

			} else {

				$user_list[] = array(
					'label'   => $label,
					'value'   => $user->user_login,
					'user_id' => $user->ID
				);

			}
		}
	}

	wp_die( json_encode( $user_list ) );
}
add_action( 'wp_ajax_affwp_search_users', 'affwp_search_users' );

/**
 * Handles ajax for processing a single batch request.
 *
 * @since 2.0
 */
function affwp_process_batch_request() {
	// Batch ID.
	if ( ! isset( $_REQUEST['batch_id'] ) ) {
		wp_send_json_error( array(
			'error' => __( 'A batch process ID must be present to continue.', 'affiliate-wp' )
		) );
	} else {
		$batch_id = sanitize_key( $_REQUEST['batch_id'] );
	}

	// Nonce.
	if ( ! isset( $_REQUEST['nonce'] )
	     || ( isset( $_REQUEST['nonce'] ) && false === wp_verify_nonce( $_REQUEST['nonce'], "{$batch_id}_step_nonce") )
	) {
		wp_send_json_error( array(
			'error' => __( 'You do not have permission to initiate this request. Contact an administrator for more information.', 'affiliate-wp' )
		) );
	}

	// Attempt to retrieve the batch attributes from memory.
	if ( $batch_id && false === $batch = affiliate_wp()->utils->batch->get( $batch_id ) ) {
		wp_send_json_error( array(
			/* translators: Batch process ID */
			'error' => sprintf( __( '%s is an invalid batch process ID.', 'affiliate-wp' ), esc_html( $_REQUEST['batch_id'] ) )
		) );
	}

	$class      = isset( $batch['class'] ) ? sanitize_text_field( $batch['class'] ) : '';
	$class_file = isset( $batch['file'] ) ? $batch['file'] : '';

	if ( empty( $class_file ) ) {
		wp_send_json_error( array(
			/* translators: Formatted batch process ID */
			'error' => sprintf( __( 'An invalid file path is registered for the %1$s batch process handler.', 'affiliate-wp' ), "<code>{$batch_id}</code>" )
		) );
	} else {
		require_once $class_file;
	}

	if ( empty( $class ) || ! class_exists( $class ) ) {
		wp_send_json_error( array(
			/* translators: 1: Formatted batch processor class name, 2: Formatted batch process ID */
			'error' => sprintf( __( '%1$s is an invalid handler for the %2$s batch process. Please try again.', 'affiliate-wp' ),
				"<code>{$class}</code>",
				"<code>{$batch_id}</code>"
			)
		) );
	}

	$step = sanitize_text_field( $_REQUEST['step'] );

	/**
	 * Instantiate the batch class.
	 *
	 * @var \AffWP\Utils\Batch_Process\Export|\AffWP\Utils\Batch_Process\Base $process
	 */
	if ( isset( $_REQUEST['data']['upload']['file'] ) ) {

		// If this is an import, instantiate with the file and step.
		$file    = sanitize_text_field( $_REQUEST['data']['upload']['file'] );
		$process = new $class( $file, $step );

	} else {

		// Otherwise just the step.
		$process = new $class( $step );

	}

	// Garbage collect any old temporary data.
	if ( $step < 2 ) {
		$process->finish( $batch_id );
	}

	$using_prefetch = ( $process instanceof \AffWP\Utils\Batch_Process\With_PreFetch );

	// Handle pre-fetching data.
	if ( $using_prefetch ) {

		// Initialize any data needed to process a step.
		$data = isset( $_REQUEST['form'] ) ? $_REQUEST['form'] : array();

		$process->init( $data );
		$process->pre_fetch();
	}

	$step          = $process->process_step();
	$response_data = array( 'step' => $step );

	// Map fields if this is an import.
	if ( isset( $process->field_mapping ) && ( $process instanceof \AffWP\Utils\Importer\CSV ) ) {
		$response_data['columns'] = $process->get_columns();
		$response_data['mapping'] = $process->field_mapping;
	}

	if ( is_wp_error( $step ) ) {
		// Log the error with response data.
		affiliate_wp()->utils->log( "Batch Process {$batch_id} failed to complete.", $response_data );
		wp_send_json_error( $step );
	} else {
		// Finish and set the status flag if done.
		if ( 'done' === $step ) {
			$response_data['done']    = true;
			$response_data['message'] = $process->get_message( 'done' );

			if ( method_exists( $process, 'get_redirect_url' ) ) {
				$response_data['url'] = $process->get_redirect_url();
			}

			// If this is an export class and not an empty export, send the download URL.
			if ( method_exists( $process, 'can_export' ) ) {

				if ( ! $process->is_empty ) {
					$response_data['url'] = affwp_admin_url( 'tools', array(
						'step'         => $step,
						'nonce'        => wp_create_nonce( 'affwp-batch-export' ),
						'batch_id'     => $batch_id,
						'affwp_action' => 'download_batch_export',
					) );
				}
			}

			// Log the success message
			affiliate_wp()->utils->log( "Batch Process {$batch_id} completed.", $response_data );

			// Once all calculations have finished, run cleanup.
			$process->finish( $batch_id );
		} else {
			$response_data['done']       = false;
			$response_data['percentage'] = $process->get_percentage_complete();
		}

		wp_send_json_success( $response_data );
	}

}
add_action( 'wp_ajax_process_batch_request', 'affwp_process_batch_request' );

/**
 * Handles ajax for processing the upload step in single batch import request.
 *
 * @since 2.1
 */
function affwp_process_batch_import() {
	if ( ! function_exists( 'wp_handle_upload' ) ) {
		require_once( ABSPATH . 'wp-admin/includes/file.php' );
	}

	require_once AFFILIATEWP_PLUGIN_DIR . 'includes/admin/tools/import/class-batch-import-csv.php';

	if( ! wp_verify_nonce( $_REQUEST['affwp_import_nonce'], 'affwp_import_nonce' ) ) {
		wp_send_json_error( array( 'error' => __( 'Nonce verification failed', 'affiliate-wp' ) ) );
	}

	if( empty( $_FILES['affwp-import-file'] ) ) {
		wp_send_json_error( array( 'error' => __( 'Missing import file. Please provide an import file.', 'affiliate-wp' ), 'request' => $_REQUEST ) );
	}

	$accepted_mime_types = array(
		'text/csv',
		'text/comma-separated-values',
		'text/plain',
		'text/anytext',
		'text/*',
		'text/plain',
		'text/anytext',
		'text/*',
		'application/csv',
		'application/excel',
		'application/vnd.ms-excel',
		'application/vnd.msexcel',
	);

	if( empty( $_FILES['affwp-import-file']['type'] ) || ! in_array( strtolower( $_FILES['affwp-import-file']['type'] ), $accepted_mime_types ) ) {
		wp_send_json_error( array( 'error' => __( 'The file you uploaded does not appear to be a CSV file.', 'affiliate-wp' ), 'request' => $_REQUEST ) );
	}

	if( ! file_exists( $_FILES['affwp-import-file']['tmp_name'] ) ) {
		wp_send_json_error( array( 'error' => __( 'Something went wrong during the upload process, please try again.', 'affiliate-wp' ), 'request' => $_REQUEST ) );
	}

	// Let WordPress import the file. We will remove it after import is complete
	$import_file = wp_handle_upload( $_FILES['affwp-import-file'], array( 'test_form' => false ) );

	if ( $import_file && empty( $import_file['error'] ) ) {

		// Batch ID.
		if ( ! isset( $_REQUEST['batch_id'] ) ) {
			wp_send_json_error( array(
				'error' => __( 'A batch process ID must be present to continue.', 'affiliate-wp' )
			) );
		} else {
			$batch_id = sanitize_key( $_REQUEST['batch_id'] );
		}

		// Attempt to retrieve the batch attributes from memory.
		if ( $batch_id && false === $batch = affiliate_wp()->utils->batch->get( $batch_id ) ) {
			wp_send_json_error( array(
				/* translators: Batch process ID */
				'error' => sprintf( __( '%s is an invalid batch process ID.', 'affiliate-wp' ), esc_html( $_REQUEST['batch_id'] ) )
			) );
		}

		$class      = isset( $batch['class'] ) ? sanitize_text_field( $batch['class'] ) : '';
		$class_file = isset( $batch['file'] ) ? $batch['file'] : '';

		if ( empty( $class_file ) ) {
			wp_send_json_error( array(
				/* translators: Formatted batch process ID */
				'error' => sprintf( __( 'An invalid file path is registered for the %1$s batch process handler.', 'affiliate-wp' ), "<code>{$batch_id}</code>" )
			) );
		} else {
			require_once $class_file;
		}

		if ( ! class_exists( $class ) ) {
			wp_send_json_error( array(
				/* translators: 1: Formatted batch processor class name, 2: Formatted batch process ID */
				'error' => sprintf( __( '%1$s is an invalid handler for the %2$s batch process. Please try again.', 'affiliate-wp' ),
					"<code>{$class}</code>",
					"<code>{$batch_id}</code>"
				)
			) );
		}


		$import = new $class( $import_file['file'] );


		if( ! $import->can_import() ) {
			wp_send_json_error( array( 'error' => __( 'You do not have permission to import data', 'affiliate-wp' ) ) );
		}

		wp_send_json_success( array(
			'batch_id'  => $batch_id,
			'upload'    => $import_file,
			'first_row' => $import->get_first_row(),
			'columns'   => $import->get_columns(),
			'nonce'     => wp_create_nonce( "{$batch_id}_step_nonce" )
		) );

	} else {

		/**
		 * Error generated by _wp_handle_upload()
		 * @see _wp_handle_upload() in wp-admin/includes/file.php
		 */

		wp_send_json_error( array( 'error' => $import_file['error'] ) );
	}

	exit;
}
add_action( 'wp_ajax_process_batch_import', 'affwp_process_batch_import' );


/**
 * Handles ajax for determining if a user log in name is valid
 *
 * @since 2.1.4
 * @since 2.3   Added support for user_email parameter
 */
function affwp_check_user_login() {

	if ( ! current_user_can( 'manage_affiliates' ) ) {
		wp_die( -1 );
	}

	if ( ! empty( $_REQUEST['user'] ) ) {
		$user = sanitize_text_field( $_REQUEST['user'] );
	} elseif ( ! empty( $_REQUEST['user_email'] ) ) {
		$user = get_user_by( 'email', sanitize_text_field( $_REQUEST['user_email'] ) );
		if ( false !== $user ) {
			$user = $user->user_login;
		}
	} else {
		wp_die( -1 );
	}

	/**
	 * Fires immediately prior to an AffiliateWP user check.
	 *
	 * @since 2.1.4
	 *
	 * @param string $user The user login.
	 */
	do_action( 'affwp_pre_check_user', $user );

	$affiliate = affwp_get_affiliate( $user );

	if ( $affiliate ) {
		$response = array(
				'affiliate' => true,
				'user'      => $user,
				'url'       => esc_url( affwp_admin_url( 'affiliates', array(
						'affiliate_id' => $affiliate->ID,
						'action'       => 'edit_affiliate',
				) ) ),
		);
	} else {
		$response = array( 'affiliate' => false, 'user' => $user );

	}

	wp_send_json_success( $response );

}
add_action( 'wp_ajax_affwp_check_user_login', 'affwp_check_user_login' );

/**
 * Activate plugin.
 *
 * @since 2.9.5
 */
function affwp_activate_plugin() {

	// Run a security check.
	check_ajax_referer( 'affiliate-wp-admin', 'nonce' );

	if ( ! isset( $_POST['plugin'] ) || ! is_string( $_POST['plugin'] ) ) {
		wp_send_json_error( esc_html__( 'Could not activate the plugin. Plugin slug is empty.', 'affiliate-wp' ) );
	}

	if ( ! current_user_can( 'activate_plugins' ) ) {

		wp_send_json_error( esc_html__( 'Plugin activation is disabled for you on this site.', 'affiliate-wp' ) );
		exit;
	}

	$activate = activate_plugins( sanitize_text_field( wp_unslash( $_POST['plugin'] ) ) );

	if ( ! is_wp_error( $activate ) ) {
		wp_send_json_success( esc_html__( 'Plugin activated.', 'affiliate-wp' ) );
	}

	wp_send_json_error( esc_html__( 'Could not activate the plugin. Please activate it on the Plugins page.', 'affiliate-wp' ) );
}
add_action( 'wp_ajax_affwp_activate_plugin', 'affwp_activate_plugin' );

/**
 * Install addon.
 *
 * @since 2.9.5
 */
function affwp_install_plugin() {

	// Run a security check.
	check_ajax_referer( 'affiliate-wp-admin', 'nonce' );

	$generic_error = esc_html__( 'There was an error while performing your request.', 'affiliate-wp' );

	// Check if new installations are allowed.
	if ( ! current_user_can( 'install_plugins' ) ) {
		wp_send_json_error( $generic_error );
		exit;
	}

	$error = esc_html__( 'Could not install the plugin. Please download and install it manually.', 'affiliate-wp' );

	if ( empty( $_POST['plugin'] ) ) {
		wp_send_json_error( $error );
	}

	// Set the current screen to avoid undefined notices.
	set_current_screen( 'affiliates_page_affiliate-wp-settings' );

	// Prepare variables.
	$url = esc_url_raw( admin_url( 'plugins.php' ) );

	ob_start();

	$creds = request_filesystem_credentials( $url, '', false, false, null );

	// Hide the filesystem credentials form.
	ob_end_clean();

	// Check for file system permissions.
	if ( false === $creds || ! WP_Filesystem( $creds ) ) {
		wp_send_json_error( $error );
	}

	/*
	 * We do not need any extra credentials if we have gotten this far, so let's install the plugin.
	 */
	require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';

	require_once AFFILIATEWP_PLUGIN_DIR . 'includes/admin/class-plugin-silent-upgrader-skin.php';

	// Do not allow WordPress to search/download translations, as this will break JS output.
	remove_action( 'upgrader_process_complete', array( 'Language_Pack_Upgrader', 'async_upgrade' ), 20 );

	// Create the plugin upgrader with our custom skin.
	$installer = new Plugin_Upgrader( new Affiliate_WP_Silent_Upgrader_Skin() );

	// Error check.
	if ( empty( $_POST['plugin'] ) || ! method_exists( $installer, 'install' ) ) {
		wp_send_json_error( $error );
	}

	$installer->install( sanitize_text_field( wp_unslash( $_POST['plugin'] ) ) );

	// Flush the cache and return the newly installed plugin basename.
	wp_cache_flush();

	$plugin_basename = $installer->plugin_info();

	if ( empty( $plugin_basename ) ) {
		wp_send_json_error( $error );
	}

	// Check for permissions.
	if ( ! current_user_can( 'activate_plugins' ) ) {

		wp_send_json_success(
			array(
				'msg'          => esc_html__( 'Plugin installed.', 'affiliate-wp' ),
				'is_activated' => false,
				'basename'     => $plugin_basename,
			)
		);
	}

	// Activate the plugin silently.
	$activated = activate_plugin( $plugin_basename );

	if ( ! is_wp_error( $activated ) ) {

		wp_send_json_success(
			array(
				'is_activated' => true,
				'msg'          => esc_html__( 'Plugin installed & activated.', 'affiliate-wp' ),
				'basename'     => $plugin_basename,
			)
		);
	}

	// Fallback error just in case.
	wp_send_json_error(
		array(
			'msg'          => $generic_error,
			'is_activated' => false,
			'basename'     => $plugin_basename,
		)
	);
}
add_action( 'wp_ajax_affwp_install_plugin', 'affwp_install_plugin' );
