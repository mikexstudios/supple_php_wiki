<?php theme_include('header'); ?>

<!--starting page content-->
<div id="page">

<?php if(get('page_exists')): ?>
	<?php out('page_content'); ?>
<?php else: ?>
	<p>This page doesn't exist yet. Maybe you want to <a href="<?php out('page_url', get('page_tag')); ?>/edit">create</a> it?</p>
<?php endif; ?>
	
</div>
<!--closing page content-->

<?php theme_include('footer'); ?>
