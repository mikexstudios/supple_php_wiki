<?php  if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>

<?php theme_include('header'); ?>

<!--starting page content-->
<div id="page">

<?php if(!empty($this->validation->error_string)): ?>
	<?php echo $this->validation->error_string; ?>
<?php endif; ?>

<div id='tab-container'>
<div class="tab-content">
<h1 class="tab" title="title for page 1">Edit Page</h1>

<!-- This bit of js handles the notification of the user if he/she navigates 
away from the page with changes to the form -->
<script type="text/javascript" src="<?php out('theme_url', 'discard_confirmation.js'); ?>"></script>
<form action="<?php out('current_url'); ?>" method="post" class="editform">
<textarea id="body" name="body" tabindex=1 onChange="form_changed()">
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

<?php if(does_user_have_permission('Registered')): ?>
<fieldset class="additional_actions">
	<legend>Additional Actions</legend>
	<p>
			<?php if(does_user_have_permission('Editor')): ?>
				<a href="<?php out('page_url', '/st-admin/pages/changepermissions/'.get('this_page').'/'); ?>">Change Page Permissions</a> |
			<?php endif; ?>

			<a href="<?php out('page_url', get('this_page'), 'attachments'); ?>">View or Attach Files</a>
	</p> 
</fieldset>
<?php else: ?>
<p class="center">If you are a <em>Registered</em> user, there are additional actions you can
perform on the page.</p>
<?php endif; ?>

<fieldset>
	<legend>Store page</legend>
	<input id="note" size="50" type="text" name="note" tabindex=2 value="<?php out('form_value', 'note'); ?>" onChange="form_changed()" /> <label for="note">Please add a note on your edit</label><br />
	<br />
	<input name="submit" type="submit" value="Store" accesskey="s" tabindex=3 onClick="form_unchanged()" />
	<input name="submit" type="submit" value="Preview" accesskey="p" tabindex=4 onClick="form_unchanged()" />
	<input type="button" value="Cancel" tabindex=5 onclick="document.location='<?php out('page_url', get('page_tag')); ?>';" onClick="form_unchanged()" />
</fieldset>
</form>
</div> <!-- end tab-content -->

<div class="tab-content formattingrules">
		<h1 class="tab" title="Formatting Rules">Formatting Rules</h1>
		<?php out('format', '<<include AbridgedFormattingRules>>'); ?>
</div>

</div> <!-- end tab container -->

<!-- This tabs.js must come after the tabs divs -->
<script type="text/javascript" src="<?php out('theme_url', 'tabs.js'); ?>"></script>

</div>
<!--closing page content-->

<?php theme_include('footer'); ?>
