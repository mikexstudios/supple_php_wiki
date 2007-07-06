<?php theme_include('header.tpl.php'); ?>

<!--starting page content-->
<div class="page">


<div class="revisioninfo">
	Comparison of <a href="http://localhost/wikka-trunk/wikka.php?wakka=SandBox&amp;time=2007-07-06+12%3A06%3A06">2007-07-06 12:06:06</a> &amp; <a href="http://localhost/wikka-trunk/wikka.php?wakka=SandBox&amp;time=2007-07-06+10%3A46%3A23">2007-07-06 10:46:23</a>
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