<?php namespace RealTimeAutoFindReplacePro\install;

/**
 * Installation
 *
 * @package Install
 * @since 1.0.0
 * @author M.Tuhin <info@codesolz.com>
 */

if ( ! defined( 'CS_BFRP_VERSION' ) ) {
	exit;
}


class Activate {

	/**
	 * Update DB
	 *
	 * @return void
	 */
	public static function bfrp_update_db() {
		$get_installed_db_version  = get_site_option( 'bfrp_db_version' );
		$get_installed_plugin_version = get_site_option( 'bfrp_plugin_version' );

		global $wpdb;
		
		if ( empty( $get_installed_db_version ) ) {

			$update_sqls = array_merge_recursive( self::bfar_tables(), self::addition_columns() );

			// update db
			if ( ! empty( $update_sqls ) ) {
				foreach ( $update_sqls as $sql ) {

					if ( $wpdb->query( $sql ) === false ) {
						continue;
					}
				}
			}

			// update plugin db version
			update_option( 'bfrp_db_version', CS_BFRP_DB_VERSION );

		} elseif ( \version_compare( $get_installed_db_version, CS_BFRP_DB_VERSION, '!=' ) ) {

			$update_sqls = array();

			if ( \version_compare( $get_installed_db_version, '1.0.1', '<' ) ) {
				$update_sqls = array(
					"ALTER TABLE `{$wpdb->prefix}rtafar_rules` ADD COLUMN unicode_modifier char(5) DEFAULT 0 AFTER whole_word",
				);
			}

			if ( \version_compare( $get_installed_db_version, '1.0.2', '<' ) ) {
				$update_sqls = array_merge_recursive(
					$update_sqls,
					array(
						"ALTER TABLE `{$wpdb->prefix}rtafar_rules` ADD COLUMN skip_base_url char(5) DEFAULT 0 AFTER unicode_modifier",
					)
				);
			}

			if ( \version_compare( $get_installed_db_version, '1.0.3', '<' ) ) {
				$update_sqls = array_merge_recursive(
					$update_sqls,
					array(
						"ALTER TABLE `{$wpdb->prefix}rtafar_rules` ADD COLUMN skip_css_url_external char(5) DEFAULT 0 AFTER skip_base_url",
						"ALTER TABLE `{$wpdb->prefix}rtafar_rules` ADD COLUMN skip_css_internal char(5) DEFAULT 0 AFTER skip_css_url_external",
						"ALTER TABLE `{$wpdb->prefix}rtafar_rules` ADD COLUMN skip_css_inline char(5) DEFAULT 0 AFTER skip_css_internal",
						"ALTER TABLE `{$wpdb->prefix}rtafar_rules` ADD COLUMN skip_js_url_external char(5) DEFAULT 0 AFTER skip_css_inline",
						"ALTER TABLE `{$wpdb->prefix}rtafar_rules` ADD COLUMN skip_js_internal char(5) DEFAULT 0 AFTER skip_js_url_external",
					)
				);
			}

			if ( \version_compare( $get_installed_db_version, '1.0.4', '<' ) ) {
				$update_sqls = array_merge_recursive( $update_sqls, self::bfar_tables() );
			}

			if ( \version_compare( $get_installed_db_version, '1.0.5', '<' ) ) {
				$update_sqls = array_merge_recursive(
					$update_sqls,
					array(
						"ALTER TABLE `{$wpdb->prefix}rtafar_rules` ADD COLUMN skip_pages text AFTER skip_js_internal",
					)
				);
			}

			if ( \version_compare( $get_installed_db_version, '1.0.6', '<' ) ) {
				$update_sqls = array_merge_recursive(
					$update_sqls,
					array(
						"ALTER TABLE `{$wpdb->prefix}rtafar_rules` ADD COLUMN skip_posts text AFTER skip_pages",
					)
				);
			}

			if ( \version_compare( $get_installed_db_version, '1.0.7', '<' ) ) {
				$update_sqls = array_merge_recursive(
					$update_sqls,
					array(
						"ALTER TABLE `{$wpdb->prefix}rtafar_rules` ADD COLUMN where_to_replace_page_ids text AFTER where_to_replace",
						"ALTER TABLE `{$wpdb->prefix}rtafar_rules` ADD COLUMN where_to_replace_post_ids text AFTER where_to_replace_page_ids",
					)
				);
			}

			if ( \version_compare( $get_installed_db_version, '1.0.8', '<' ) ) {
				$update_sqls = array_merge_recursive(
					$update_sqls,
					array(
						"ALTER TABLE `{$wpdb->prefix}rtafar_rules` ADD COLUMN html_tags_class tinytext  AFTER skip_pages",
						"ALTER TABLE `{$wpdb->prefix}rtafar_rules` ADD COLUMN html_tags_remove_attributes char(2) AFTER html_tags_class",
					)
				);
			}

			// pre_print( $update_sqls );

			// update db
			if ( ! empty( $update_sqls ) ) {
				foreach ( $update_sqls as $sql ) {
					if( true == self::column_exists( $sql ) ){
						continue;
					}
					else if ( @$wpdb->query( $sql ) === false ) {
						continue;
					}
				}
			}

			// update plugin db version
			update_option( 'bfrp_db_version', CS_BFRP_DB_VERSION );
			update_option( 'bfarp_plugin_version', CS_BFRP_VERSION );
		}

		return true;
	}


	/**
	 * Install DB
	 *
	 * @return void
	 */
	public static function on_activate() {
		global $wpdb;

		$update_sqls = \array_merge_recursive( self::bfar_tables(), self::addition_columns() );

		// pre_print($update_sqls);

		foreach ( $update_sqls as $sql ) {
			if( true == self::column_exists( $sql ) ){
				continue;
			}
			else if ( @$wpdb->query( $sql ) === false ) {
				continue;
			}
		}

		// add db version to db
		add_option( 'bfarp_plugin_version', CS_BFRP_VERSION );
		add_option( 'bfrp_db_version', CS_BFRP_DB_VERSION );
		add_option( 'bfarp_plugin_install_date', date( 'Y-m-d H:i:s' ) );
	}


	/**
	 * on deactivate
	 *
	 * @return void
	 */
	public static function on_deactivate() {
		// delete plugin data
		delete_option( 'bfarp_plugin_version' );
		delete_option( 'bfrp_db_version' );
	}


	/**
	 * Additional columns
	 *
	 * @return void
	 */
	private static function addition_columns(){
		global $wpdb;
		return array(
			"ALTER TABLE `{$wpdb->prefix}rtafar_rules` ADD COLUMN where_to_replace_page_ids text AFTER where_to_replace",
			"ALTER TABLE `{$wpdb->prefix}rtafar_rules` ADD COLUMN where_to_replace_post_ids text AFTER where_to_replace_page_ids",
			"ALTER TABLE `{$wpdb->prefix}rtafar_rules` ADD COLUMN bypass_rule_is_active char(5) DEFAULT 0 AFTER where_to_replace_post_ids",
			"ALTER TABLE `{$wpdb->prefix}rtafar_rules` ADD COLUMN bypass_rule_wrapped_first_char char(5) AFTER bypass_rule_is_active",
			"ALTER TABLE `{$wpdb->prefix}rtafar_rules` ADD COLUMN bypass_rule_wrapped_last_char char(5) AFTER bypass_rule_wrapped_first_char",
			"ALTER TABLE `{$wpdb->prefix}rtafar_rules` ADD COLUMN remove_bypass_wrapper char(5) DEFAULT 0 AFTER bypass_rule_wrapped_last_char",
			"ALTER TABLE `{$wpdb->prefix}rtafar_rules` ADD COLUMN case_insensitive char(5) DEFAULT 0 AFTER remove_bypass_wrapper",
			"ALTER TABLE `{$wpdb->prefix}rtafar_rules` ADD COLUMN whole_word char(5) DEFAULT 0 AFTER case_insensitive",
			"ALTER TABLE `{$wpdb->prefix}rtafar_rules` ADD COLUMN unicode_modifier char(5) DEFAULT 0 AFTER whole_word",
			"ALTER TABLE `{$wpdb->prefix}rtafar_rules` ADD COLUMN skip_base_url char(5) DEFAULT 0 AFTER unicode_modifier",
			"ALTER TABLE `{$wpdb->prefix}rtafar_rules` ADD COLUMN skip_css_url_external char(5) DEFAULT 0 AFTER skip_base_url",
			"ALTER TABLE `{$wpdb->prefix}rtafar_rules` ADD COLUMN skip_css_internal char(5) DEFAULT 0 AFTER skip_css_url_external",
			"ALTER TABLE `{$wpdb->prefix}rtafar_rules` ADD COLUMN skip_css_inline char(5) DEFAULT 0 AFTER skip_css_internal",
			"ALTER TABLE `{$wpdb->prefix}rtafar_rules` ADD COLUMN skip_js_url_external char(5) DEFAULT 0 AFTER skip_css_inline",
			"ALTER TABLE `{$wpdb->prefix}rtafar_rules` ADD COLUMN skip_js_internal char(5) DEFAULT 0 AFTER skip_js_url_external",
			"ALTER TABLE `{$wpdb->prefix}rtafar_rules` ADD COLUMN skip_pages text AFTER skip_js_internal",
			"ALTER TABLE `{$wpdb->prefix}rtafar_rules` ADD COLUMN skip_posts text AFTER skip_pages",
			"ALTER TABLE `{$wpdb->prefix}rtafar_rules` ADD COLUMN html_tags_class tinytext  AFTER skip_pages",
			"ALTER TABLE `{$wpdb->prefix}rtafar_rules` ADD COLUMN html_tags_remove_attributes char(2) AFTER html_tags_class",
		);
	}

	/**
	 * Tables
	 *
	 * @return void
	 */
	private static function bfar_tables() {
		global $wpdb;
		$charset_collate = $wpdb->get_charset_collate();
		return array(
			"CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}rtafar_history`(
				`id` int(11) NOT NULL auto_increment,
				`data` mediumtext,
				`replaced_on` datetime,
				PRIMARY KEY ( `id`)
			) $charset_collate",
			"CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}rtafar_search_temp`(
				`id` int(11) NOT NULL auto_increment,
				`tbl` varchar(512),
				`col` varchar(512),
				`pcol` varchar(512),
				`rid` int(11),
				`data` mediumtext,
				PRIMARY KEY ( `id`)
			) $charset_collate",
		);
	}

	/**
	 * Check Element exists
	 *
	 * @param [type] $sql
	 * @return void
	 */
	private static function column_exists( $sql ){

		//check creating table or column
		if( false !== \strpos( $sql, 'CREATE') ){
			return false;
		}

		if( \preg_match_all('/((TABLE) `(.*)`)/', $sql, $matches)) {
			$table = \array_unique($matches[3]);
			if( ! isset($table[0] ) || empty( $table[0] ) ){
				return false;
			}
			$table = \trim( $table[0] );
		}else{
			return false;
		}

		if( \preg_match_all('/((COLUMN) (.*)AFTER)/', $sql, $colMatch)) {
			$col = \array_unique($colMatch[3]);
			if( ! isset($col[0] ) || empty( $col[0] ) ){
				return false;
			}
			$col = \explode(' ', $col[0] );

			if( ! isset($col[0] ) || empty( $col[0] ) ){
				return false;
			}
			$col = \trim( $col[0] );
		}else{
			return false;
		}

		global $wpdb;

		$is_exists = $wpdb->get_var( $wpdb->prepare(
			"SHOW COLUMNS FROM {$table} LIKE %s", $col
		));

		return empty( $is_exists ) ? false : true;

	}

}
