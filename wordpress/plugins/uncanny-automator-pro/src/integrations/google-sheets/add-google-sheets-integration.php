<?php

namespace Uncanny_Automator_Pro;

/**
 * Class Add_Google_Sheets_Integration
 */
class Add_Google_Sheets_Integration {

	/**
	 * Integration code
	 *
	 * @var string
	 */
	public static $integration = 'GOOGLE_SHEETS';

	/**
	 * Only load this integration and its triggers and actions if the related plugin is active
	 *
	 * @param $status
	 * @param $plugin
	 *
	 * @return bool
	 */
	public function plugin_active( $status, $plugin ) {

		return true;
	}

	/**
	 * Set the directories that the auto loader will run in
	 *
	 * @param $directory
	 *
	 * @return array
	 */
	public function add_integration_directory_func( $directory ) {

		$directory[] = dirname( __FILE__ ) . '/helpers';
		$directory[] = dirname( __FILE__ ) . '/triggers';
		$directory[] = dirname( __FILE__ ) . '/tokens';

		return $directory;
	}

	/**
	 * Register the integration by pushing it into the global automator object
	 */
	public function add_integration_func() {

		Automator()->register->integration(
			self::$integration,
			array(
				'name'     => 'Sheets Web App',
				'icon_svg' => plugins_url( 'src/integrations/google-sheets/img/google-sheet-icon.svg', AUTOMATOR_PRO_FILE ),
			)
		);
	}
}
