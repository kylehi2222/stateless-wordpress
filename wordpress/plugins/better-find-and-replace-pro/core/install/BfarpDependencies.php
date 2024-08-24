<?php

/**
 * Check BFARP Dependencies
 *
 * @package Install
 * @since 1.1.0
 * @author M.Tuhin <info@codesolz.com>
 */

if ( ! function_exists( 'bfarpCheckDependencies' ) ) {

	function bfarpCheckDependencies() {

		// trigger notice
		add_action(
			'admin_notices',
			function() {
				?>
				<div class="notice cs-notice notice-error" >
					<p>
						<strong><?php echo CS_BFRP_PLUGIN_NAME; ?></strong>
					</p>
					<p>
					<?php
						echo sprintf(
							__(
								'In order to activate and use %2$s%1$s%3$s at first you need to keep installed & activate %4$s%2$sBetter Find and Replace%3$s%5$s',
								'better-find-and-replace-pro'
							),
							'Better Find and Replace Pro - Extension of Better Find and Replace',
							'<code>',
							'</code>',
							'<a href="https://wordpress.org/plugins/real-time-auto-find-and-replace/" target="_blank">',
							'</a>'
						);
					?>
					</p>

				</div>
				<?php
			}
		);

		if ( ! function_exists( 'deactivate_plugins' ) ) {
			require_once ABSPATH . '/wp-admin/includes/plugin.php';
		}

		deactivate_plugins( 'better-find-and-replace-pro/better-find-and-replace-pro.php', true );

		return false;
	}
}


if ( function_exists( 'add_action' ) ) {
	add_action(
		'admin_init',
		function() {
			if ( ! class_exists( 'Real_Time_Auto_Find_And_Replace' ) ) {
				bfarpCheckDependencies();
			}
		}
	);

}


if ( ! function_exists( 'has_core_bfar' ) ) {

	function has_core_bfar() {

		if ( ! function_exists( 'is_plugin_active' ) ) {
			require_once ABSPATH . '/wp-admin/includes/plugin.php';
		}

		if ( is_plugin_active( 'real-time-auto-find-and-replace/real-time-auto-find-and-replace.php' ) ) {
			return true;
		}

		return false;
	}
}

