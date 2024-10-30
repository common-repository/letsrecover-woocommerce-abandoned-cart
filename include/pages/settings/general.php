<div class="wplrp-settings-wrapper wplrp-notification-settings">
	<h2>General Settings</h2>
	<div class="wplrp-from-wrapper form-wrap">
		<form method="post" action=""  >
			<?php 
				wp_nonce_field( 'wplrp_nounce_action', 'wplrp_setup_nonce_field' ); 
				$options = get_option('wplrp_options');
			?>
			<div class="wplrp-from-wrapper form-wrap">
				<ul>
					<li>
						<div class="options-wrapper p-0">
							<label><input type="checkbox" name="wplrp_enable_push" id="wplrp_enable_push" value="1"    <?php if( $options['enable_push'] == '1' ) { ?> checked="checked" <?php } ?> /> <strong>Enable Web Push Notification Recovery</strong></label>
						</div>
					</li>
					<li>
						<div class="options-wrapper p-0">
							<label><input type="checkbox" name="wplrp_enable_email" id="wplrp_enable_email" value="1"    <?php if( $options['enable_email'] == '1' ) { ?> checked="checked" <?php } ?> /> <strong>Enable Email Recovery</strong></label>
						</div>
					</li>
				</ul>
			</div>
			<div class="wplrp-button-wrapper">
					<button type="submit" class="wplrp-btn wplrp-btn-primary" name="save_global_settings">Save Settings</button>
			</div>

		</form>
	</div>
</div>
 

