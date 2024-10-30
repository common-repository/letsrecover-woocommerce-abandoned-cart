<?php 
	$wplrp_web_push = get_option('wplrp_web_push'); 
	$prompt = $wplrp_web_push['prompt'];
?>
<div class="wplrp-settings-wrapper wplrp-notification-settings wplrp-optin-wrapper">
	<h2>Setup Opt-in Prompt</h2>		

	<form method="post" action=""  >
		<?php 
			wp_nonce_field( 'wplrp_nounce_action', 'wplrp_setup_nonce_field' ); 
			$options = get_option('wplrp_options');
		?>
		<div class="wplrp-from-wrapper form-wrap">	
			<div class="wplrp-push-content">
				<ul class="wplrp_custom_prompt_options">
					<li>
						<label for="wplrp_title">Opt-in Prompt Type</label>
						<select class="wplrp_title wplrp-textfield type" onchange="prompt_type(this)" name="type">
								<option <?php if( $prompt['type'] == 'native'){ ?> selected <?php } ?> value="native">Browser's Navtive Prompt</option>
						</select>
					</li>
				</ul>

				<ul class="wplrp_custom_prompt <?php if( $prompt['type'] == 'custom'){ echo 'wplrp-d-block'; } ?>" >
					<li>
						<label for="message">Message</label>
						<div class="wplrp-group-fields">
							<input tabindex="3"  maxlength="250" id="message" name="message" required class=" required-field wplrp-textfield"  value="<?php esc_html_e($prompt['message']);?>">
							<input type="color" id="prompt_message_color" class="wplrp-textfield wplrp_colorpicker" name="message_color"  value="<?php esc_html_e($prompt['message_color']);?>">
						</div>
					</li>

					<li>
						<label for="logo">Logo</label>
						<div class="wplrp-group-fields">
							<input tabindex="" class="upload-field" name="logo" id="logo" placeholder="" type="text" value="<?php echo esc_url($prompt['logo']);?>"  aria-required="true" >
							<button tabindex="3" class="wplrp-btn wplrp-btn-primary wplrp-btn-upload" type="button" data-label="Logo">Choose Logo</button>
						</div>
					</li>


					<li>
						<label for="allow_button_text">Allow Button</label>
						<div class="wplrp-group-fields">
							<input name="allow_button_text" required class=" required-field wplrp-textfield" id="allow_button_text" type="text"  size="100" aria-required="true" value="<?php esc_html_e($prompt['allow_button_text']);?>" >
							<input type="color" id="allow_button_text_color" class="wplrp-textfield wplrp_colorpicker allow_button_text_color" name="allow_button_text_color" value="<?php esc_html_e($prompt['allow_button_text_color']);?>">
							<input type="color" id="allow_button_background_color" class="wplrp-textfield wplrp_colorpicker allow_button_background_color" name="allow_button_background_color" value="<?php esc_html_e($prompt['allow_button_background_color']);?>">
						</div>
					</li>

					<li>
						<label for="dismiss_button_text">Dismiss Button</label>
						<div class="wplrp-group-fields">
							<input name="dismiss_button_text" required class=" required-field wplrp-textfield" id="dismiss_button_text" type="text"  size="100" aria-required="true" value="<?php esc_html_e($prompt['dismiss_button_text']);?>"  >
							<input type="color" id="dismiss_button_text_color" class="wplrp-textfield wplrp_colorpicker dismiss_button_text_color" name="dismiss_button_text_color" value="<?php esc_html_e($prompt['dismiss_button_text_color']);?>">
							<input type="color" id="dismiss_button_background_color" class="wplrp-textfield wplrp_colorpicker dismiss_button_background_color" name="dismiss_button_background_color" value="<?php esc_html_e($prompt['dismiss_button_background_color']);?>">
						</div>
					</li>
				</ul>
			</div>

			<div class="wplrp-push-preview">
					                
					<div class="wplrp-optin-preview">
						<div class="custom-prompt wplrp_custom_prompt <?php if( $prompt['type'] == 'custom') echo 'wplrp-d-block';  ?>">
							<div class="wplrppromptcontainer">
									<div class="wplrpprompt">
										<div class="wplrpPromptIcon">
											<img src="<?php echo esc_url($prompt['logo']);?>" alt=''>
										</div>
										<div class="wplrpPromptText">
											<div class="wplrpPromptMessage">
													<?php esc_html_e($prompt['message']);?>
											</div>
										</div>
										<div class="wplrpPromptButtons">
											<div class="wplrppromptdismissbtn">
													<div class="wplrppromptbutton"><?php esc_html_e($prompt['dismiss_button_text']);?></div>
											</div>
											<div class="wplrppromptapprovebtn">
													<div class="wplrppromptbutton"><?php esc_html_e($prompt['allow_button_text']);?></div>
											</div>
										</div>

									</div>
							</div>
						</div>
						<div class="native-prompt wplrp_native_prompt <?php if( $prompt['type'] == 'native') echo 'wplrp-d-block';?>">
							<div class="wplrpprompt">
								<img src="<?php echo WPLRP_URL . 'assets/images/native-prompt.png';?>">
							</div>
						</div>
					</div>
			</div>

			<div class="wplrp-button-wrapper">
				<button type="submit" class="wplrp-btn wplrp-btn-primary" name="wplrp_push_prompt">Save Settings</button>
			</div>
		</div>
	</form>
</div>



<?php wp_enqueue_media(); ?>