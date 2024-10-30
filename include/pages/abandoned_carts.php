<?php
	$abandoned_carts = new \WPLRP\Inc\Reports\Abandoned_carts();
	$abandoned_carts->prepare_items();
?>
<div class="wrap wplrp-abandoned-carts">
	<h2>Abandoned Carts</h2>
	<form method="post">
		<?php  $abandoned_carts->display(); ?>
	</form>
</div>