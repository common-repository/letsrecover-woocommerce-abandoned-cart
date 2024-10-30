<?php 
include_once( 'include/libraries/push/vendor/autoload.php' );	
use Minishlink\WebPush\VAPID;
return VAPID::createVapidKeys();

