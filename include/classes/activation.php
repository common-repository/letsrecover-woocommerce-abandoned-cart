<?php

namespace WPLRP\Inc\activate;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @subpackage Activation
 */
class Activation {

	protected static $instance = null;

	private function __construct() {
      $this->activate();
	}

	public static function init() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	public function activate(  ) {
      
      //add settings for Web Push notifications
		$prerequisite =  $this->check_requirements();
		
		if( ! $prerequisite )
 	     $this->web_push();
		else{
			
			?>
			<p><?php _e( 'The plugin requires the following' ); ?></p>
			<ul>
				<?php foreach($prerequisite as $p){ ?>
					<li><?php _e($p); ?></li>
				<?php } ?>
			</ul>
			<?php
			exit();
		}
	}


	public function check_requirements(){
		/**
		 * check the following requirements
		 * PHP 7.2
		 * GMP PHP Extension
		 * mbstring PHP Extension
		 * curl PHP Extension
		 * openssl PHP Extension
		 * HTTPS (SSL Certificate)
		 * Check WooCommerce
		 */
		
		$prerequisite = [];

		if(version_compare(PHP_VERSION, '7.1') < 0){
			$prerequisite[] = 'PHP version atleast 7.1 but 7.2+ is recommended';
		}

		if (extension_loaded('mbstring') === false) { 
			$prerequisite[] = 'mbstring (Multibyte String) PHP extension';
		}

		if(extension_loaded('curl') === false){
			$prerequisite[] = 'curl PHP extension';
		}

		if(extension_loaded('openssl') === false){
			$prerequisite[] = 'openssl PHP extension';
		}

		if( ! isset($_SERVER['HTTPS']) ){
			$prerequisite[] = 'SSL certificate. The site must use HTTPS';
		}

		if ( class_exists( 'WooCommerce' ) === false ) {
			$prerequisite[] = 'WooCommerce plugin';
		}

		return $prerequisite;

	}

   public function web_push(){


		/**
		 * generate VAPID keys
		*/
      require_once WPLRP_INCLUDES . 'libraries/push/vendor/autoload.php' ;	
      $vapid_keys = \Minishlink\WebPush\VAPID::createVapidKeys();

		/**
		 * create table for Web Push Subscriptions
		*/
		global $wpdb;
		$charset_collate = $wpdb->get_charset_collate();
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );		

		$query = "CREATE TABLE IF NOT EXISTS " . $wpdb->prefix . "letsrecover_subscriptions (
					`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
					`date_time` datetime DEFAULT NULL,
					`md5_endpoint` char(32) DEFAULT NULL,
					`push_token` text DEFAULT NULL,
					`user_id` bigint(20) unsigned DEFAULT NULL,
					`ip_address` varchar(100) DEFAULT NULL,
					`country` varchar(100) DEFAULT NULL,
					`platform` varchar(100) DEFAULT NULL,
					`browser` varchar(100) DEFAULT NULL,
					`status` tinyint(4) DEFAULT '1',
					PRIMARY KEY (`id`),
					UNIQUE KEY `endpoint` (`md5_endpoint`),
					KEY `user_id` (`user_id`)
				) $charset_collate; ";
				
		
		dbDelta( $query );

		$query = "CREATE TABLE IF NOT EXISTS " . $wpdb->prefix . "letsrecover_abandoned_cart (
					`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
					`date_time` datetime DEFAULT CURRENT_TIMESTAMP,
					`user_id` bigint(20) unsigned DEFAULT NULL,
					`subscriber_id` int(10) unsigned DEFAULT NULL,
					`cart_detail` text,
					`cart_total` varchar(255)  DEFAULT NULL,
					`next_template_id` tinyint(3) unsigned DEFAULT '0',
					`push_sent` tinyint(3) unsigned DEFAULT '0',
					`status` enum('Abandoned','Recovered','Invalid','Empty')  NOT NULL DEFAULT 'Abandoned',
					PRIMARY KEY (`id`),
					KEY `get_subscriber_abandoned_cart` (`subscriber_id`,`status`),
					KEY `get_abandoned_carts` (`next_template_id`,`status`),
					KEY `user_id` (`user_id`)
				) $charset_collate; ";
				
		
		dbDelta( $query );

		$query = "CREATE TABLE IF NOT EXISTS " . $wpdb->prefix . "letsrecover_notifications (
					`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
					`date_time` datetime DEFAULT CURRENT_TIMESTAMP,
					`payload` text,
					`template_md5` varchar(255)  DEFAULT NULL,
					`subscriber_id` int(10) unsigned DEFAULT NULL,
					`cart_id` int(10) unsigned DEFAULT NULL,
					`success` tinyint(3) DEFAULT '1',
					`delivered` tinyint(3) unsigned DEFAULT '0',
					`clicked` tinyint(3) unsigned DEFAULT '0',
					`closed` tinyint(3) unsigned DEFAULT '0',
					PRIMARY KEY (`id`),
					KEY `cart_id` (`cart_id`),
					KEY `subscriber_id` (`subscriber_id`)
				) $charset_collate; ";
		dbDelta( $query );

		/**
		 * Add default values for Web Push Recovery
		*/
		add_option('wplrp_web_push', 	array(
													'vapid_keys'=> array(
														'public_key' 	=> $vapid_keys['publicKey'],
														'private_key'	=> $vapid_keys['privateKey']
													),
													'templates' => array(
														array(
																'template_name'	=> __( "Template 1" ),
																'title'		=> __( "We saved your cart" ),
																'message'	=> __( "We are still holding your product. Grab it, before it's too late" ),
																'icon'		=> __( "{product_image}" ),
																'image'		=> __( "{product_image}" ),
																'badge'		=> "",	
																'url'		   => __( "{checkout_page}" ),
																'interval_time'	=> 1,
																'interval_unit'	=> 'hour',
																'button_1_text'	=> "",
																'button_1_url'		=> "",
																'button_2_text'		=> "",
																'button_2_url'		=> "",
														),
														array(
																'template_name'	=> __( "Template 2" ),
																'title'		=> __( "Did you forget something?" ),
																'message'	=> __( "Looks like you have some items in your cart. Hurry back before they are gone!" ),
																'icon'		=> __( "{product_image}" ),
																'image'		=> __( "{product_image}" ),
																'badge'		=> "",
																'url'		   => __( "{checkout_page}" ),
																'interval_time'	=> 24,
																'interval_unit'	=> 'hour',
																'button_1_text'	=> "",
																'button_1_url'		=> "",
																'button_2_text'	=> "",
																'button_2_url'		=> "",
														),
													),
													'prompt'		=> array(
														'type'      => 'native',
														'message'	=> __( "Would you like to receive notifications on latest updates?" ),
														'message_color' => __( "#333333" ),
														'logo'		=> WPLRP_URL . 'assets/images/bell.png',
														'allow_button_text' => __( "Yes" ),
														'allow_button_text_color'=> __( "#FFFFFF" ),
														'allow_button_background_color'=> __("#007bff") ,
														'dismiss_button_text'		=> __( "Not yet" ),
														'dismiss_button_text_color'		=> __( "#007bff" ),
														'dismiss_button_background_color'		=>  __("#FFFFFF") 
													),
													'stats'		=> array(
														'notifications' => array(
															'sent' 		=> 0,
															'failed' 	=> 0,
															'delivered' => 0,
															'clicked' 	=> 0,
															'closed' 	=> 0,
														),
														'carts'	=> array(
															'captured' => 0,
															'recovered' => 0,
															'revenue'	=> 0,
														),
														'subscribers' => array(
															'total' => 0
														)
													)
												)


      );

   }


}
