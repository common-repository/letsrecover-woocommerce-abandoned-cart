<?php

namespace WPLRP\Inc\Settings\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @subpackage Admin_functions
 */
class Admin_functions {

	protected static $instance = null;

	private function __construct() {

		//add menu in the menu bar
		add_action('admin_menu',  array($this,'Letsrecover_menu'));

		add_action( 'admin_enqueue_scripts', array( $this, 'letsrecover_admin_scripts' ) );

		add_action( 'admin_init',  array( $this, 'save_settings')  );

		add_action( 'wp_ajax_get_push_info', array( $this, 'get_push_info') );
		add_action( 'wp_ajax_nopriv_get_push_info', array( $this, 'get_push_info' )); 

		if ( class_exists( 'WooCommerce' ) === false ) {
			add_action( 'admin_notices', array($this, 'woocommerce_not_found') );
		}
		if(version_compare(PHP_VERSION, '7.2') < 0){
			add_action( 'admin_notices', array($this, 'recommended_php_version_not_found') );
		}
		if (extension_loaded('gmp') === false) { 
			add_action( 'admin_notices', array($this, 'recommended_php_extension_not_found') );
		}

		if( get_transient('wplrp') ){
			add_action('admin_notices', array($this, 'wplrp_admin_notice'));
		}


	}
	

	public static function init() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}
	public function recovery_options( $option_name ){
		$options = get_option('wplrp_options');
		if( $options[$option_name] == '1' )
			return true;
		else
			return false;
	}

	public function woocommerce_not_found(){
		?>
			<div class="notice notice-error">
				<p><?php _e( 'Please install and activate WooCommerce plugin to use the Letsrecover WooCommerce Abandoned Cart Recovery plugin' ); ?></p>
			</div>
		<?php
	}
	public function recommended_php_version_not_found(){
		?>
			<div class="notice notice-error is-dismissible">
				<p><?php _e( 'Please upgrade your PHP version to atleast 7.2 for better performance of Letsrecover WooCommerce Abandoned Cart Recovery plugin' ); ?></p>
			</div>
		<?php
	}

	public function recommended_php_extension_not_found(){
		?>
			<div class="notice notice-error is-dismissible">
				<p><?php _e( 'Please install PHP GMP extension for better performance of Letsrecover WooCommerce Abandoned Cart Recovery plugin' ); ?></p>
			</div>
		<?php
	}

	public function wplrp_admin_notice(){
		$notice = get_transient('wplrp');
		?>
			<div class="notice notice-<?php _e($notice['type']) ?> is-dismissible">
				<p><?php _e($notice['message']) ?></p>
			</div>
		<?php
	}


	public function letsrecover_menu(){
		add_menu_page( 'LetsRecover','LetsRecover','manage_options', 'letsrecover-abandoned-carts',array($this,'dashboard'),'dashicons-cart');
		add_submenu_page('letsrecover-abandoned-carts','LetsRecover - Dashboard','Dashboard','manage_options','letsrecover-abandoned-carts',array($this,'dashboard'));
		add_submenu_page('letsrecover-abandoned-carts','LetsRecover - Abandoned Carts','Abandoned Carts','manage_options','abandoned-carts-page',array($this,'abandoned_carts_page'));

		add_submenu_page('letsrecover-abandoned-carts','LetsRecover - Subscribers','Subscribers','manage_options','letsrecover-subscribers', array($this,'subscribers_page'));
		add_submenu_page('letsrecover-abandoned-carts','LetsRecover - Notifications','Notifications','manage_options','letsrecover-notifications', array($this,'notifications'));
		add_submenu_page('letsrecover-abandoned-carts','LetsRecover - Settings','Settings','manage_options','letsrecover-templates', array($this,'templates_page'));

		add_submenu_page('options.php','LetsRecover Push Settings','Settings','manage_options','letsrecover-templates', array($this,'templates_page'));
		add_submenu_page('options.php','LetsRecover Push Settings','Settings','manage_options','letsrecover-optin-prompt', array($this,'prompt_page'));
		add_submenu_page('options.php','LetsRecover Send Manual Notification','Settings','manage_options','letsrecover-manual-notification', array($this,'send_manual_notification'));
		
	}

	public function letsrecover_admin_scripts(){
		wp_enqueue_style( 'letsrecover-css', WPLRP_URL . 'assets/css/letsrecover_admin.min.css' , '', '1.0.0', false);
		wp_enqueue_script( 'letsrecover-admin-js', WPLRP_URL . 'assets/js/letsrecover_admin.min.js', '', '1.0.0', false);
		if( 
			isset($_GET['page']) && 
			(
				$_GET['page'] == 'letsrecover-manual-notification' || 
				($_GET['page'] == 'letsrecover-templates' && isset($_GET['id'])) 
			) 
			){
			wp_enqueue_style( 'emojionearea-css', WPLRP_URL . 'assets/css/emojionearea.min.css' , '', '3.4.0', false);
			wp_enqueue_script( 'emojionearea-js', WPLRP_URL . 'assets/js/emojionearea.min.js', '', '3.4.0', false);
		}

	}


	public function settings_page(){
		include_once WPLRP_INCLUDES . 'page-menu.php';
		include_once WPLRP_INCLUDES . 'pages/settings/templates.php';
	}

	public function templates_page(){
		include_once WPLRP_INCLUDES . 'page-menu.php';
		if( ! isset($_GET['id']) )
			include_once WPLRP_INCLUDES . 'pages/settings/templates.php';
		else
			include_once WPLRP_INCLUDES . 'pages/settings/edit-template.php';
	}

	public function prompt_page(){
		include_once WPLRP_INCLUDES . 'page-menu.php';
		include_once WPLRP_INCLUDES . 'pages/settings/optin-prompt.php';
	}

	public function subscribers_page(){
		require_once WPLRP_INCLUDES . "classes/subscribers.php"; 
		include_once WPLRP_INCLUDES . 'pages/subscribers.php';
	}

	public function abandoned_carts_page(){
		require_once WPLRP_INCLUDES . "classes/abandoned_carts.php"; 
		include_once WPLRP_INCLUDES . 'pages/abandoned_carts.php';
	}

	public function dashboard(){
		$wplrp_web_push = get_option('wplrp_web_push'); 
		$stats = $wplrp_web_push['stats'];
		include_once WPLRP_INCLUDES . 'pages/dashboard.php';
	}

	public function notifications(){
		require_once WPLRP_INCLUDES . "classes/notifications.php"; 
		include_once WPLRP_INCLUDES . 'pages/notifications.php';
	}
	public function send_manual_notification(){
		if( ! $_GET['subscriber_id'] )
			return; 

		$subscriber_id = sanitize_text_field($_GET['subscriber_id']);
		global $wpdb;
		$subscriber = $wpdb->get_row($wpdb->prepare("select * from {$wpdb->prefix}letsrecover_subscriptions where id = %d and status = 1", $subscriber_id), ARRAY_A);
		include_once WPLRP_INCLUDES . 'pages/send-notification.php';
	}

	public function save_settings(){

		if( isset($_POST['wplrp_setup_nonce_field'])){
			if( ! wp_verify_nonce($_POST['wplrp_setup_nonce_field'], 'wplrp_nounce_action') ){
				return;
			}
			if ( ! current_user_can( 'manage_options' ) ) { 
				return;
			}
		
			$data = wc_clean( $_POST );
			if( isset($data['save_global_settings']) ){
				update_option('wplrp_options', array(
																	'enable_push' => sanitize_text_field($data['wplrp_enable_push']),
																	'enable_email' => sanitize_text_field($data['wplrp_enable_email']))
																);
			}

			if( isset($data['template_name']) ){
				$wplrp_web_push = get_option('wplrp_web_push'); 

				$values =  array(
									'template_name'	=> sanitize_text_field( $data['template_name'] ),
									'title'		=> sanitize_text_field( $data['title'] ),
									'message'	=> sanitize_text_field( $data['message'] ),
									'icon'		=> sanitize_text_field( $data['icon'] ),
									'image'		=> sanitize_text_field( $data['image'] ),
									'badge'		=> sanitize_text_field( $data['badge'] ),
									'url'		   => sanitize_text_field( $data['url'] ),
									'interval_time'	=> sanitize_text_field( $data['interval_time'] ),
									'interval_unit'	=> sanitize_text_field( $data['interval_unit'] ),
									'button_1_text'	=> sanitize_text_field( $data['button_1_text'] ),
									'button_1_url'		=> sanitize_text_field( $data['button_1_url'] ),
									'button_2_text'		=> sanitize_text_field( $data['button_2_text'] ),
									'button_2_url'		=> sanitize_text_field( $data['button_2_url'] ),
								);

				if( isset($data['next_template_id']) )
					$wplrp_web_push['templates'][$data['next_template_id']] = $values;
				else
					$wplrp_web_push['templates'][] = $values;

				update_option('wplrp_web_push', stripslashes_deep($wplrp_web_push));
				wp_redirect('?page=letsrecover-templates');
			}

			elseif( isset($data['wplrp_push_prompt']) ){
				$wplrp_web_push = get_option('wplrp_web_push'); 
				$wplrp_web_push['prompt'] =  array(
														'type'      							=> $data['type'],
														'message'								=> sanitize_text_field($data['message']),
														'message_color' 						=> $data['message_color'],
														'logo'									=> sanitize_url($data['logo']),
														'allow_button_text' 					=> sanitize_text_field($data['allow_button_text']),
														'allow_button_text_color'			=> $data['allow_button_text_color'],
														'allow_button_background_color'	=> $data['allow_button_background_color'],
														'dismiss_button_text'				=> sanitize_text_field($data['dismiss_button_text']),
														'dismiss_button_text_color'		=> $data['dismiss_button_text_color'],
														'dismiss_button_background_color'=> $data['dismiss_button_background_color'],
												);

				update_option('wplrp_web_push', stripslashes_deep($wplrp_web_push));
				wp_redirect('?page=letsrecover-optin-prompt');
			}



		}
	}

	function get_push_info(){
		if( ! ($_GET['cart_id']) )
			exit('Invalid cart ID');

		global $wpdb;
		$cart_id = sanitize_text_field($_GET['cart_id']);
		$result = $wpdb->get_results( $wpdb->prepare("select date_time, payload, delivered, clicked, closed, success from {$wpdb->prefix}letsrecover_notifications where cart_id = %d order by id desc", array($cart_id)) , ARRAY_A);
		$data = '';
		foreach($result as $r){
			$payload = unserialize($r['payload']);
			$date = wp_date( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), strtotime( $r['date_time'] ) );

			if( $r['success'] )
				$status = 'sent';
			else
				$status = 'failed';

			if( $r['clicked'] )
				$status = 'clicked';
			elseif( $r['closed'] )
				$status = 'closed';
			elseif( $r['delivered'] )
				$status = 'delivered';

			$data .= "<li><span>$date</span><span>$payload[t]</span><span>$payload[m]</span><span class='notification-status wplrp-tooltip $status '></span></li>";
		}
		_e($data);
		exit();
	}




}



