<?php  if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>

<?php theme_include('header'); ?>

<?php if(!empty($this->validation->error_string)): ?>
		<?php echo $this->validation->error_string; ?>
<?php endif; ?>

<?php out('message'); ?>

<!--starting page content-->
<div class="wrap">

<h2 id="add-new-user">Add New User</h2>

<div class="narrow">

<p>Users can <a href="<?php out('admin_url', 'users/register'); ?>">register themselves</a>, or you can manually create users here.</p>
<form action="<?php out('admin_url', 'users/addnew'); ?>" method="post" name="adduser" id="adduser">
<table class="editform" width="100%" cellspacing="2" cellpadding="5">
	<tr>
		<th scope="row" width="33%">Username (required)</th>
		<td width="66%"><input name="user_login" type="text" id="user_login" value="<?php echo $this->validation->user_login; ?>" /></td>
	</tr>
	<tr>
		<th scope="row">E-mail (required)</th>
		<td><input name="email" type="text" id="email" value="<?php echo $this->validation->email; ?>" /></td>
	</tr>
	<tr>
		<th scope="row">Password (twice) </th>
		<td><input name="pass1" type="password" id="pass1" />
		<br />
		<input name="pass2" type="password" id="pass2" /></td>
	</tr>
	<tr>
		<th scope="row">Role</th>
		<td>
			<select name="role" id="role">
				<option value='Registered' <?php echo $this->validation->set_select('role', 'Registered'); ?>>Registered</option>
				<option value='Editor' <?php echo $this->validation->set_select('role', 'Editor'); ?>>Editor</option>
				<option value='Administrator' <?php echo $this->validation->set_select('role', 'Administrator'); ?>>Administrator</option>
			</select>
		</td>
	</tr>
</table>
<p class="submit">
	<input name="adduser" type="submit" id="addusersub" value="Add User &raquo;" />
</p>

</form>

</div> <!--End div narrow-->
</div>
<!--closing page content-->

<?php theme_include('footer'); ?>
