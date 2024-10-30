<?php

namespace WPLRP\Inc\Reports;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

/**
 * @subpackage Abandoned_carts
 */
class Abandoned_carts extends \WP_List_Table{

	protected static $instance = null;
	public $date_fm;

	public function __construct() {
		parent::__construct( [
			'singular' => 'Abandoned Cart', 
			'plural'   => 'Abandoned Carts',
			'ajax'     => false //should this table support ajax?

		] );
		$this->date_fm  = get_option( 'date_format' ) . ' ' . get_option( 'time_format' );
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
			'id'				=> 'Cart ID',
			'date_time'		=> 'Date Time',
			'user_id' 		=> 'User ID',
			'subscriber_id'=> 'Subscriber ID',
			'cart_detail' 	=> 'Cart Detail',
			'status'			=> 'Cart Status',
			'push_sent' 	=> 'Notification Sent',
		);

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
			case 'status':
				return "<span class='wplrp-tooltip wplrp-cart-$item[$column_name]'>$item[$column_name]</span>";

			case 'date_time':
				return wp_date( $this->date_fm, strtotime($item[ $column_name ]) );
			
			case 'user_id':
			case 'subscriber_id':
			case 'id':
				return $item[ $column_name ];
			
			case 'push_sent':
				$link = '';
				if( $item['push_sent']  ){
					$push_items = "<ul class='wplrp-push-info'><li><span  class='spinner is-active'></span></li></ul>";
					$link .=  $item['push_sent'] . " <a data-cart-id='".$item['id']."' class='wplrp-push-sent' href='javascript:void(0);'>View</a>"  . $push_items ;
				}else{
					$link .= $item[ $column_name ];
				}
				return $link;
			
			case 'cart_detail':
				$cart_info = unserialize($item[ $column_name ]);
				if( ! $cart_info )
					return "";
				$items = "<ul class='wplrp-cart-info'>";
				foreach($cart_info['items'] as $item){
					$items .= "<li>$item</li>";
				}
				$items .= "</ul>";
				return "<a class='wplrp-cart-total' href='javascript:void(0);'>{$cart_info['total']}</a>"  . $items ;

			default:
				return print_r( $item, true ); //Show the whole array for troubleshooting purposes
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
					if(  (int) $id  )
						self::delete_cart( $id );
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
		$data 			= self::get_abandoned_carts( $per_page, $current_page );
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
	public static function get_abandoned_carts( $per_page = 20, $page_number = 1 ) {

		global $wpdb;

		$sql = "SELECT SQL_CALC_FOUND_ROWS * FROM {$wpdb->prefix}letsrecover_abandoned_cart ORDER BY id ";

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
	public static function delete_cart( $id  ) {
		global $wpdb;
		$wpdb->delete(
			"{$wpdb->prefix}letsrecover_abandoned_cart",
			[ 'id' => $id ],
			[ '%d' ]
		);		

	}
	

}



