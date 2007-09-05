<?php  if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>

<?php theme_include('header'); ?>

<!--starting page content-->
<div class="wrap">

<?php if(!empty($this->validation->error_string)): ?>
		<?php echo $this->validation->error_string; ?>
<?php endif; ?>

<?php out('message'); ?>

<h2>Permission Options</h2>
<form method="post" action="<?php out('admin_url', 'options/general'); ?>"> 
<p class="submit"><input type="submit" name="Submit" value="Update Options &raquo;" /></p>

<table class="optiontable"> 

<tr valign="top"> 
<th scope="row">Default read role(s):</th> 
<td>
	<input name="default_read_permission" type="text" id="default_read_permission" value="<?php if(empty($this->validation->default_read_permission)) { out('setting', 'default_read_permission'); } else { echo $this->validation->default_read_permission; } ?>" size="40" /><br />
	Enter roles separated by a comma allowed to view pages (ie. Anonymous, Registered, Editor). <em>These roles can be overridden on specific pages</em>.
</td> 
</tr>

<tr valign="top"> 
<th scope="row">Default write role(s):</th> 
<td>
	<input name="default_write_permission" type="text" id="default_write_permission" value="<?php if(empty($this->validation->default_write_permission)) { out('setting', 'default_write_permission'); } else { echo $this->validation->default_write_permission; } ?>" size="40" /><br />
	Enter roles separated by a comma allowed to edit pages (ie. Registered, Editor). <em>These roles can be overridden on specific pages</em>.
</td> 
</tr>

</table> 

<p class="submit"><input type="submit" name="Submit" value="Update Options &raquo;" /></p>
</form>

</div>
<!--closing page content-->

<?php theme_include('footer'); ?>
