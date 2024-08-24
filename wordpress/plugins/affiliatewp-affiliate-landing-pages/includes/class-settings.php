<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class AffiliateWP_Affiliate_Landing_Pages_Admin {

	/**
	 * Sets up the class.
	 *
	 * @access public
	 * @since  1.0
	 * @since 1.2.1 Supports settings sections.
	 */
	public function __construct() {

		$version = get_option( 'affwp_version' );

		// Keeps compatibility with older versions of AffiliateWP.
		if ( version_compare( $version, '2.18.0', '<' ) ) {
			add_filter( 'affwp_settings_tabs', array( $this, 'setting_tab' ) );
			add_filter( 'affwp_settings', array( $this, 'register_settings_legacy' ) );

			return;
		}

		// Use the new section system.
		add_filter( 'affwp_settings_affiliates', array( $this, 'register_settings' ) );
		add_action( 'affiliatewp_after_register_admin_sections', array( $this, 'register_section' ) );
	}

	/**
	 * Register the new settings tab.
	 *
	 * @since 1.0
	 *
	 * @param array $tabs The array of tabs.
	 *
	 * @return array
	 */
	public function setting_tab( array $tabs ) : array {
		$tabs['affiliate-landing-pages'] = __( 'Affiliate Landing Pages', 'affiliatewp-affiliate-landing-pages' );
		return $tabs;
	}

	/**
	 * Register the admin section.
	 *
	 * @since 1.2.1
	 */
	public function register_section() {

		if ( ! method_exists( affiliate_wp()->settings, 'register_section' ) ) {
			return; // It is an old AffiliateWP and do not have support for sections.
		}

		affiliate_wp()->settings->register_section(
			'affiliates',
			'addon_landing_pages',
			__( 'Affiliate Landing Pages', 'affiliate-wp' ),
			apply_filters(
				'affiliatewp_register_section_addon_landing_pages',
				array(
					'affiliate-landing-pages',
					'affiliate-landing-pages-post-types',
				)
			),
			sprintf(
				wp_kses( /* translators: %s - AffiliateWP.com Affiliate Landing Pages URL. */
					__( 'Assign pages or posts to specific affiliates. <a href="%s" target="_blank" rel="noopener noreferrer">Read our documentation</a> to learn more.', 'affiliate-wp' ),
					array(
						'a' => array(
							'href'   => array(),
							'target' => array(),
							'rel'    => array(),
						),
					)
				),
				affwp_utm_link( 'https://affiliatewp.com/docs/affiliate-landing-pages-installation-and-usage/', 'settings-affiliates', 'Affiliate Landing Pages Documentation' )
			),
			array(),
			'table',
			true
		);
	}

	/**
	 * Return the list of settings.
	 *
	 * @since 1.2.1
	 *
     * @param array $filter_by If supplied, will return only the specified settings.
	 *
	 * @return array
	 */
	private function get_settings( array $filter_by = array() ) : array {

		$settings = array(
			'affiliate-landing-pages' => array(
				'name' => __( 'Enable', 'affiliate-wp' ),
				'desc' => __( 'Enable Affiliate Landing Pages. This will allow a page or post to be assigned to an affiliate.', 'affiliate-wp' ),
				'type' => 'checkbox',
				'education_modal' => array(
					'enabled'       => ! affwp_can_access_pro_features(),
					'name'          => __( 'Affiliate Landing Pages', 'affiliate-wp' ),
					'utm_content'   => __( 'Affiliate Landing Pages', 'affiliate-wp' ),
					'require_addon' => array(
						'id'   => 167098,
						'name' => 'affiliate-landing-pages',
						'path' => 'affiliatewp-affiliate-landing-pages/affiliatewp-affiliate-landing-pages.php',
					)
				),
			),
			'affiliate-landing-pages-post-types' => array(
				'name'    => __( 'Post Types', 'affiliatewp-affiliate-landing-pages' ),
				'desc'    => __( 'Select which post types support landing pages.', 'affiliatewp-affiliate-landing-pages' ),
				'type'    => 'multicheck',
				'options' => $this->gather_post_types(),
				'class'   => affiliate_wp()->settings->get( 'affiliate-landing-pages' ) ? '' : 'affwp-hidden',
				'visibility' => array(
					'required_field' => 'affiliate-landing-pages',
					'value'          => true,
				),
			)
		);

		if ( empty( $filter_by ) ) {
			return $settings; // No filters, return all settings.
		}

		// Return only specific settings.
		return array_intersect_key( $settings, array_flip( $filter_by ) );
	}

	/**
	 * Register the settings for our Affiliate Landing Pages
	 *
	 * @since 1.0
	 *
	 * @param array $settings The array of settings.
	 *
	 * @return array
	 */
	public function register_settings( array $settings ) : array {

		return array_merge_recursive(
			$settings,
			$this->get_settings( array( 'affiliate-landing-pages-post-types' ) )
		);
	}

	/**
	 * Register the settings for our Landing Pages tab for older AffiliateWP versions (prior to 2.18.0).
	 *
	 * @since 1.0
	 *
	 * @since 1.2.1 It know uses the get_settings() method to get all settings.
	 *
	 * @param array $settings The array of settings.
	 *
	 * @return array
	 */
	public function register_settings_legacy( array $settings ) : array {

		$settings['affiliate-landing-pages'] = $this->get_settings();

		return $settings;
	}

	/**
	 * Gathers a list of public post types.
	 *
	 * @since 1.0.3
	 *
	 * @return array List of valid post types.
	 */
	private function gather_post_types() : array {

		$results    = array();
		$post_types = get_post_types( array( 'public' => true ), 'objects' );

		foreach ( $post_types as $post_type ) {
			$results[ $post_type->name ] = $post_type->label;
		}

		unset( $results['attachment'] );

		/**
		 * Filters the post types to display in the landing page admin.
		 *
		 * @since 1.0.3
		 *
		 * @param array $results List of public post type slugs keyed by their name.
		 */
		return apply_filters( 'affwp_alp_admin_post_types', $results );
	}

}

new AffiliateWP_Affiliate_Landing_Pages_Admin();
