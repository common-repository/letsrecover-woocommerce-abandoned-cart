
<div class="wrap wplrp-dashboard">
	<h2>Dashboard</h2>
<?php 
	$failed = sanitize_text_field($stats['notifications']['failed']);
	$sent = sanitize_text_field($stats['notifications']['sent']);
	$delivered = sanitize_text_field($stats['notifications']['delivered']);
	$clicked = sanitize_text_field($stats['notifications']['clicked']);
	$closed = sanitize_text_field($stats['notifications']['closed']);
	$cart_captured = sanitize_text_field($stats['carts']['captured']);
	$cart_recovered = sanitize_text_field($stats['carts']['recovered']);
	$subscriber_total = sanitize_text_field($stats['subscribers']['total']);
?>
<div class="wplrp-widget">
	<div class="wplrp-widget-header">Recovery Notifications</div>
	<div class="wplrp-widget-body">
		<div class="wplrp-widget-block">
			<div class="wplrp-block-name">Sent</div>
			<div class="wplrp-block-number"><?php echo number_format($sent);?></div>
			<div class="wplrp-block-percent"></div>
		</div>
		<div class="wplrp-widget-block wplrp-block-failed">
			<div class="wplrp-block-name">Failed</div>
			<div class="wplrp-block-number"><?php echo number_format($failed); ?></div>
			<div class="wplrp-block-percent">Failure Rate: <?php echo $failed ? number_format($failed / $sent * 100,1) : 0;?>%</div>
		</div>

		<div class="wplrp-widget-block wplrp-block-success">
			<div class="wplrp-block-name">Successed</div>
			<div class="wplrp-block-number"><?php echo number_format($sent - $failed); ?></div>
			<div class="wplrp-block-percent">Success Rate: <?php echo $failed ? number_format( ($sent - $failed) / $sent   * 100,1) : 0;?>%</div>
		</div>

		<div class="wplrp-widget-block wplrp-block-delivered">
			<div class="wplrp-block-name">Delivered</div>
			<div class="wplrp-block-number"><?php echo number_format($delivered);?></div>
			<div class="wplrp-block-percent">Delivery Rate: <?php echo $delivered ? number_format($delivered / ($sent - $failed) * 100,1) : 0;?>%</div>
		</div>
		<div class="wplrp-widget-block wplrp-block-clicked">
			<div class="wplrp-block-name">Clicked</div>
			<div class="wplrp-block-number"><?php echo number_format($clicked);?></div>
			<div class="wplrp-block-percent">Click Rate: <?php echo $clicked ? number_format($clicked / $delivered * 100,1) : 0;?>%</div>
		</div>
		<div class="wplrp-widget-block wplrp-block-closed">
			<div class="wplrp-block-name">Closed</div>
			<div class="wplrp-block-number"><?php echo number_format($closed);?></div>
			<div class="wplrp-block-percent">Close Rate: <?php echo $closed ? number_format($closed / $delivered * 100,1) : 0;?>%</div>
		</div>

	</div>
	<div class="wplrp-widget-footer"><a href="admin.php?page=letsrecover-notifications" class="btn button">View detail</a></div>
</div>

<div class="wplrp-widget-inline">
	<div class="wplrp-widget">
		<div class="wplrp-widget-header">Abandoned Carts</div>
		<div class="wplrp-widget-body">
			<div class="wplrp-widget-block">
				<div class="wplrp-block-name">Captured</div>
				<div class="wplrp-block-number"><?php echo number_format($cart_captured);?></div>
				<div class="wplrp-block-percent"></div>
			</div>
			<div class="wplrp-widget-block wplrp-block-delivered">
				<div class="wplrp-block-name">Recovered</div>
				<div class="wplrp-block-number"><?php echo number_format($cart_recovered);?></div>
				<div class="wplrp-block-percent">Recovery Rate: <?php echo $cart_recovered ? number_format($stats['carts']['recovered'] / $cart_captured * 100,1) : 0;?>%</div>
			</div>
			<div class="wplrp-widget-block wplrp-block-clicked">
				<div class="wplrp-block-name">Revenue Recovered</div>
				<div class="wplrp-block-number"><?php echo (get_option( 'woocommerce_currency_pos' ) == 'left' ?   get_woocommerce_currency_symbol() : '') . number_format($stats['carts']['revenue'],2) . (get_option( 'woocommerce_currency_pos' ) == 'right' ?   get_woocommerce_currency_symbol() : '');?></div>
				<div class="wplrp-block-percent"></div>
			</div>

		</div>
		<div class="wplrp-widget-footer"><a href="admin.php?page=abandoned-carts-page" class="btn button">View detail</a></div>
	</div>

	<div class="wplrp-widget">
		<div class="wplrp-widget-header">Subscribers</div>
		<div class="wplrp-widget-body">

			<div class="wplrp-widget-block">
				<div class="wplrp-block-name">Total (all time)</div>
				<div class="wplrp-block-number"><?php echo number_format($subscriber_total);?></div>
				<div class="wplrp-block-percent"></div>
			</div>

			<div class="wplrp-widget-block wplrp-block-delivered">
				<div class="wplrp-block-name">Active</div>
				<div class="wplrp-block-number"><?php global $wpdb; echo $wpdb->get_var("select count(*) as total from {$wpdb->prefix}letsrecover_subscriptions where status = 1" ); ?></div>
				<div class="wplrp-block-percent"></div>
			</div>

		</div>
		<div class="wplrp-widget-footer"><a href="admin.php?page=letsrecover-subscribers" class="btn button">View detail</a></div>
	</div>
</div>
