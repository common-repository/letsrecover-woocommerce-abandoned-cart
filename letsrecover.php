<?php 
/*
Plugin Name: LetsRecover Abandoned Cart
Description: Abandoned Cart Notification Reminder Using Web Push Notifications.
Author: Tahir Jamil
Plugin URI: https://www.letsrecoverplugin.com
Text Domain: woo-abandoned-cart-notifications
Version: 1.2.0
Requires at least: 5.0
Tested up to: 5.7.1
WC requires at least: 4.0
WC tested up to: 5.2.2
Requires PHP: 7.1
Recommended PHP: 7.2+
*/

defined( 'ABSPATH' ) || die();
define( 'WPLRP_VERSION', '1.0.0' );
define( 'WPLRP_DIR', WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . "letsrecover-woocommerce-abandoned-cart" . DIRECTORY_SEPARATOR );
define( 'WPLRP_URL', plugins_url( "",__FILE__ ) . DIRECTORY_SEPARATOR);
define( 'WPLRP_INCLUDES', WPLRP_DIR . "include" . DIRECTORY_SEPARATOR );
define( 'WPLRP_PRO_URL', '#' );

register_activation_hook( __FILE__, 'letsrecover_activation_hook' );
function  letsrecover_activation_hook(){
	require_once WPLRP_INCLUDES . "classes". DIRECTORY_SEPARATOR ."activation.php";
	\WPLRP\Inc\activate\Activation::init();
}

/* Compatibility for Super PWA Plugin */
if (!function_exists('letsrecover_super_pwa_compatibility')) {
	
	add_action('plugins_loaded', 'letsrecover_super_pwa_compatibility');
	function letsrecover_super_pwa_compatibility(){
		if( in_array('super-progressive-web-apps/superpwa.php',get_option('active_plugins') ) ){
			// Change service worker filename to match Letsrecover's service worker
			add_filter( 'superpwa_sw_filename', 'superpwa_letsrecover_sw_filename') ;		
			// Import Letsrecover service worker in SuperPWA
			add_filter( 'superpwa_sw_template', 'superpwa_letsrecover_sw' );
		}
	}

}

function superpwa_letsrecover_sw_filename( $sw_filename ) {
	return 'letsrecover-pwa-sw.js';
}
function superpwa_letsrecover_sw( $sw ) {
	$match = preg_grep( '#Content-Type: text/javascript#i', headers_list() );
	if ( ! empty ( $match ) ) {
		$letsrecover = 'var site_url="'. site_url() .'";' . PHP_EOL;
		$letsrecover .= "importScripts('" . plugins_url('assets/js/letsrecover_service_worker.min.js', __FILE__) . "');" . PHP_EOL;
		return $letsrecover . $sw;
	}
	$letsrecover  = '<?php' . PHP_EOL; 
	$letsrecover .= 'header( "Content-Type: application/javascript" );' . PHP_EOL;
	$letsrecover = 'echo var site_url="'. site_url() .'";' . PHP_EOL;
	$letsrecover .= 'echo "importScripts(\''. plugins_url('assets/js/letsrecover_service_worker.min.js', __FILE__) . '\');";' . PHP_EOL;
	$letsrecover .= '?>' . PHP_EOL . PHP_EOL;
	return $letsrecover . $sw;
}
/* Compatibility for Super PWA Plugin */

/* Plugin's action button */
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'letsrecover_plugin_action_link' );
function letsrecover_plugin_action_link( $links ) {
	$settings_link = array(
		'<a href="' . admin_url( 'admin.php?page=letsrecover-templates' ) . '">' . __( 'Settings' ) . '</a>',
	);

	return array_merge( $settings_link, $links );
}

/* initalize plugin's classes */
add_action('init', 'letsrecover_init_plugin');
function letsrecover_init_plugin(){	
	require_once   "init-classes.php";
}
