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
	<select name="read_permission" id="read_permission">
		<option value='Default' <?php $read_permission = $this->input->post('read_permission'); if(empty($read_permission)) { echo 'selected="selected"'; } ?>>Default Permissions</option>
		<option value='Anonymous' <?php echo $this->validation->set_select('read_permission', 'Anonymous'); ?>>Anonymous or Higher</option>
		<option value='Registered' <?php echo $this->validation->set_select('read_permission', 'Registered'); ?>>Registered or Higher</option>
		<option value='Editor' <?php echo $this->validation->set_select('read_permission', 'Editor'); ?>>Editors or Higher</option>
		<option value='Administrator' <?php echo $this->validation->set_select('read_permission', 'Administrator'); ?>>Administrators Only</option>
	</select>
	<br />
	Select the role(s) need to view the <?php out('page_name'); ?> page (ie. Registered or Higher). <em class="highlight">The <a href="<?php out('admin_url', 'options/permissions'); ?>">default permissions</a> can be changed on the Options page</em>.
</td> 
</tr>

<tr valign="top"> 
<th scope="row">Write role(s):</th> 
<td>
	<select name="write_permission" id="write_permission">
		<option value='Default' <?php $write_permission = $this->input->post('write_permission'); if(empty($write_permission)) { echo 'selected="selected"'; } ?>>Default Permissions</option>
		<option value='Anonymous' <?php echo $this->validation->set_select('write_permission', 'Anonymous'); ?>>Anonymous or Higher</option>
		<option value='Registered' <?php echo $this->validation->set_select('write_permission', 'Registered'); ?>>Registered or Higher</option>
		<option value='Editor' <?php echo $this->validation->set_select('write_permission', 'Editor'); ?>>Editors or Higher</option>
		<option value='Administrator' <?php echo $this->validation->set_select('write_permission', 'Administrator'); ?>>Administrators Only</option>
	</select>
	<br />
	Select the role(s) need to edit the <?php out('page_name'); ?> page (ie. Editors or Higher). <em class="highlight">The <a href="<?php out('admin_url', 'options/permissions'); ?>">default permissions</a> can be changed on the Options page</em>.
</td> 
</tr>

</table> 

<p class="submit"><input type="submit" name="Submit" value="Change Permissions &raquo;" /></p>
</form>

</div>
<!--closing page content-->

<?php theme_include('footer'); ?>
