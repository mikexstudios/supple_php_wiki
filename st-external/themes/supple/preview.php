<?php  if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>
<?php theme_include('header'); ?>

<!--starting page content-->
<div class="error">
	<h2>Note: This is a preview. If everything looks good, click 'Store' to save your changes. Otherwise, you can re-edit the page.</h2>
</div>

<div id="page">

<?php out('page_content'); ?>

<form action="<?php out('current_url'); ?>" method="post">
<input type="hidden" name="body" value="<?php out('form_value', 'body'); ?>" />
<fieldset>
	<legend>Store page</legend>
	<input id="note" size="50" type="text" name="note" value="<?php out('form_value', 'note'); ?>" /> <label for="note">Please add a note on your edit</label><br />
	<br />
	<input name="submit" type="submit" value="Store" accesskey="s" />
	<input name="submit" type="submit" value="Re-edit" accesskey="r" />
	<input type="button" value="Cancel" onclick="document.location='<?php out('page_url', get('page_tag')); ?>';" />
</fieldset>
</form>

</div>
<!--closing page content-->

<?php theme_include('footer'); ?>
