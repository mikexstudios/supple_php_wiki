<div id="footer">
	<a href="<?php out('page_url', get('page_tag')); ?>/edit">Edit this page</a> | 
	<a href="<?php out('page_url', get('page_tag'), 'history'); ?>" title="Click to view recent edits to this page">Page History</a> | 
	<a href="<?php out('page_url', get('page_tag'), 'revisions'); ?>" title="Click to view recent revisions list for this page"><?php out('page_time'); ?></a>
	
	<div class="smallprint">
		<a class="ext" href="http://validator.w3.org/check/referer">Valid XHTML 1.0 Transitional</a> |
		<a class="ext" href="http://jigsaw.w3.org/css-validator/check/referer">Valid CSS</a> |
		Powered by <a class="ext" href="http://www.suppletext.org/">suppleText</a><br />
		Page was generated in <?php out('execution_time'); ?> seconds
	</div>
</div>





