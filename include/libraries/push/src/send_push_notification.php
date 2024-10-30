<?php
require __DIR__ . '/../vendor/autoload.php';
use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\Subscription;


$subscriber = Subscription::create( array(
																		'endpoint' 	=> $push_token['endpoint'],
																		'authToken' => $push_token['token'],
																		'publicKey' => $push_token['key'],
																	)
															);
		



$payload_json 	= json_encode($payload);

$auth = array(
	'VAPID' => array(
		'subject' 	=> 'mailto:tahir1002@hotmail.com',
		'publicKey' 	=> $this->wplrp_web_push['vapid_keys']['public_key'],
		'privateKey' 	=> $this->wplrp_web_push['vapid_keys']['private_key']
	),
);
	

$webPush = new WebPush($auth,[],6,['verify'=>false]);

$webPush->setAutomaticPadding(false);
$webPush->setReuseVAPIDHeaders(true);

$report = $webPush->sendOneNotification(
	$subscriber,
	$payload_json // optional (defaults null)
);

try{
		$getResponse = $report->getResponse();
		if( $getResponse )
			$responseCode 	= $getResponse->getStatusCode();

}catch(\TypeError $e){
		$responseCode = 404;
}
		
if( ! $report->isSuccess() ) 
	$notification_failed = true;

if( $responseCode == 404 || $responseCode == 410 || $responseCode == 401  )
	$invalid_subscriber = true;





