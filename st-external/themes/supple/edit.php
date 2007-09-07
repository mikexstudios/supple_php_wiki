<?php  if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>
<?php theme_include('header'); ?>

<!--starting page content-->
<div id="page">

<?php if(!empty($this->validation->error_string)): ?>
	<?php echo $this->validation->error_string; ?>
<?php endif; ?>

<form action="<?php out('current_url'); ?>" method="post">
<textarea id="body" name="body" tabindex=1>
<?php 
	//This checks to see if we have some body value set from a form submission error.
	$form_body = get('form_value', 'body');
	if(!empty($form_body))
	{
		echo $form_body;
	}
	else
	{
		out('page_content');
	} 
?>
</textarea><br />

<fieldset>
	<legend>Additional Actions</legend>
	<p>
		<?php if(does_user_have_permission('Registered')): ?>
			<?php if(does_user_have_permission('Editor')): ?>
				<a href="<?php out('page_url', '/st-admin/pages/changepermissions/'.get('this_page').'/'); ?>">Change Page Permissions</a> |
			<?php endif; ?>
			
			<a href="<?php out('page_url', get('this_page'), 'attachments'); ?>">View or Attach Files</a>
		<?php else: ?>
			You currently do not have the user permissions to perform additional actions on this page.
		<?php endif; ?>
	</p> 
</fieldset>

<fieldset>
	<legend>Store page</legend>
	<input id="note" size="50" type="text" name="note" tabindex=2 value="<?php out('form_value', 'note'); ?>" /> <label for="note">Please add a note on your edit</label><br />
	<br />
	<input name="submit" type="submit" value="Store" accesskey="s" tabindex=3 />
	<input name="submit" type="submit" value="Preview" accesskey="p" tabindex=4 />
	<input type="button" value="Cancel" tabindex=5 onclick="document.location='<?php out('page_url', get('page_tag')); ?>';" />
</fieldset>
</form>

</div>
<!--closing page content-->

<?php theme_include('footer'); ?>
