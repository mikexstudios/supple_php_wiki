<?php  if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>

<?php theme_include('header'); ?>

<!--starting page content-->
<div class="wrap">

<?php if(!empty($this->validation->error_string)): ?>
		<?php echo $this->validation->error_string; ?>
<?php endif; ?>

<?php out('message'); ?>

<h2>Permission Options</h2>
<form method="post" action="<?php out('admin_url', 'options/permissions'); ?>"> 
<p class="submit"><input type="submit" name="Submit" value="Update Options &raquo;" /></p>

<table class="optiontable"> 

<tr valign="top"> 
<th scope="row">Default read role(s):</th> 
<td>
	<select name="default_read_permission" id="default_read_permission">
		<option value='Anonymous' <?php $default_read_permission = $this->input->post('default_read_permission'); if(empty($default_read_permission)) { echo 'selected="selected"'; } else { echo $this->validation->set_select('default_read_permission', 'Anonymous'); } ?>>Anonymous or Higher</option>
		<option value='Registered' <?php echo $this->validation->set_select('default_read_permission', 'Registered'); ?>>Registered or Higher</option>
		<option value='Editor' <?php echo $this->validation->set_select('default_read_permission', 'Editor'); ?>>Editors or Higher</option>
		<option value='Administrator' <?php echo $this->validation->set_select('default_read_permission', 'Administrator'); ?>>Administrators Only</option>
	</select>
	<br />
	Select the default permission(s) needed to view pages (ie. Anonymous or Higher). <em class="highlight">These roles can be overridden on specific pages</em>.
</td> 
</tr>

<tr valign="top"> 
<th scope="row">Default write role(s):</th> 
<td>
	<select name="default_write_permission" id="default_write_permission">
		<option value='Anonymous' <?php echo $this->validation->set_select('default_write_permission', 'Anonymous'); ?>>Anonymous or Higher</option>
		<option value='Registered' <?php $default_write_permission = $this->input->post('default_write_permission'); if(empty($default_write_permission)) { echo 'selected="selected"'; } else { echo $this->validation->set_select('default_write_permission', 'Registered'); } ?>>Registered or Higher</option>
		<option value='Editor' <?php echo $this->validation->set_select('default_write_permission', 'Editor'); ?>>Editors or Higher</option>
		<option value='Administrator' <?php echo $this->validation->set_select('default_write_permission', 'Administrator'); ?>>Administrators Only</option>
	</select>
	<br />
	Select the default permission(s) needed to edit pages (ie. Registered or Higher). <em class="highlight">These roles can be overridden on specific pages</em>.
</td> 
</tr>

</table> 

<p class="submit"><input type="submit" name="Submit" value="Update Options &raquo;" /></p>
</form>

</div>
<!--closing page content-->

<?php theme_include('footer'); ?>
