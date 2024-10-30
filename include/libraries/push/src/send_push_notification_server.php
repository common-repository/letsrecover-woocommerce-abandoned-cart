<?php

//error_reporting(E_ALL);
//ini_set('display_errors',1);
//ini_set('display_startup_errors',1);
require  __DIR__ . '/push_server/vendor/autoload.php';
use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\Subscription;


$request_data = json_decode(file_get_contents('php://input'),true);


$invalid_subscribers = array();
$subscriptions = $request_data['subscribers'];

foreach($subscriptions as $sr) 
{
    $subscriptions_total[] = array(
                                    'subscription' => Subscription::create( array(
                                                                                                            'endpoint' 	=> $sr['endpoint'],
                                                                                                            'authToken' => $sr['token'],
                                                                                                            'publicKey' => $sr['key'],
                                                                                                        )
                                                                                                )
                                            
                                        );
    $endpoints[$sr['endpoint']] = $sr['id'];
}




//payload
if( $request_data )	
    $request_data['payload']['notification_id'] = $request_data['notification_id'];

$payload 	= html_entity_decode(json_encode($request_data['payload']));

$auth = array(
    'VAPID' => array(
        'subject' 	    => 'mailto:tahir1002@hotmail.com',
        'publicKey' 	=> $request_data['site_detail']['public_key'],
        'privateKey' 	=> $request_data['site_detail']['private_key']
    ),
);
    

$webPush = new WebPush($auth,[],20,['verify'=>false]);
// $webPush->setDefaultOptions($defaultOptions);
$webPush->setAutomaticPadding(false);
$webPush->setReuseVAPIDHeaders(true);

if(! empty($subscriptions_total) ) 
{
    foreach($subscriptions_total as $sub)
    {
        $webPush->queueNotification(
            $sub['subscription'],
            $payload
        );
    }
}

/**
* Check sent results
* @var MessageSentReport $report
*/
$sent= 0;
$fail= 0;

foreach ($webPush->flush() as $report) {

    try{
        $endpoint 		= $report->getRequest()->getUri()->__toString();

        $getResponse = $report->getResponse();
        if( $getResponse )
            $responseCode 	= $getResponse->getStatusCode();

    }catch(\TypeError $e){
        $responseCode = 404;
    }
    
    if( $report->isSuccess() ) {
        $sent++;
    }else{
        $fail++;
    
        if( $responseCode == 404 || $responseCode == 410 || $responseCode == 401  )
            $invalid_subscribers[] = $endpoints[$endpoint];
    }
}//foreach ($webPush->flush() as $report)

echo json_encode(array('sent' => $sent, 'failed' => $fail, 'invalid_subscribers' => $invalid_subscribers));



