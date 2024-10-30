<?php	
		$subscribers_batches = array_chunk($getSubscriptions,300);
		$data['site_detail'] = array('public_key' => get_option('wplpp_public_key'), 'private_key' => get_option('wplpp_private_key'));
		$data['payload']		= $notificationData['payload'];
		$data['notification_id']		= $notification_id;

		$request['headers'] 	= array( 'Content-Type' => 'Application/Json');
		$request['timeout']	= 40;
		$failed = 0;
		$success = 0;
		$failed_subscribers = array();
		foreach($subscribers_batches as $subscribers){

			$data['subscribers'] = $subscribers;
			$request['body'] 		=  json_encode($data);

			$result 		= wp_remote_post('http://your-site.com/send_push_notification_server.php',$request);
			$response 	= wp_remote_retrieve_body($result);


			$res_array 	= json_decode($response,true);

			$failed 		= $failed + $res_array['failed'];
			$success 	= $success + $res_array['sent'];
			$failed_subscribers[] = $res_array['invalid_subscribers'];
		}
		$failed_subscribers = array_filter($failed_subscribers);
		foreach($failed_subscribers as $fs)
			foreach($fs as $f)
			$delete_subscribers[] = $f;



		if( $notification_id ){
			$wpdb->query($wpdb->prepare("UPDATE " . $wpdb->prefix . "posts set post_status = 'Sent' where  id = %d ", $notification_id));

			update_post_meta($notification_id,'wplpp_notification_fail',$failed);
			update_post_meta($notification_id,'wplpp_notification_success',$success);

			if( $delete_subscribers ){				
		
				$delete_subscribers = implode(', ', $delete_subscribers);
				$wpdb->query($wpdb->prepare("delete from " . $wpdb->prefix . "wplpp_subscriptions where id IN ( %s )", $delete_subscribers) );
			}
		}
