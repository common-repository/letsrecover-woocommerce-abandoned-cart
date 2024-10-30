<?php

namespace WPLRP\Inc\Settings\Wp;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @subpackage Wp_functions
 */
class Wp_functions {

	protected static $instance = null;

	public $wplrp_web_push;
	private function __construct() {

		$this->wplrp_web_push = get_option('wplrp_web_push');

		if( ! $this->wplrp_web_push['templates'] )
			add_action( 'admin_notices', array($this, 'admin_notice_template_not_found') );

		add_action('wp_footer',  array($this,'insert_push_script'),10);
		add_action( 'admin_init',  array( $this, 'send_custom_notification')  );

		add_action( 'init', array($this,'add_sw_rewrite_rules') );
		add_action( 'parse_request', array($this,'generate_sw_url') );


		//ajax call to store subscriptions
		add_action( 'wp_ajax_letsrecover_save_subscription', array( $this, 'letsrecover_save_subscription') );
		add_action( 'wp_ajax_nopriv_letsrecover_save_subscription', array( $this, 'letsrecover_save_subscription' )); 

		add_action( 'wp_ajax_wplrp_notification_log', array( $this, 'wplrp_notification_log') );
		add_action( 'wp_ajax_nopriv_wplrp_notification_log', array( $this, 'wplrp_notification_log' )); 

		add_action( 'woocommerce_add_to_cart', array($this, 'store_cart_hook'));
		add_action( 'woocommerce_cart_item_removed', array($this, 'store_cart_hook'));
		add_action( 'woocommerce_cart_item_restored', array($this, 'store_cart_hook'));
		add_action( 'woocommerce_after_calculate_totals', array($this, 'store_cart_hook'));
		add_action( 'woocommerce_thankyou', array($this, 'update_abandoned_cart_status'), 10, 1);

		add_filter( 'cron_schedules', array($this,'wplpp_user_define_recurrence' ));
		// Schedule Cron Job Event
		add_action( 'wp', array($this,'wplpp_cron_job') );
		add_action('wplrp_cart_recovery_event', array($this, 'get_carts'));

		if( isset($_GET['letsrecover']) )
			$this->get_carts();

	}

	public static function init() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	public function admin_notice_template_not_found(){
		?>
			<div class="notice notice-error is-dismissible">
				<p><?php _e( 'No abandoned cart notification template found. Please <a href="/wp-admin/admin.php?page=letsrecover-templates">add the templates</a> to start sending abandoned cart reminder', 'sample-text-domain' ); ?></p>
			</div>
		<?php
	}

	// Custom Cron Recurrences
	function wplpp_user_define_recurrence( $schedules ) {
		$schedules['wplrp_cart_recover_interval'] = array(
			'display' => __( 'Every minute' ),
			'interval' => 60 ,
		);
		return $schedules;
	}

	function wplpp_cron_job() {
		if ( ! wp_next_scheduled( 'wplrp_cart_recovery_event' ) ) {
			wp_schedule_event( time(), 'wplrp_cart_recover_interval', 'wplrp_cart_recovery_event' );
		}
	}

	function add_sw_rewrite_rules() {
		add_rewrite_rule( "^/{letsrecover-sw.js}$","index.php?{letsrecover-sw.js}=1");
	}

	function generate_sw_url($query){
		if ( ! property_exists( $query, 'query_vars' ) || ! is_array( $query->query_vars ) ) {
			return;
		}	
		$query_vars_as_string = http_build_query( $query->query_vars );

		if ( strpos( $query_vars_as_string, 'letsrecover-sw.js' ) !== false ) {
			header( 'Content-Type: text/javascript' );
			_e("importScripts('" . WPLRP_URL . "assets/js/letsrecover_service_worker.min.js');");
			exit();
		}
	}
	

	public function insert_push_script(){
		?>
			<script id="letsrecover-push-sdk">(function(w,d, s, id) {
				w.letsrecover=w.letsrecover||function(){(w.letsrecover.q=w.letsrecover.q||[]).push(arguments)};
				var js, fjs = d.getElementsByTagName(s)[0];
				js = d.createElement(s); js.id = id;
				js.src = "<?php echo WPLRP_URL . 'assets/js/app.min.js?v1.0.0'; ?>";
				fjs.parentNode.appendChild(js);
				}(window,document, 'script', 'letsrecover-jssdk'));
				letsrecover('subscribe','<?php echo esc_attr($this->wplrp_web_push['vapid_keys']['public_key']); ?>','<?= site_url();?>','<?php echo esc_attr($this->wplrp_web_push['prompt']['type']); ?>','<?php if( in_array('super-progressive-web-apps/superpwa.php',get_option('active_plugins') ) ){ echo 'none'; } ?>');
				<?php global $woocommerce; if( ! $woocommerce->cart->is_empty( ) ){ ?>
					letsrecover('show_prompt');
				<?php } ?>
			</script>
		<?php		

		if( $this->wplrp_web_push['prompt']['type'] == 'custom'){
			require_once WPLRP_INCLUDES . "/custom-prompt.php";
		}

	}

	function letsrecover_save_subscription(){
		global $wpdb;

		$endpoint 		= sanitize_text_field($_POST['endpoint']);
		$method 			= sanitize_text_field($_POST['type']);
		$key 				= sanitize_text_field($_POST['key']);
		$token 			= sanitize_text_field($_POST['token']);
		$userIp 			= sanitize_text_field($_SERVER['REMOTE_ADDR']);
		$md5_endpoint 	= md5($endpoint);

		if (!isset($endpoint)) {
			_e('Error: not a subscription');
			exit();
		}
		if( strpos($endpoint,'https://android.googleapis.com/gcm/') !== false )
			exit('Invalid endpoint');


		//if update request
		if($method == 'PUT'){
			
			//check if current endpoint already exists
			$subscription_id = $wpdb->get_var( $wpdb->prepare("select id from {$wpdb->prefix}letsrecover_subscriptions where md5_endpoint = %s", $md5_endpoint) );
			if( $subscription_id ){

				//prepare push token, it includes endpint, key and token
				$values['push_token'] = serialize(array('endpoint' => $endpoint, 'key' => $key, 'token' => $token));
				$values['user_id'] = get_current_user_id();

				//update query
				$wpdb->update($wpdb->prefix .'letsrecover_subscriptions', 
								//set
								$values,  
								//where 
								array('id' 	=> $subscription_id),
								//values format
								array('%s', '%d'),
								//where format
								array('%d')
				);


			}
		}


		if( empty($subscription_id) ){
			//if it is new subscription with POST method or update failed with PUT method

			$response 		= wp_remote_get( "http://www.geoplugin.net/json.gp?ip=$userIp", array('headers' => array('referer' => home_url())) );
			$responseBody	= wp_remote_retrieve_body( $response );
			$result 			= (array) json_decode( $responseBody );
			if ( is_array( $result ) && ! is_wp_error( $result ) ) {
				$userCountry = @$result['geoplugin_countryName'];
			} else {
				$userCountry = "";
			}

			require_once WPLRP_INCLUDES . "libraries/user-agent.php";
			$user_agent = new \wplrp_user_agent();
			// create a new subscription entry in your database (endpoint is unique)
			$wpdb->insert(
				$wpdb->prefix . "letsrecover_subscriptions", 
				array(
						'date_time'	=> date('Y-m-d H:i'),
						'md5_endpoint'	=> $md5_endpoint,
						'push_token'	=> serialize(array('endpoint' => $endpoint, 'key' => $key, 'token' => $token)),
						'user_id' 	=> get_current_user_id(),
						'ip_address'=> $userIp,
						'platform'	=> $user_agent->platform(),
						'browser'	=> $user_agent->browser(),
						'country'	=> $userCountry,
						),
				array('%s','%s','%s','%s','%s','%s','%s','%s')
			);
			$subscription_id = $wpdb->insert_id; 
			
			//update subscribers stats
			$this->wplrp_web_push['stats']['subscribers']['total'] = $this->wplrp_web_push['stats']['subscribers']['total'] + 1;
			update_option('wplrp_web_push', $this->wplrp_web_push);
			
			$this->letsrecover_store_cart( $subscription_id );
		}

		setcookie('wplrp_subscriber_id', $subscription_id, (time() + 86400),"/");
		exit('subscription saved');
	}

	function store_cart_hook(){

		if( !  $_COOKIE['wplrp_subscriber_id']  )
			return;

		$cookie = sanitize_text_field($_COOKIE['wplrp_subscriber_id']);
		$subscriber_id = isset($cookie) ? $cookie : null;
		
		$this->letsrecover_store_cart( $subscriber_id );
	}

	function update_abandoned_cart_status( $order_id ){
		
		if( !  $_COOKIE['wplrp_subscriber_id']  )
			return;
		
		$cookie = sanitize_text_field($_COOKIE['wplrp_subscriber_id']);
		global $wpdb;
		$order 		= wc_get_order( $order_id );
		$user_id 	= $order->get_user_id();
		$subscriber_id = $cookie;



		//if any abandoned cart reminder notification sent to this user
		$cart_detail = $wpdb->get_row( $wpdb->prepare("select id, push_sent, cart_total from {$wpdb->prefix}letsrecover_abandoned_cart where subscriber_id = %d and status = 'Abandoned' ORDER BY id desc ", $subscriber_id ), ARRAY_A);
		if( $cart_detail ){

			/**
			 * if cart was abandoned
			 * and any notification sent to user
			 */
			if( $cart_detail['push_sent'] > 0 ){
				$wpdb->update("{$wpdb->prefix}letsrecover_abandoned_cart",
									array('status' => 'Recovered','user_id' => $user_id),
									array('id'		=> $cart_detail['id']),
									array('%s','%d'),
									array('%d')
				);

				//update USER_ID against subscriber
				$wpdb->update("{$wpdb->prefix}letsrecover_subscriptions",
									array('user_id' => $user_id),
									array('id'		=> $subscriber_id),
									array('%s'),
									array('%d')
				);

				//update cart stats options
				$this->wplrp_web_push['stats']['carts']['recovered'] = $this->wplrp_web_push['stats']['carts']['recovered'] + 1;
				$this->wplrp_web_push['stats']['carts']['revenue'] = $this->wplrp_web_push['stats']['carts']['revenue'] + $cart_detail['cart_total'];
				update_option('wplrp_web_push', $this->wplrp_web_push);

			}else{
				/**
				 * if it was a direct order. No abandoned cart notification sent
				 */
				$wpdb->delete("{$wpdb->prefix}letsrecover_abandoned_cart", array('id' => $cart_detail['id']) );
			}
		}

	}


	function letsrecover_store_cart( $subscriber_id ){
		global $wpdb;
		global $woocommerce;

		$product_image = "";
		$product_icon = "";

		if( ! $subscriber_id )
			return;

		if( ! $woocommerce->cart->is_empty( ) ){
			$items_count 	=  count( WC()->cart->get_cart() );
			$cart_total		= strip_tags($woocommerce->cart->get_cart_total());
			foreach($woocommerce->cart->get_cart() as $cart_item_key => $cart_item) {
				$_product =  wc_get_product( $cart_item['data']->get_id()); 
            $items[] =  $_product->get_title() . "(" . $cart_item['quantity'] . ") " . strip_tags(apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key )) ;
				
				//get only first availabled product's  image
				if( ! $product_image ){
					$image_id  = $_product->get_image_id();
					$product_icon 		= wp_get_attachment_image_url( $image_id, array(150,150) );
					$product_image 	= wp_get_attachment_image_url( $image_id, array(512,256) );
				}
			}
			$cart_detail	= array(
										'total' 			=> $items_count . ' item' . ($items_count > 1 ? 's' : '') . " ($cart_total)",
										'cart_total'	=> $cart_total,
										'items'			=> $items,
										'image'			=> array('img' => $product_image, 'icon' => $product_icon) ,
									);
			//check if cart already exists for current subscriber
			$cart = $wpdb->get_row( $wpdb->prepare("SELECT  `id` FROM {$wpdb->prefix}letsrecover_abandoned_cart where subscriber_id = %d AND `status` = 'Abandoned'", array($subscriber_id)), ARRAY_A );
			if( $cart ){
				$wpdb->update(
					$wpdb->prefix . "letsrecover_abandoned_cart", 
					array(
							'date_time' 	=> date('Y-m-d H:i:s'), 
							'user_id'		=> get_current_user_id(),
							'cart_detail' => serialize($cart_detail),
							'cart_total'	=> WC()->cart->total,
							),
					array(
						'subscriber_id'=> $subscriber_id,
						'status'			=> 'Abandoned',
					),
					array('%s','%d','%s','%s'),
					array('%d','%s')
				);
			}
			else{
				$wpdb->insert(
					$wpdb->prefix . "letsrecover_abandoned_cart", 
					array(
							'date_time' 	=> date('Y-m-d H:i:s'), 
							'user_id'		=> get_current_user_id(),
							'cart_detail' 	=> serialize($cart_detail),
							'cart_total'	=> WC()->cart->total,
							'subscriber_id'=> $subscriber_id,
							),
					array('%s','%d','%s','%s','%d')
				);

				//update abandoned carts stats
				$this->wplrp_web_push['stats']['carts']['captured'] = $this->wplrp_web_push['stats']['carts']['captured'] + 1;
				update_option('wplrp_web_push', $this->wplrp_web_push);

			}

		}else{

			$wpdb->update( "{$wpdb->prefix}letsrecover_abandoned_cart", 
									array('status' => 'Empty', 'cart_detail' => ""),
									array('subscriber_id' => $subscriber_id, 'status' => 'Abandoned'), 
									array('%s','%s'),
									array('%d','%s') 
								);
		}


	}

	function get_carts(){
		global $wpdb;

		//get all templates
		$push_templates = $this->wplrp_web_push['templates'];
		if ( ! $push_templates ){
			return;
		}
			

		$carts = $wpdb->get_results( $wpdb->prepare("SELECT  * FROM {$wpdb->prefix}letsrecover_abandoned_cart where next_template_id < %d AND status = 'Abandoned'", array( count($push_templates) )), ARRAY_A );
		$sent = 0;
		$invalid_subscribers = [];
		$notification_failed = [];
		$invalid_carts = [];
		foreach($carts as $c){

			$cart_detail = unserialize($c['cart_detail']);

			$current_template = $push_templates[$c['next_template_id']];

			$send_after = "+" . $current_template['interval_time'] . " " . $current_template['interval_unit'];

			//if time passed
			if( strtotime($c['date_time'] . $send_after) < time() ){
				//subscriber detail for push notification
				$subscriber = $wpdb->get_row( $wpdb->prepare("SELECT  `id`,`push_token` FROM {$wpdb->prefix}letsrecover_subscriptions where id = %d AND status = %d", array($c['subscriber_id'],1)), ARRAY_A );

				if( ! $subscriber ){
					continue;
				}

				//create notification payload
				$payload 	= array_filter(array(
									'm' 		=> $current_template['message'],
									't' 		=> $current_template['title'],
									'u'		=> ($current_template['url'] == "{checkout_page}") ?  wc_get_checkout_url() : $current_template['url'],
									'i'		=> ($current_template['icon'] == '{product_image}') ? $cart_detail['image']['icon'] : $current_template['icon'],
									'img'		=> ($current_template['image']== '{product_image}') ? $cart_detail['image']['img'] : $current_template['image'],
									'b'   	=> $current_template['badge'],
									'ah'		=> 1,
									'a' 		=> array_filter(array(
															array_filter(array('title' => $current_template['button_1_text'], 'action' => $current_template['button_1_url'])),
															array_filter(array('title' => $current_template['button_2_text'], 'action' => $current_template['button_2_url'])),
													))//a

								));

				//update next_template_id 
				$wpdb->query( $wpdb->prepare("UPDATE {$wpdb->prefix}letsrecover_abandoned_cart SET next_template_id = next_template_id + 1, push_sent = push_sent + 1 WHERE id = %d", $c['id'] ));

				//send notification
				$notification_response = $this->send_push_notification($subscriber, $payload, $c['id']);
				$sent++;

				if( $notification_response['invalid_subscriber'] )
					$invalid_subscribers[] = $notification_response['invalid_subscriber'];

				if( $notification_response['notification_failed'] )
					$notification_failed[] = $notification_response['notification_failed'];

				if( $notification_response['invalid_carts'] )
					$invalid_carts[] = $notification_response['invalid_carts'];


			}
		}//end foreach cart

		if( $carts ){
			if( $invalid_subscribers ){
				//update subscriber status
				$format = implode(', ', array_fill(0, count($invalid_subscribers), '%d'));
				$wpdb->query($wpdb->prepare("UPDATE {$wpdb->prefix}letsrecover_subscriptions SET `status` = 0 WHERE id IN ($format)", $invalid_subscribers ));
			}

			if( $notification_failed ){
				//update notification status
				$format = implode(', ', array_fill(0, count($notification_failed), '%d'));
				$wpdb->query($wpdb->prepare("UPDATE {$wpdb->prefix}letsrecover_notifications SET success = 0 WHERE id IN ($format)", $notification_failed ));

				//update notification stats options
				$this->wplrp_web_push['stats']['notifications']['failed'] = $this->wplrp_web_push['stats']['notifications']['failed'] + count($notification_failed);
			}

			if( $invalid_carts ){
				//update cart status
				$format = implode(', ', array_fill(0, count($invalid_carts), '%d'));
				$wpdb->query($wpdb->prepare("UPDATE {$wpdb->prefix}letsrecover_abandoned_cart SET status = 'Invalid' WHERE id IN ($format)", $invalid_carts ));
			}

			//update notification stats options
			$this->wplrp_web_push['stats']['notifications']['sent'] = $this->wplrp_web_push['stats']['notifications']['sent'] + $sent;
			update_option('wplrp_web_push', $this->wplrp_web_push);
		}



		// d($carts);
	}

	public function send_custom_notification(){
		if( isset($_POST['wplrp_setup_nonce_field'])){
			
			if( ! wp_verify_nonce($_POST['wplrp_setup_nonce_field'], 'wplrp_nounce_action') ){
				return;
			}

			if ( ! current_user_can( 'manage_options' ) ) { 
				return;
			}
		
			$data = wc_clean( $_POST );
			global $wpdb;
			
			if( isset($data['wplrp_send_push']) && $_GET['subscriber_id'] ){

				$subscriber = $wpdb->get_row( $wpdb->prepare("SELECT  `id`,`push_token` FROM {$wpdb->prefix}letsrecover_subscriptions where id = %d AND status = 1", array( sanitize_text_field($_GET['subscriber_id']))), ARRAY_A );
				
				//create notification payload
				$payload 	= array_filter(array(
									'm' 		=> $data['message'],
									't' 		=> $data['title'],
									'u'		=> $data['url'],
									'i'		=> $data['icon'],
									'ah'		=> 1,
									'a' 		=> array_filter(array(
															array_filter(array('title' => $data['button_1_text'], 'action' => $data['button_1_url'])),
															array_filter(array('title' => $data['button_2_text'], 'action' => $data['button_2_url'])),
													))//a
								));
				if( isset($data['image']) )
					$payload['img'] = $data['image'];

				
				$cart_id = sanitize_text_field($_GET['cart_id']);

				//update manual_sent count 
				$wpdb->query($wpdb->prepare("update {$wpdb->prefix}letsrecover_abandoned_cart SET push_sent = push_sent + 1 WHERE id = %d ", $cart_id));


				
				$notification_response = $this->send_push_notification($subscriber, $payload, $cart_id);
				//notification response will be empty upon success, 
				if($notification_response){

					//update notification status
					$wpdb->query($wpdb->prepare("UPDATE {$wpdb->prefix}letsrecover_notifications SET success = 0 WHERE id = %d ", $notification_response['notification_failed']) );

					//update cart status
					$wpdb->query($wpdb->prepare("UPDATE {$wpdb->prefix}letsrecover_abandoned_cart SET `status` = 'Invalid' WHERE id = %d", $cart_id) );

					//update subscriber status
					$wpdb->query($wpdb->prepare("UPDATE {$wpdb->prefix}letsrecover_subscriptions SET `status` = 0 WHERE id = %d ", $notification_response['invalid_subscriber'] ));

				}

				//update notification stats options
				$this->wplrp_web_push['stats']['notifications']['sent'] = $this->wplrp_web_push['stats']['notifications']['sent'] + 1;
				update_option('wplrp_web_push', $this->wplrp_web_push);

				set_transient('wplrp', array('type' => 'success', 'message' => 'Notification Sent Successfully'), 5);
				wp_redirect('admin.php?page=letsrecover-abandoned-carts');
			}
		}

		
	}
	function send_push_notification( $subscription, $payload, $cart_id ){

		global $wpdb;

		$subscriber_id 		= $subscription['id'];
		$push_token				= unserialize($subscription['push_token']);
		$invalid_subscriber 	= false;
		$notification_failed = false;
		$response 				= [];

		$payload = stripslashes_deep($payload);
		$wpdb->insert(
			$wpdb->prefix . "letsrecover_notifications", 
			array(
				'date_time' 	=> date('Y-m-d H:i:s'), 
				'template_md5'	=> md5($payload['t'] . ' ' . $payload['m']),
				'payload' 		=> serialize($payload),
				'cart_id'		=> $cart_id,
				'subscriber_id'=> $subscriber_id,
			),
			array('%s','%s','%s','%d','%d')
		);

		$payload['id'] = $wpdb->insert_id;
		
		// global $wpdb;
		require WPLRP_INCLUDES . 'libraries/push/src/send_push_notification.php';	
		
		if( $invalid_subscriber ){
			$response['invalid_subscriber'] 	= $subscriber_id;
			$response['invalid_carts'] 				= $cart_id;
		}

		if( $notification_failed )
			$response['notification_failed'] = $payload['id'];
		
		return $response;
		

	}

	function wplrp_notification_log(){
		global $wpdb;
		$column 	= sanitize_text_field($_POST['wplrp_log']);
		$id 		= sanitize_text_field($_POST['id']);

		$wpdb->update(
			$wpdb->prefix . "letsrecover_notifications", 
			array($column 	=> 1),
			array('id'=> $id),
			array('%d'),
			array('%d')
		);

		//update notification stats
		$this->wplrp_web_push['stats']['notifications'][$column] = $this->wplrp_web_push['stats']['notifications'][$column] + 1;
		update_option('wplrp_web_push', $this->wplrp_web_push);

		
	}



}



