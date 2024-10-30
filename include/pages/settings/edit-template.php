<div class="wplrp-settings-wrapper wplrp-notification-settings">
	<h2>Edit Template</h2>
	<div class="wplrp-from-wrapper form-wrap">
		<form method="post" action=""  >
			<?php 
				wp_nonce_field( 'wplrp_nounce_action', 'wplrp_setup_nonce_field' ); 
				$options = get_option('wplrp_options');
			?>

			<ul class="push-setting">

				<?php 
					$templates = get_option('wplrp_web_push'); 
					$template = ['template_name' => '', 'title'=> '', 'message'=> '', 'icon'=> '', 'image'=> '', 'badge'=> '','url'=> '','interval_time'=> '','interval_unit'=> '', 'button_1_text' => "", 'button_1_url' => "", 'button_2_text' => "", 'button_2_url' => ""];
					if( $templates['templates'] && isset($templates['templates'][$_GET['id']]) )
						$template = $templates['templates'][ sanitize_text_field($_GET['id']) ];
				?>
				<li>
					<label for="template_name">Template Name</label>
					<input name="template_name" class="wplrp_title wplrp-textfield" id="template_name" type="text" value="<?php _e($template['template_name']);?>" size="100" aria-required="true" >
				</li>

				<li>
					<label for="send_after_time">Send After</label>
					<div class="options-wrapper  vertical  p-0">
						<div class="wplrp_push_icon wplrp-group-fields" >
							<input name="interval_time" class="send_after_time wplrp-textfield" id="send_after_time" type="text" value="<?php _e($template['interval_time']);?>" size="100" aria-required="true" >
							<select name="interval_unit" class="wplrp-textfield">
								<option value="hour" <?php if ($template['interval_unit'] == 'hour') {?> selected <?php } ?>>Hour</option>
								<option value="minute" <?php if ($template['interval_unit'] == 'minute') {?> selected <?php } ?>>Minute</option>
							</select>
						</div>
					</div>
				</li>

				<li>
					<label for="wplrp_title">Notification Title</label>
					<input name="title" class="wplrp_title wplrp-textfield emojifield" id="wplrp_title" type="text" value="<?php _e($template['title']);?>" size="100" aria-required="true" >
				</li>

				<li>
					<label for="wplrp_message">Notification Message</label>
					<textarea rows="3" name="message" class="wplrp_push_message wplrp_textarea required-field wplrp-textfield emojifield" required id="wplrp_message"><?php _e($template['message']);?></textarea>
				</li>

				<li>
					<label for="wplrp_icon">Notification Icon</label>
					<div class="options-wrapper  vertical  p-0">
						<div class="wplrp_push_icon wplrp-group-fields"  >
							<input name="icon" id="wplrp_icon" class="upload-field" placeholder="" type="text" value="<?php _e($template['icon']);?>" size="100">
							<button  class="wplrp-btn wplrp-btn-primary wplrp-btn-upload" data-label="Icon" data-field="wplrp_push_icon" type="button" id="">Choose Icon</button>
						</div>
					</div>
				</li>

				<li>
					<label for="wplrp_image">Notification Image <div class="wplrp-info"><span class="dashicons dashicons-editor-help"></span><p class="wplrp-font-weight-normal">Notification (large) Image is only supported by Chrome browser in Windows & Android.</p></div></label>
					<div class="options-wrapper  vertical  p-0">
						<div class=" wplrp_push_image wplrp-group-fields" >
							<input name="image" id="wplrp_image" class="upload-field" placeholder="" type="text" value="<?php _e($template['image']);?>" size="100">
							<button  class="wplrp-btn wplrp-btn-primary wplrp-btn-upload" data-label="Image" data-field="wplrp_push_image" type="button" id="">Choose Image</button>
						</div>

					</div>
				</li>

				<li>
					
					<label for="wplrp_push_badge">Notification Badge <div class="wplrp-info"><span class="dashicons dashicons-editor-help"></span><p class="wplrp-font-weight-normal">The badge is a small monochrome icon that is used (for mobile) to portray a little more information to the user about where the notification is from.<br />Recommended size: 100px x 100px</p></div></label>
					
					<div class=" wplrp-group-fields" >
						<input tabindex="" class="upload-field" name="badge" id="wplrp_push_badge" placeholder="" type="text" value="<?php _e($template['badge']);?>"  aria-required="true" >
						<button tabindex="3" class="wplrp-btn wplrp-btn-primary wplrp-btn-upload" type="button" data-label="Badge">Choose Badge</button>
					</div>
				</li>


				<li>
					<label for="wplrp_url">Target URL</label>
					<input name="url" class="required-field" required id="wplrp_url" type="text" value="<?php _e($template['url']);?>" placeholder="" size="100" aria-required="true" >
				</li>


				<li class="wplrp-action-button wplrp-action-button-1 <?php if( $template['button_1_text']  ) { echo 'wplrp-d-block';  } ?>" >
					<fieldset>
							<legend>Button 1</legend>
							<label for="wplrp_button1_text">Text</label>
							<input name="button_1_text" id="wplrp_button1_text" class="emojifield button_1_text wplrp-textfield" type="text" value="<?php esc_html_e($template['button_1_text']);?>"  >
							<label for="wplrp_button1_url">URL</label>
							<input name="button_1_url" id="wplrp_button1_url" class=" wplrp-textfield" type="text" value="<?php echo esc_url($template['button_1_url']);?>"  >
							<button class="wplrp-action-button-remove wplrp-d-block" type="button" ><svg class="svg-inline--fa fa-times fa-w-11" aria-hidden="true" focusable="false" data-prefix="fa" data-icon="times" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 352 512" data-fa-i2svg=""><path fill="currentColor" d="M242.72 256l100.07-100.07c12.28-12.28 12.28-32.19 0-44.48l-22.24-22.24c-12.28-12.28-32.19-12.28-44.48 0L176 189.28 75.93 89.21c-12.28-12.28-32.19-12.28-44.48 0L9.21 111.45c-12.28 12.28-12.28 32.19 0 44.48L109.28 256 9.21 356.07c-12.28 12.28-12.28 32.19 0 44.48l22.24 22.24c12.28 12.28 32.2 12.28 44.48 0L176 322.72l100.07 100.07c12.28 12.28 32.2 12.28 44.48 0l22.24-22.24c12.28-12.28 12.28-32.19 0-44.48L242.72 256z"></path></svg></button>
					</fieldset>
				</li>

				<li class="wplrp-action-button wplrp-action-button-2 <?php if( $template['button_2_text']  ) { echo 'wplrp-d-block';  } ?>" >
					<fieldset>
							<legend>Button 2</legend>
							<label for="wplrp_button2_text">Text</label>
							<input name="button_2_text" id="wplrp_button2_text" class="emojifield button_2_text  wplrp-textfield" type="text" value="<?php esc_html_e($template['button_2_text']);?>"  >
							<label for="wplrp_button2_url">URL</label>
							<input name="button_2_url" id="wplrp_button2_url" class=" wplrp-textfield" type="text"  value="<?php echo esc_url($template['button_2_url']);?>"  >
							<button class="wplrp-action-button-remove wplrp-d-block" type="button" ><svg class="svg-inline--fa fa-times fa-w-11" aria-hidden="true" focusable="false" data-prefix="fa" data-icon="times" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 352 512" data-fa-i2svg=""><path fill="currentColor" d="M242.72 256l100.07-100.07c12.28-12.28 12.28-32.19 0-44.48l-22.24-22.24c-12.28-12.28-32.19-12.28-44.48 0L176 189.28 75.93 89.21c-12.28-12.28-32.19-12.28-44.48 0L9.21 111.45c-12.28 12.28-12.28 32.19 0 44.48L109.28 256 9.21 356.07c-12.28 12.28-12.28 32.19 0 44.48l22.24 22.24c12.28 12.28 32.2 12.28 44.48 0L176 322.72l100.07 100.07c12.28 12.28 32.2 12.28 44.48 0l22.24-22.24c12.28-12.28 12.28-32.19 0-44.48L242.72 256z"></path></svg></button>
					</fieldset>
				</li>


					


			</ul>

			<div class="wplrp-button-wrapper">
				<?php if( $_GET['id'] != "" ){ ?>
					<input type="hidden" name="next_template_id" value="<?php echo (int) ($_GET['id']);?>">
				<?php } ?>
				<a  class="wplrp-btn wplrp-btn-secondary" href="/wp-admin/admin.php?page=letsrecover-templates"   >Cancel</a>
				<button type="submit" class="wplrp-btn wplrp-btn-primary" name=""   >Save Template</button>
			</div>
		</form>
	</div>
</div>
