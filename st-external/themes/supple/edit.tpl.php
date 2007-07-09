<?php theme_include('header.tpl.php'); ?>

<!--starting page content-->
<div id="page">

<form action="<?php out('current_url'); ?>" method="post">
<textarea id="body" name="body">
<?php out('page_content'); ?>
</textarea><br />
<fieldset>
	<legend>Store page</legend>
	<input id="note" size="50" type="text" name="note" value="" /> <label for="note">Please add a note on your edit</label><br />
	<br />
	<input name="submit" type="submit" value="Store" accesskey="s" />
	<input name="submit" type="submit" value="Preview" accesskey="p" />
	<input type="button" value="Cancel" onclick="document.location='<?php out('page_url', get('page_tag')); ?>';" />
</fieldset>
</form>

</div>
<!--closing page content-->

<?php theme_include('footer.tpl.php'); ?>