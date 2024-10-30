<div class="wplrp-settings-wrapper wplrp-notification-settings">
	

	<div class="wplrp-from-wrapper form-wrap">
		<form method="post" action="" id="wplrp_push_notification" >
			<?php 
				wp_nonce_field( 'wplrp_nounce_action', 'wplrp_setup_nonce_field' ); 

				if( empty($_GET['tab']) || $_GET['tab'] == 'template' ){
					if( ! isset($_GET['id']) )
						require_once('webpush/notification-templates.php'); 
					else
						require_once('webpush/edit-template.php'); 
				}
				elseif( empty($_GET['tab']) || $_GET['tab'] == 'interval' )
					require_once('webpush/setup-interval.php'); 
				elseif( empty($_GET['tab']) || $_GET['tab'] == 'optin-prompt' )
					require_once('webpush/setup-optin-prompt.php'); 
			?>
		</form>
   </div>
</div>


