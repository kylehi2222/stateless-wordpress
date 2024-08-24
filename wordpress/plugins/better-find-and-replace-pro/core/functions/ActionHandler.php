<?php namespace RealTimeAutoFindReplacePro\functions;

/**
 * Class: Register custom menu
 *
 * @package Action
 * @since 1.0.0
 * @author M.Tuhin <info@codesolz.net>
 */

if ( ! defined( 'CS_BFRP_VERSION' ) ) {
	die();
}

use RealTimeAutoFindReplace\admin\functions\DbReplacer;
use RealTimeAutoFindReplace\lib\Util;
use RealTimeAutoFindReplace\lib\RTAFAR_DB;
use RealTimeAutoFindReplacePro\lib\CsMysql;


class ActionHandler {

	/**
	 * custom URL replacer
	 *
	 * @param [type] $settings
	 * @return void
	 */
	public static function bfrpCustomUrlReplacer( $settings, $inWhichUrl ) {
		global $wpdb;

		$DbReplacer = new DbReplacer( $settings );
		$userQuery  = $settings['cs_db_string_replace'];
		$findUrlIn  = $settings['url_options'];

		$find    = isset( $userQuery['find'] ) ? $userQuery['find'] : '';
		$replace = isset( $userQuery['replace'] ) ? $userQuery['replace'] : '';

		$i = 0;

		if ( $findUrlIn ) {

			$urlTypes = array();

			// find urls in post table's
			foreach ( $inWhichUrl as $value ) {
				if ( false !== \strpos( $value, 'tblp_' ) ) {
					continue;
				}
				$urlTypes[] = "post_type = '{$value}' ";
				if ( ( $key = array_search( $value, $inWhichUrl ) ) !== false ) {
					unset( $inWhichUrl[ $key ] );
				}
			}
			$i += $DbReplacer->urlFromPostTables( $find, $replace, $urlTypes );

			if ( $inWhichUrl ) {
				foreach ( $inWhichUrl as $urlType ) {
					if ( false === \strpos( $urlType, 'tblp_taxonomy' ) ) {
						continue;
					}
					$tax   = \str_replace( 'tblp_taxonomy_', '', $urlType );
					$terms = get_terms(
						array(
							'taxonomy'   => $tax,
							'hide_empty' => false,
						)
					);

					if ( $terms ) {
						foreach ( $terms as $item ) {

							$is_replaced = $DbReplacer->bfrReplace(
								$find,
								$replace,
								$item->slug,
								$wpdb->base_prefix . 'terms',
								$item->term_id, // row id
								'term_id',
								'slug',
								array( 'term_id' => $item->term_id )
							);

							if ( true === $is_replaced ) {
								$i++;
							}
						}
					}
				}
			}
		}

		return array(
			'i'            => $i,
			'dryRunReport' => $DbReplacer->dryRunReport,
		);

	}


	/**
	 * Find & Replace in custom tables
	 *
	 * @param [type] $settings
	 * @param [type] $tables
	 * @return void
	 */
	public static function bfrpCustomProTbls( $settings, $tables ) {
		if ( empty( $tables ) ) {
			return false;
		}

		global $wpdb;

		$DbReplacer = new DbReplacer( $settings );
		$userQuery  = $settings['cs_db_string_replace'];

		$find    = isset( $userQuery['find'] ) ? $userQuery['find'] : '';
		$replace = isset( $userQuery['replace'] ) ? $userQuery['replace'] : '';

		$mySqlReservedKey = CsMysql::reserveKeyWord();

		// pre_print( $userQuery );

		$table_info = array();
		$r = 0;
		foreach ( $tables as $table ) {

			// get table columns
			$columns = RTAFAR_DB::get_columns( $table );

			// pre_print( $columns );

			$sql        = "SELECT * from {$table} where ";
			$cols       = array();
			$keyPrimary = isset( $columns[0] ) ? $columns[0] : '';

			// empty primary key
			if ( empty( $keyPrimary ) ) {
				continue;
			}

			$tableInfo = RTAFAR_DB::get_info_of_tbl( $table );
			// pre_print( $tableInfo );
			if( isset( $userQuery['large_table'] ) && 'on' == $userQuery['large_table'] ) { 
				$table_info = array(
					'rows' => isset( $tableInfo['Auto_increment'] ) ? $tableInfo['Auto_increment'] : '',
					'size' => isset( $tableInfo['Data_length_mb'] ) ? $tableInfo['Data_length_mb'] : '',
				);
			}else{
				$table_info = \array_merge_recursive( $table_info, array(
					$table => array(
						'rows' => isset( $tableInfo['Rows'] ) ? $tableInfo['Rows'] : '',
						'size' => isset( $tableInfo['Data_length_mb'] ) ? $tableInfo['Data_length_mb'] : '',
					)
				));
			}

			// pre_print( ini_get('max_execution_time') );

			foreach ( $columns[1] as $column ) {

				if ( \in_array( \strtolower( $column ), $mySqlReservedKey ) ) {
					continue;
				}

				$cols[] = "{$column} like '%{$find}%'";
			}
			$sql .= \implode( ' OR ', $cols );

			// pre_print( $userQuery );

			//check large table
			$lgTblSearch_prevOffset = 0;
			$lgTblSearch_newOffset = 0;
			$largeTblSearchLimit = 10;
			if( isset( $userQuery['large_table'] ) && 'on' == $userQuery['large_table'] ) {
				$largeTblSearchLimit = isset( $settings['largeTblSearchLimit']) ? $settings['largeTblSearchLimit'] : $largeTblSearchLimit;
				$lgTblSearch_prevOffset = isset( $settings['largeTblSearchOffset']) ? (int) $settings['largeTblSearchOffset'] : $lgTblSearch_prevOffset;
				$lgTblSearch_newOffset = isset( $settings['largeTblSearchOffset']) ? (int) $lgTblSearch_prevOffset + (int) $largeTblSearchLimit : 0;

				//is searching prev
				if( isset( $settings['largeTblSearchPrev'] ) ){
					$lgTblSearch_newOffset = isset( $settings['largeTblSearchOffset']) ? (int) $lgTblSearch_prevOffset - (int) $largeTblSearchLimit : 0;
					$lgTblSearch_newOffset = $lgTblSearch_newOffset <= 0 ? 0 : $lgTblSearch_newOffset;
				}

				$sql .= " LIMIT {$largeTblSearchLimit} OFFSET {$lgTblSearch_newOffset}";
			}

			// pre_print( $sql );

			$getResults = $wpdb->get_results( $sql );
			if ( $getResults ) {
				foreach ( $getResults as $item ) {
					if ( empty( $item ) ) {
						continue;
					}

					foreach ( $item  as $key => $val ) {

						// replace in post_title
						$is_replaced = $DbReplacer->bfrReplace(
							$find,
							$replace,
							$val,
							$table,
							$item->{$keyPrimary}, //row id
							$keyPrimary,
							$key,
							array( $keyPrimary => $item->{$keyPrimary} )
						);

						if ( true === $is_replaced ) {
							$r++;
						}
					}
				}
			}
		}


		$largeTableData = array();
		if( isset( $userQuery['large_table'] ) && 'on' == $userQuery['large_table'] ) { 

			$lastRowID = 0;
			$isClickedPrev = array();
			if( isset( $settings['largeTblSearchPrev'] ) ){ 
				if( $lgTblSearch_newOffset <= 0 ){ 
					$lastRowID = 0;
				}else{
					// pre_print( $settings);
					$lastRowID = isset( $settings['largeTblSearchLastRowID'] ) ? $settings['largeTblSearchLastRowID'] : '';	
				}

				$isClickedPrev = array(
					'largeTblSearchPrev' => 1
				);

			}else{
				$lastRowID = isset( $settings['largeTblSearchLastRowID'] ) ? $settings['largeTblSearchLastRowID'] : '';
			}

			$largeTableData = array(
				'custom_data' => \array_merge_recursive( array(
					'prevOffset' => $lgTblSearch_prevOffset,
					'newOffset' => $lgTblSearch_newOffset,
					'limit' => $largeTblSearchLimit,
					'table_info' => $table_info,
					'lastRowID' => $lastRowID
				), $isClickedPrev )
			);
		}

		return \array_merge_recursive( array(
			'i'            => $r,
			'dryRunReport' => $DbReplacer->dryRunReport,
		), $largeTableData );

	}


	/**
	 * Replace db real-time
	 *
	 * @param [type] $userQuery
	 * @return void
	 */
	public function replaceSingleBtnClick( $userQuery ) {
		$row_data = \json_decode( Util::check_evil_script( $userQuery['row_data'] ), true );

		$res = false;
		if ( $row_data ) {
			$res = $this->dbReplacer( array( $row_data ) );
		}

		$res = \array_values( $res );

		return wp_send_json(
			array(
				'is_success' => false === $res[0] ? false : true,
			)
		);
	}


	/**
	 * Bulk replace
	 *
	 * @param [type] $userQuery
	 * @return void
	 */
	public function replaceBulkBtnClick( $userQuery ) {
		$selected_data = \json_decode( Util::check_evil_script( $userQuery['selected_data'] ), true );
		if ( $selected_data ) {
			$res = $this->dbReplacer( $selected_data );

			return wp_send_json(
				array(
					'is_success' => true,
					'rr'         => $res,
					'et'         => __( 'Error! Please refresh the page and try again. After refreshing, if you still see error; please activate WordPress debug mode and check debug log.', 'better-find-and-replace-pro' ),
				)
			);
		}

		return false;
	}



	/**
	 * Replace in db
	 *
	 * @param [type] $rows
	 * @return void
	 */
	private function dbReplacer( $rows ) {
		global $wpdb;

		if ( ! \is_array( $rows ) ) {
			return false;
		}

		$res = array();
		foreach ( $rows as $row ) {
			$row = (object) $row;

			$getVal = $wpdb->get_row(
				$wpdb->prepare(
					"SELECT * from `{$wpdb->prefix}rtafar_search_temp` WHERE tbl = %s and col = %s and rid = %s ",
					$row->tbl,
					$row->col,
					$row->rid
				)
			);

			if ( isset( $getVal->data ) && ! empty( $item = $getVal->data ) ) {
				$itemObj = \json_decode( $item );
				$wpdb->update( $row->tbl, array( $row->col => $itemObj->new_val ), array( $itemObj->pCol => $row->rid ) );
				// $res[ $row->rid ] = true;
				$res[ $row->rid . '---' . $row->col ] = true;
				do_action( 'bfar_save_item_history', $item );
			} else {
				$res[ $row->rid . '_' . $row->col ] = false;
			}

		}

		return $res;
	}


	/**
	 * Save Search Data to Temp Tbl
	 *
	 * @param [type] $search_report
	 * @return void
	 */
	public static function bfrpSaveDryRunItemTemp( $reports ) {
		
		// $reports = \json_decode( \stripslashes_deep( $search_report['form_data'] ), true );
		// pre_print($reports);
		
		// remove prev data
		self::bfrpEraseDryRunTempData();
		
		if ( empty( $reports ) ) {
			return false;
		}

		global $wpdb;
		
		foreach ( $reports as $key => $row ) {

			if ( empty( $row ) ) {
				continue;
			}
			if ( is_array( $row ) ) {
				foreach ( $row as $key => $item ) {

					if ( empty( $item ) || ! is_array( $item ) ) {
						continue;
					}

					$wpdb->insert(
						"{$wpdb->prefix}rtafar_search_temp",
						array(
							'tbl'  => isset( $item['tbl'] ) ? $item['tbl'] : '',
							'col'  => isset( $item['col'] ) ? $item['col'] : '',
							'pcol' => isset( $item['pCol'] ) ? $item['pCol'] : '',
							'rid'  => isset( $item['rid'] ) ? $item['rid'] : '',
							'data' => \json_encode( $item ),
						)
					);
				}
			}
		}

		return 'Saved Temp Data!';
	}


	/**
	 * Erase search data
	 *
	 * @return void
	 */
	public static function bfrpEraseDryRunTempData() {
		global $wpdb;

		// erase all previous data
		$wpdb->query( "TRUNCATE TABLE `{$wpdb->prefix}rtafar_search_temp`" );

		return true;
	}

	/**
	 * Log history
	 *
	 * @param [type] $log_history
	 * @return void
	 */
	public static function bfrpSaveItemHistory( $log_history ) {
		if ( empty( $log_history ) ) {
			return false;
		}

		global $wpdb;

		$wpdb->insert(
			"{$wpdb->prefix}rtafar_history",
			array(
				'data'        => $log_history,
				'replaced_on' => date( 'Y-m-d H:i:s' ),
			)
		);

		return $wpdb->insert_id;
	}

	/**
	 * Clear All Rules Button
	 *
	 * @return void
	 */
	public static function allmaskingrules_extra_tablenav_common(){
		global $wpdb;
		$is_item_exits = $wpdb->get_var( "select count(id) as total from `{$wpdb->prefix}rtafar_rules` ");
		if( empty($is_item_exits) ){
			return;
		}
		$html = '<div class="alignleft actions bfrp-common-tabs">';
		$html .= '<button class="button action bfrp-btn-clear-all" data-type="rules" type="button">' . __( 'Clear All', 'better-find-and-replace-pro' ) . '</button> ';
		echo $html . '</div>';
	}
	

	/**
	 * Clear all items
	 *
	 * @param [type] $userInput
	 * @return void
	 */
	public static function deleteAllItems( $userInput ){
		global $wpdb;

		// erase all data
		if( ! isset( $userInput['type'] ) || empty( $type = $userInput['type'] ) ){
			return wp_send_json(
				array(
					'status' => false,
					'title'         => __( 'Error!', 'better-find-and-replace-pro' ),
					'text'         => __( 'Item type not found.', 'better-find-and-replace-pro' ),
				)
			);
		}

		if( 'rules' == $type ) {
			$wpdb->query( "TRUNCATE TABLE `{$wpdb->prefix}rtafar_rules`" );
		}
		elseif( 'dblogs' == $type ) {
			$wpdb->query( "TRUNCATE TABLE `{$wpdb->prefix}rtafar_history`" );
		}


		return wp_send_json(
			array(
				'status' => true,
				'title'         => __( 'Deleted!', 'better-find-and-replace-pro' ),
				'text'         => __( 'Item(s) has been deleted successfully.', 'better-find-and-replace-pro' ),
			)
		);
	}


	/**
	 * Clear All Logs Button
	 *
	 * @return void
	 */
	public static function rtafar_restoreindb_extra_tablenav_common(){
		global $wpdb;
		$is_item_exits = $wpdb->get_var( "select count(id) as total from `{$wpdb->prefix}rtafar_history` ");
		if( empty($is_item_exits) ){
			return;
		}
		$html = '<div class="alignleft actions bfrp-common-tabs">';
		$html .= '<button class="button action bfrp-btn-clear-all" data-type="dblogs" type="button">' . __( 'Clear All', 'better-find-and-replace-pro' ) . '</button> ';
		echo $html . '</div>';
	}

}
