<?php namespace RealTimeAutoFindReplacePro\admin\options\functions;

/**
 * Class: Coin LIst
 *
 * @package Admin
 * @since 1.0.0
 * @author M.Tuhin <info@codesolz.net>
 */

if ( ! defined( 'CS_BFRP_VERSION' ) ) {
	die();
}

use RealTimeAutoFindReplace\lib\Util;
use RealTimeAutoFindReplace\admin\functions\Masking;
use RealTimeAutoFindReplacePro\functions\advScreenOptions\ScreenOptions;

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

class AllRestoreDbData extends \WP_List_Table {
	var $item_per_page;
	var $total_post;

	public function __construct() {
		parent::__construct(
			array(
				'singular' => __( 'restore_item', 'real-time-auto-find-and-replace' ),
				'plural'   => __( 'restore_items', 'real-time-auto-find-and-replace' ),
				'ajax'     => false,
			)
		);

		$per_page = ScreenOptions::bfrp_get_dblogs_per_page();
		$this->item_per_page = empty($per_page) ? 10 : $per_page;
	}

	/**
	 *
	 * @return typeGenerate column
	 */
	public function get_columns() {
		return array(
			'cb'            => '<input type="checkbox" />',
			'tbl'           => __( 'Table', 'real-time-auto-find-and-replace' ),
			'column'        => __( 'Column', 'real-time-auto-find-and-replace' ),
			'original_data' => __( 'Original Data', 'real-time-auto-find-and-replace' ),
			'replaced_with' => __( 'Replaced With', 'real-time-auto-find-and-replace' ),
			'replaced_on'   => __( 'Replaced On', 'real-time-auto-find-and-replace' ),
		);
	}

	/**
	 * Column default info
	 */
	function column_default( $item, $column_name ) {
		switch ( $column_name ) {
			case 'tbl':
			case 'column':
			case 'original_data':
			case 'replaced_with':
			case 'replaced_on':
				return $item->{$column_name};
			default:
				return '---'; // Show the whole array for troubleshooting purposes
		}
	}

	/**
	 * Column cb
	 */
	public function column_cb( $item ) {
		return sprintf( '<input type="checkbox" name="restore_id[]" value="%1$s" />', $item['id'] );
	}

	public function column_tbl( $item ) {
		$pCol  = isset( $item['data']->pCol ) ? $item['data']->pCol : '--';
		$html  = isset( $item['data']->tbl ) ? $item['data']->tbl : '--';
		$html .= isset( $item['data']->rid ) ? '<span class="badge badge-lightgreen">' . $pCol . '#' . $item['data']->rid . '</span>' : '--';
		$html .= '<div class="row-actions"><span class="edit">';
		$html .= '<a href="' . \wp_nonce_url( \admin_url( "admin.php?page=cs-bfar-restore-database&action=restore&restore_id[]={$item['id']}" ), 'bulk-' . $this->_args['plural'] ) . '">Restore</a>';
		$html .= '</span></div>';

		return $html;
	}

	public function column_replaced_on( $item ) {
		return isset( $item['replaced_on'] ) ? $item['replaced_on'] : '';
	}

	public function column_column( $item ) {
		return isset( $item['data']->col ) ? $item['data']->col : '--';
	}

	public function column_original_data( $item ) {
		return isset( $item['data']->old_val ) ? '<span title="' . esc_html( $item['data']->old_val ) . '">' . substr( \esc_html( $item['data']->old_val ), 0, 100 ) . '</span>...' : '--';
	}

	public function column_replaced_with( $item ) {
		return isset( $item['data']->new_val ) ? '<span title="' . esc_html( $item['data']->new_val ) . '">' . substr( \esc_html( $item['data']->new_val ), 0, 100 ) . '</span>...' : '--';
	}


	public function no_items() {
		_e( 'Sorry! No Data Found!', 'real-time-auto-find-and-replace' );
	}

	function get_views() {
		$all_link     = admin_url( 'admin.php?page=cs-bfar-restore-database' );
		$views['all'] = "<a href='{$all_link}' >All <span class='count'>({$this->total_post})</span></a>";
		return $views;
	}

	public function get_bulk_actions() {
		$actions = array(
			'delete'  => __( 'Delete', 'real-time-auto-find-and-replace' ),
			'restore' => __( 'Restore', 'real-time-auto-find-and-replace' ),
		);
		return $actions;
	}

	/**
	 * Extra table nav
	 */
	public function extra_tablenav( $which ) {
		if( has_action( 'rtafar_restoreindb_extra_tablenav' ) ){
			do_action( 'rtafar_restoreindb_extra_tablenav' );
		}
	}

	/**
	 * Get the data
	 *
	 * @global type $wpdb
	 * @return type
	 */
	private function poulate_the_data() {
		global $wpdb, $wapg_tables;
		$search = '';
		if ( isset( $_GET['s'] ) && ! empty( $skey = $_GET['s'] ) ) {
			$search = " where c.data like '%{$skey}%'";
		}

		if ( isset( $_GET['order'] ) ) {
			$order = $_GET['order'];
		} else {
			$order = 'c.id DESC';
		}

		$current_page = $this->get_pagenum();
		if ( 1 < $current_page ) {
				$offset = $this->item_per_page * ( $current_page - 1 );
		} else {
				$offset = 0;
		}

		$data   = array();
		$result = $wpdb->get_results(
			"SELECT * from `{$wpdb->prefix}rtafar_history` as c "
				. "$search "
				. " order by {$order} limit $this->item_per_page offset {$offset}"
		);

		if ( $result ) {
			foreach ( $result as $item ) {
				$data[] = array(
					'id'          => $item->id,
					'data'        => \json_decode( $item->data ),
					'replaced_on' => $item->replaced_on,
				);
			}
		}
		$total         = $wpdb->get_var( "select count(id) as total from {$wpdb->prefix}rtafar_history as c {$search} " );
		$data['count'] = $this->total_post = $total;

		return $data;
	}

	function process_bulk_action() {
		global $wpdb;
		  // security check!
		if ( isset( $_GET['_wpnonce'] ) && ! empty( $_GET['_wpnonce'] ) &&
			isset( $_GET['restore_id'] ) && !empty( $_GET['restore_id'] )
		) {

			$action = 'bulk-' . $this->_args['plural'];

			if ( ! wp_verify_nonce( $_GET['_wpnonce'], $action ) ) {
				wp_die( 'Nope! Security check failed!' );
			}

			$action = $this->current_action();

			$log_ids = Util::check_evil_script( $_GET['restore_id'] );
			switch ( $action ) :
				case 'delete':
					if ( $log_ids ) {
						foreach ( $log_ids as $log ) {
							$wpdb->delete( "{$wpdb->prefix}rtafar_history", array( 'id' => $log ) );
						}
					}
					$this->success_admin_notice( 'deleted' );
					break;
				case 'restore':
					// pre_print( $log_ids);
					if ( $log_ids ) {
						foreach ( $log_ids as $itemID ) {
							$item = $wpdb->get_row( $wpdb->prepare( "select * from `{$wpdb->prefix}rtafar_history` where id = %d ", $itemID ) );
							$item = \json_decode( $item->data );
							$wpdb->update( $item->tbl, array( $item->col => $item->old_val ), array( $item->pCol => $item->rid ) );
							$wpdb->delete( "{$wpdb->prefix}rtafar_history", array( 'id' => $itemID ) );
						}
					}
					$this->success_admin_notice( 'restored' );
					break;

			endswitch;
		}
		return;
	}

	public function success_admin_notice( $msg ) {
		?>
		<div class="updated">
			<p><?php echo sprintf( __( 'Item has been %s successfully!', 'real-time-auto-find-and-replace' ), $msg ); ?></p>
		</div>
		<?php
	}

	public function prepare_items() {
		$columns  = $this->get_columns();
		$hidden   = array();
		$sortable = $this->get_sortable_columns();

		// Column headers
		$this->_column_headers = array( $columns, $hidden, $sortable = '' );
		$this->process_bulk_action();

		$data  = $this->poulate_the_data();
		$count = $data['count'];
		unset( $data['count'] );
		$this->items = $data;

		 // Set the pagination
		$this->set_pagination_args(
			array(
				'total_items' => $count,
				'per_page'    => $this->item_per_page,
				'total_pages' => ceil( $count / $this->item_per_page ),
			)
		);
	}

}
