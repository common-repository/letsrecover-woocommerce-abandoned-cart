<?php

namespace WPLRP\Inc\Reports;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

/**
 * @subpackage Notifications
 */
class Notifications extends \WP_List_Table{

	protected static $instance = null;

	public function __construct() {
		parent::__construct( [
			'singular' => 'Notification', 
			'plural'   => 'Notifications',
			'ajax'     => false //should this table support ajax?

		] );
	}

	public static function init() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Override the parent columns method. Defines the columns to use in your listing table
	 *
	 * @return Array
	 */
	public function get_columns(){
		$columns = array(
			'cb'      		=> '<input type="checkbox" />',
			'id'				=> 'ID',
			'date_time'		=> 'Notification Date',
			'payload'		=> 'Notification Title',
			'payload_message'		=> 'Notification Message',
		);

		if( ! isset($_GET['filter']) || $_GET['filter'] != 'grouped' ){
			$columns['subscriber_id']= 'Subscriber ID';
			$columns['cart_id']	 	= 'Cart ID';
			$columns['success'] 		= 'Status';
		}else{
			$columns['total_notifications'] 		= 'Total';
			$columns['total_sent'] 		= 'Success';
		}
			
		$columns['delivered']= 'Delivered';
		$columns['clicked']	= 'Clicked';
		$columns['closed']	= 'Closed';

		return $columns;
	}

	/**
	 * Columns to make sortable.
	 *
	 * @return array
	 */
	public function get_sortable_columns() {
		$sortable_columns = array(
			'id' => array( 'id', false ),
		);

		return $sortable_columns;
	}

	/**
	 * Render a column when no column specific method exists.
	 *
	 * @param array $item
	 * @param string $column_name
	 *
	 * @return mixed
	 */
	public function column_default( $item, $column_name ) {
		switch ( $column_name ) {

			case 'date_time':
				$date_fm  = get_option( 'date_format' ) . ' ' . get_option( 'time_format' );
				return wp_date( $date_fm, strtotime($item[ $column_name ]) );

			case 'success':
				return $item[ $column_name ] == 1 ? '<span class="wplrp-green-text" >Sent</span>' : '<span class="wplrp-red-text">Failed</span>';

			case 'payload':
				$palyload = unserialize($item[ $column_name ]);
				return $palyload['t'];

			case 'payload_message':
				$palyload = unserialize($item[ $column_name ]);
				return $palyload['m'];

			case 'clicked':
			case 'closed':
			case 'delivered':

				if(  isset($_GET['filter']) && $_GET['filter'] == 'grouped' ){
					return $item[ $column_name ];
			
				}else{
					if( $item[ $column_name ] )
						return "<span class='stats'></span>";
					else
						return "";
				}
			
			case 'total_notifications':
			case 'total_sent':
				return $item[ $column_name ];


			default:
				return $item[ $column_name ];
		}
	}

	/**
	 * Returns an associative array containing the bulk action
	 *
	 * @return array
	 */
	public function get_bulk_actions() {
		$actions = [
			'bulk-delete' => 'Delete'
		];

		return $actions;
	}

	public function process_bulk_action() {
		
		// If the delete bulk action is triggered
		if ( ( isset( $_POST['action'] ) && $_POST['action'] == 'bulk-delete' )
				|| ( isset( $_POST['action2'] ) && $_POST['action2'] == 'bulk-delete' )
		) {


			if( is_array($_POST['bulk-delete']) && ! empty($_POST['bulk-delete']) ){
				$delete_ids = sanitize_text_field($_POST['bulk-delete']);

				// loop over the array of record IDs and delete them
				foreach ( $delete_ids as $id ) {
					if( (int) $id  )
						self::delete_notification( $id );

				}
			}
			wp_redirect( esc_url( add_query_arg() ) );
			exit;
		}
	}


	/**
	 * Render the bulk edit checkbox
	 *
	 * @param array $item
	 *
	 * @return string
	 */
	function column_cb( $item ) {
		return sprintf(
			'<input type="checkbox" name="bulk-delete[]" value="%s" />', $item['id']
		);
	}

	/**
	 * Prepare the items for the table to process
	 *
	 * @return Void
	 */
	public function prepare_items(){ 
		$this->_column_headers = array($this->get_columns(), array(), $this->get_sortable_columns());

		/** Process bulk action */
		$this->process_bulk_action();

		$per_page     	= 10;
		$current_page 	= $this->get_pagenum();

		//get data with limit and total records found
		$data 			= self::get_notifications( $per_page, $current_page );
		$total_items  	= $data['total']['records'];

		$this->set_pagination_args( [
			'total_items' => $total_items, //WE have to calculate the total number of items
			'per_page'    => $per_page //WE have to determine how many items to show on a page
		] );

		$this->items = $data['rows'];

	}


	/**
	 * Retrieve abandoned cart's data from the database
	 *
	 * @param int $per_page
	 * @param int $page_number
	 *
	 * @return mixed
	 */
	public static function get_notifications( $per_page = 20, $page_number = 1 ) {

		global $wpdb;

		$sql = "SELECT SQL_CALC_FOUND_ROWS *, payload as payload_message FROM {$wpdb->prefix}letsrecover_notifications ORDER BY id ";

		if(  isset($_GET['filter']) && $_GET['filter'] == 'grouped' )
			$sql = "SELECT SQL_CALC_FOUND_ROWS id, date_time, payload, payload as payload_message,count(*) as total_notifications, sum(success) total_sent, sum(delivered) as delivered, sum(clicked) as clicked, sum(closed) as closed FROM {$wpdb->prefix}letsrecover_notifications GROUP BY template_md5 ORDER BY id";

		$sql .= ! empty( $_REQUEST['order'] ) ? ' ' . esc_sql( (int) $_REQUEST['order'] ) : ' DESC';

		$sql .= " LIMIT $per_page";

		$sql .= ' OFFSET ' . ( $page_number - 1 ) * $per_page;

		$result['rows'] = $wpdb->get_results( $sql, 'ARRAY_A' );

		$result['total'] = $wpdb->get_row( "select FOUND_ROWS() as records;", 'ARRAY_A' );

		return $result;

	}



	/**
	 * Delete abandoned cart's data from the database
	 *
	 * @param int $id
	 *
	 */
	public static function delete_notification( $id  ) {
		global $wpdb;
		$wpdb->delete(
			"{$wpdb->prefix}letsrecover_notifications",
			[ 'id' => $id ],
			[ '%d' ]
		);		

	}

}



