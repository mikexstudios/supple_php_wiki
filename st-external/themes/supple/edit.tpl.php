<?php theme_include('header.tpl.php'); ?>

<!--starting page content-->
<div class="page">

<form action="<?php out('current_url'); ?>" method="post">
<textarea id="body" name="body">
<?php out('page_content'); ?>
</textarea><br />
<fieldset><legend>Store page</legend>
<input id="note" size="50" type="text" name="note" value="" /> <label for="note">Please add a note on your edit</label><br />
<input name="submit" type="submit" value="Store" accesskey="s" />

<input name="submit" type="submit" value="Preview" accesskey="p" />
<input type="button" value="Cancel" onclick="document.location='http://localhost/wikka-trunk/wikka.php?wakka=SandBox';" />
</fieldset>
</form>
<script type="text/javascript" src="<?php out('theme_url', 'wikiedit/protoedit.js'); ?>"></script>
<script type="text/javascript" src="<?php out('theme_url', 'wikiedit/wikiedit2.js'); ?>"></script>
<script type="text/javascript">  
	wE = new WikiEdit(); wE.init('body','WikiEdit','editornamecss', '<?php out('theme_url', 'wikiedit/images/'); ?>');
</script>


	
</div>
<!--closing page content-->

<?php theme_include('footer.tpl.php'); ?>