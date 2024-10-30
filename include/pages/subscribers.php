<?php
	$subscribers = new \WPLRP\Inc\Reports\Subscribers();
	$subscribers->prepare_items();
?>
<div class="wrap wplrp-subscribers">
	<h2>Subscribers</h2>
	<form method="post">
		<?php  $subscribers->display(); ?>
	</form>
</div>
