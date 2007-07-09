<?php theme_include('header.tpl.php'); ?>

<!--starting page content-->
<div id="page">

<?php for($i=0; $i<$num_revisions; $i++): ?>
<div class="history_revisioninfo">Latest edit on <a href="<?php out('page_url', get('page_tag'), 'show', 'time='.urlencode($revision_a_data[$i]['time'])); ?>"><?php echo $revision_a_data[$i]['time']; ?></a> by <span class="user"><?php echo $revision_a_data[$i]['user']; ?></span> <span class="pagenote smaller"></span></div><br class="clear" />
<div class="additions">
	<strong>Additions:</strong><br />
	<ins><?php echo $added[$i]; ?></ins>
</div>

<div class="deletions">
	<strong>Deletions:</strong><br />
	<del><?php echo $deleted[$i]; ?></del>
</div>

<hr />
<?php endfor; ?>

	
</div>
<!--closing page content-->

<?php theme_include('footer.tpl.php'); ?>