<?php

namespace DMS\Includes\Integrations\WCFM;

use DMS\Includes\Data_Objects\Setting;
use DMS\Includes\Integrations\SEO\Yoast\Seo_Yoast;

class WCFM {

	/**
	 * Singular name of the store
	 */
	const SINGULAR_NAME = 'wcfm_store';

	/**
	 * Plural name of the stores
	 */
	const PLURAL_NAME = 'wcfm_stores';

	/**
	 * WCFM class instance
	 *
	 * @var WCFM
	 */
	public static WCFM $instance;

	/**
	 * Define instance and hooks
	 *
	 * @return void
	 */
	public static function run() {
		$wcfm = self::get_instance();
		add_filter( 'dms_validate_object_id', array( $wcfm, 'validate_object_id' ), 10, 2 );
		add_filter( 'dms_available_content_types', array( $wcfm, 'add_shop_pages' ) );
		add_filter( 'dms_search_select_values', array( $wcfm, 'add_shop_pages_to_select' ) );
		add_filter( 'dms_allowed_object_types', array( $wcfm, 'add_wcfm_store_object_type' ) );
		add_filter( 'dms_mapping_value_name', array( $wcfm, 'add_wcfm_store_name' ), 10, 2 );
		add_filter( 'dms_mapping_scenarios_list', array( $wcfm, 'add_wcfm_store_mapping_scenario' ) );
	}

	/**
	 * Check store existence
	 *
	 * @param $is_validated
	 * @param $object_id
	 *
	 * @return mixed|true
	 */
	public function validate_object_id( $is_validated, $object_id ) {
		if ( ! empty( wcfmmp_get_store( $object_id )->get_name() ) ) {
			return true;
		}

		return $is_validated;
	}

	/**
	 * Get singleton instance.
	 *
	 * @return WCFM
	 */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Add wcfm_store pages to mapping possible types
	 *
	 * @param array $post_types
	 *
	 * @return array
	 */
	public function add_shop_pages( array $post_types ): array {
		$post_types[] = array(
			'name'  => self::PLURAL_NAME,
			'label' => __( 'WCFM Stores', 'domain-mapping-system' ),
		);

		return $post_types;
	}

	/**
	 * Add wcfm_store pages to mapping select box
	 *
	 * @param array $values
	 *
	 * @return array
	 */
	public function add_shop_pages_to_select( array $values ): array {
		$is_wcfm_enabled = Setting::find( 'dms_use_wcfm_stores' )->get_value();
		if ( $is_wcfm_enabled ) {
			if ( ! function_exists( 'wcfmmp_get_store' ) ) {
				return $values;
			}
			// Initialize an empty array to store all stores
			$stores = [];
			// Get all user IDs with the role 'wcfm_vendor'
			$vendor_users = get_users( [
				'role'   => 'wcfm_vendor',
				'fields' => 'ID',
			] );
			// Loop through each vendor user ID and get store details
			foreach ( $vendor_users as $vendor_id ) {
				$store = wcfmmp_get_store( $vendor_id );
				if ( $store ) {
					$stores[] = array(
						'title' => $store->get_shop_name(),
						'id'    => self::SINGULAR_NAME . '_' . $vendor_id,
					);
				}
			}
			$values[ __( 'WCFM Stores' ) ] = $stores;
		}

		return $values;
	}

	/**
	 * Allow wcfm_store object during mapping value saving
	 *
	 * @param array $types
	 *
	 * @return array
	 */
	public function add_wcfm_store_object_type( array $types ): array {
		$types[] = self::SINGULAR_NAME;

		return $types;
	}

	/**
	 * Add mapping value name
	 *
	 * @param string $name
	 * @param object $value
	 *
	 * @return string
	 */
	public function add_wcfm_store_name( string $name, object $value ): string {
		if ( $value->object_type == self::SINGULAR_NAME ) {
			$name = wcfmmp_get_store( $value->object_id )->get_shop_name();
		}

		return $name;
	}

	/**
	 * Define mapping scenario for wcfm_stores
	 *
	 * @param array $scenarios
	 *
	 * @return array
	 */
	public function add_wcfm_store_mapping_scenario( array $scenarios ): array {
		return array_merge( [ 'DMS\\Includes\\Frontend\\Scenarios\\WCFM_Store_Mapping' ], $scenarios );
	}
}
