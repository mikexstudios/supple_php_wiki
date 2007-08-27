<?php  if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>
<?php theme_include('header'); ?>

<!--starting page content-->
<div id="page">

<?php 
	$revision_num = get('current_revision');
	if(!empty($revision_num)):
?>
<div class="error">
	<h2>
	This is revision <?php echo $revision_num; ?>, an old version of 
	<a href="<?php out('page_url', get('page_tag')); ?>"><?php out('page_tag'); ?></a> 
	from <a href="<?php out('page_url', get('page_tag'), 'revisions'); ?>"><?php echo unix_to_human(get('page_time')); ?></a>.			<br />
	</h2>
</div>
<?php endif; ?>

<?php if(get('page_exists')): ?>
<?php out('page_content'); ?>
<?php else: ?>
	<p>This page doesn't exist yet. Maybe you want to <a href="<?php out('page_url', get('page_tag')); ?>/edit">create</a> it?</p>
<?php endif; ?>
	
</div>
<!--closing page content-->

<?php theme_include('footer'); ?>
