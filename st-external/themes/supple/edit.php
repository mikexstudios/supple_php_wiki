<?php  if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>
<?php theme_include('header'); ?>

<!--starting page content-->
<div id="page">

<?php if(!empty($this->validation->error_string)): ?>
	<?php echo $this->validation->error_string; ?>
<?php endif; ?>

<form action="<?php out('current_url'); ?>" method="post">
<textarea id="body" name="body">
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
	<legend>Store page</legend>
	<input id="note" size="50" type="text" name="note" value="<?php out('form_value', 'note'); ?>" /> <label for="note">Please add a note on your edit</label><br />
	<br />
	<input name="submit" type="submit" value="Store" accesskey="s" />
	<input name="submit" type="submit" value="Preview" accesskey="p" />
	<input type="button" value="Cancel" onclick="document.location='<?php out('page_url', get('page_tag')); ?>';" />
</fieldset>
</form>

</div>
<!--closing page content-->

<?php theme_include('footer'); ?>
