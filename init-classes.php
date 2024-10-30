<?php 
namespace WPLRP\Inc;

use WPLRP\Inc\Settings\Admin\Admin_functions;
use WPLRP\Inc\Settings\Wp\Wp_functions;


if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once WPLRP_INCLUDES . "classes/admin-functions.php"; 
require_once WPLRP_INCLUDES . "classes/wp-functions.php"; 


Admin_functions::init();
Wp_functions::init();

