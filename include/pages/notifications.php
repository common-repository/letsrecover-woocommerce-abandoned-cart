<?php
	$subscribers = new \WPLRP\Inc\Reports\Notifications();
	$subscribers->prepare_items();
?>
<div class="wrap wplrp-notifications">
	<h2>Notifications</h2>
	<form method="post">
		<ul class="subsubsub">
			<li class="individual"><a href="admin.php?page=letsrecover-notifications&filter=individual" <?php if(! isset($_GET['filter']) || $_GET['filter'] == 'individual') { ?> class="current" <?php } ?> aria-current="page">Individual Notifications</a> |</li>
			<li class="grouped"><a href="admin.php?page=letsrecover-notifications&filter=grouped" <?php if( isset($_GET['filter']) && $_GET['filter'] == 'grouped') { ?> class="current" <?php } ?> >Group by Templates </a></li>
		</ul>		
		<?php  $subscribers->display(); ?>
	</form>
</div>