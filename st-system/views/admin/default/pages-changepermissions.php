<?php  if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>

<?php theme_include('header'); ?>

<!--starting page content-->
<div class="wrap">

<?php if(!empty($this->validation->error_string)): ?>
		<?php echo $this->validation->error_string; ?>
<?php endif; ?>

<?php out('message'); ?>

<h2><a href="<?php out('page_url', get('page_name')); ?>"><?php out('page_name'); ?></a> Page Permissions</h2>
<form method="post" action="<?php out('admin_url', 'pages/changepermissions/'.get('page_name').'/'); ?>"> 

<table class="optiontable"> 

<tr valign="top"> 
<th scope="row">Read role(s):</th> 
<td>
	<input name="read_permission" type="text" id="read_permission" value="<?php if(empty($this->validation->read_permission)) { out('page_metadata', 'read_permission', get('page_name')); } else { echo $this->validation->read_permission; } ?>" size="40" /><br />
	Enter roles separated by a comma allowed to view pages (ie. Anonymous, Registered, Editor). <em class="highlight">Leave blank for the <a href="<?php out('admin_url', 'options/permissions'); ?>">default permissions</a></em>.
</td> 
</tr>

<tr valign="top"> 
<th scope="row">Write role(s):</th> 
<td>
	<input name="write_permission" type="text" id="write_permission" value="<?php if(empty($this->validation->write_permission)) { out('page_metadata', 'write_permission', get('page_name')); } else { echo $this->validation->write_permission; } ?>" size="40" /><br />
	Enter roles separated by a comma allowed to edit pages (ie. Registered, Editor). <em class="highlight">Leave blank for the <a href="<?php out('admin_url', 'options/permissions'); ?>">default permissions</a></em>.
</td> 
</tr>

</table> 

<p class="submit"><input type="submit" name="Submit" value="Change Permissions &raquo;" /></p>
</form>

</div>
<!--closing page content-->

<?php theme_include('footer'); ?>
