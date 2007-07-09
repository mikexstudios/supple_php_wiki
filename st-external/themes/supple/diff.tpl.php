<?php theme_include('header.tpl.php'); ?>

<!--starting page content-->
<div id="page">


<div class="revisioninfo">
	Comparison of <a href="<?php out('page_url', get('page_tag'), 'show', 'time='.urlencode(get('revision_a_time'))); ?>"><?php out('revision_a_time'); ?></a>
	 &amp; <a href="<?php out('page_url', get('page_tag'), 'show', 'time='.urlencode(get('revision_b_time'))); ?>"><?php out('revision_b_time'); ?></a>
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

<?php theme_include('footer.tpl.php'); ?>