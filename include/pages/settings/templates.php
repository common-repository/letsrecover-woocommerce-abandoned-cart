
<?php 
	$wplrp_web_push = get_option('wplrp_web_push'); 
	$templates = $wplrp_web_push['templates'];

	//delete the current interval
	if( isset($_GET['delete']) && $_GET['delete'] != "" ){

		//delete the speified template
		unset($templates[$_GET['delete']]);
		$wplrp_web_push['templates'] = array_values($templates);


		update_option('wplrp_web_push', $wplrp_web_push );
		wp_redirect('?page=letsrecover-templates');
	}

?>

<div class="wplrp-settings-wrapper wplrp-notification-settings">
	<h2>Notification Templates</h2>
	<div class="wplrp-from-wrapper form-wrap">
		<table class="wp-list-table widefat fixed striped table-view-list posts">
			<thead>
				<tr>			
					<th>Template Name</th>
					<th>Notification Title</th>
					<th>Notification Message</th>
					<th>Send After</th>
					<th></th>
				</tr>
			</thead>
			<tbody id="the-list">
				<?php foreach($templates as $k=>$t){ ?>
					<tr class="iedit author-self level-0 post-908 type-post status-publish format-standard hentry category-uncategorized">
						<td><?php esc_html_e($t['template_name']);?></td>
						<td><?php esc_html_e($t['title']);?></td>
						<td><?php esc_html_e($t['message']);?></td>
						<td><?php echo esc_html($t['interval_time']) .  " " . esc_html($t['interval_unit']) . ( ( (int) $t['interval_time'] ) > 1  ? 's' : '' );?> </td>
						<td><a href="?page=letsrecover-templates&id=<?php echo (int) $k;?>">Edit</a> </td>
					</tr>
				<?php } ?>
			</tbody>
		</table>
	</div>
</div>
