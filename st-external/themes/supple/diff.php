<?php  if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>
<?php theme_include('header'); ?>

<!--starting page content-->
<div id="page">

<div class="revisioninfo">
	Comparison of <a href="<?php out('page_url', get('page_tag'), 'show', element('id', get('diff_a'))); ?>"><?php echo unix_to_human(element('time', get('diff_a'))); ?></a>
	 &amp; <a href="<?php out('page_url', get('page_tag'), 'show', element('id', get('diff_b'))); ?>"><?php echo unix_to_human(element('time', get('diff_b'))); ?></a>
</div>

<div class="additions">
	<strong>Additions:</strong><br />
	<ins><?php out('diff_added'); ?></ins>
</div>

<div class="deletions">
	<strong>Deletions:</strong><br />
	<del><?php out('diff_deleted'); ?></del>
</div>
	
</div>
<!--closing page content-->

<?php theme_include('footer'); ?>
