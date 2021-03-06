<?php  if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>
<div id="footer">
	<?php 
		$logged_in_username = get('logged_in_username');
		if(!empty($logged_in_username)):
	?>
		Hello, <a href="<?php out('page_url', $logged_in_username); ?>"><?php echo $logged_in_username; ?></a>! (<a href="<?php out('page_url', 'st-admin/users/logout'); ?>">Logout</a>) |
	<?php else: ?>
		<a href="<?php out('page_url', 'st-admin'); ?>">Log in</a> |
	<?php endif; ?>
	
	<?php 
		if(get('page_exists')):
			if(does_user_have_permission(get_page_write_roles(get('page_tag'))) === TRUE):
	?>
		<a href="<?php out('page_url', get('page_tag'), 'edit', get('current_revision')); ?>">Edit this page</a> |
	<?php endif; //does_user_have_permission ?> 
		<a href="<?php out('page_url', get('page_tag'), 'revisions'); ?>" title="Click to view recent revisions list for this page"><?php echo unix_to_human(get('page_time')); ?></a>
	<?php else: ?>
		<a href="<?php out('page_url', get('page_tag')); ?>/edit">Create page</a>
	<?php endif; ?>
	
	<div class="smallprint">
		<a class="ext" href="http://validator.w3.org/check/referer">Valid XHTML 1.0 Transitional</a> |
		<a class="ext" href="http://jigsaw.w3.org/css-validator/check/referer">Valid CSS</a> |
	<?php if($this->config->item('is_mu') == true): ?>
		Free wiki at <a class="ext" href="http://www.suppletext.com/">suppleText</a>
	<?php else: ?>
		Powered by <a class="ext" href="http://www.suppletext.org/">suppleText</a>
	<?php endif; ?>
	<?php 
		if(!empty($logged_in_username)): 
	?>
		| <a href="<?php out('page_url', 'st-admin'); ?>">Admin</a><br />
	<?php else: ?>
		<br />
	<?php endif; ?>
	<?php if($this->config->item('is_mu') != true): ?>
		Page was generated in <?php out('execution_time'); ?> seconds with <?php out('database_queries'); ?> queries.
	<?php endif; ?>
	</div>
</div>

</body>
</html>



