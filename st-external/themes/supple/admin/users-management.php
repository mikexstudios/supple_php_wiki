<?php  if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>

<?php admin_theme_include('header'); ?>

<?php if(!empty($this->validation->error_string)): ?>
		<?php echo $this->validation->error_string; ?>
<?php endif; ?>

<?php out('message'); ?>

<!--starting page content-->
<div class="wrap">

<h2>User List by Role</h2>

<form action="<?php out('admin_url', 'users/management'); ?>" method="post" name="updateusers" id="updateusers">
<table class="widefat">

<tbody>
<tr class="thead">
	<th>ID</th>
	<th>Username</th>
	<th>E-mail</th>
	<th>Role</th>
	<th>Actions</th>
</tr>
</tbody>

<tbody>
<?php 
	$all_users_info = get('all_users_info'); 
	//print_r($all_users_info);
	foreach($all_users_info as $each_username => $each_user_info): 
?>
	<tr id='user-<?php echo $each_user_info['uid']; ?>' <?php echo alternator('class="alternate"', ''); ?>>
		<td><input type='checkbox' name='users[]' id='user_<?php echo $each_user_info['uid']; ?>' value='<?php echo $each_user_info['uid']; ?>' /> <label for='user_<?php echo $each_user_info['uid']; ?>'><?php echo $each_user_info['uid']; ?></label></td>
		<td><label for='user_<?php echo $each_user_info['uid']; ?>'><strong><?php echo $each_username; ?></strong></label></td>
		<td><a href='mailto:<?php echo @$each_user_info['email']; ?>' title='e-mail: <?php echo $each_user_info['email']; ?>'><?php echo $each_user_info['email']; ?></a></td>
		<td><?php echo $each_user_info['role']; ?></td>
		<td><a href='<?php out('admin_url', 'users/profile/'.$each_username); ?>' class='edit'>Edit</a></td>
	</tr>
<?php
	endforeach;	
?>
</tbody>
</table>

<h3>Update Selected</h3>
<ul style="list-style:none;">
	<li><input type="radio" name="action" id="action0" value="delete" <?php echo $this->validation->set_radio('action', 'delete'); ?>/> <label for="action0">Delete checked users.</label></li>
	<li>
		<input type="radio" name="action" id="action1" value="promote" <?php echo $this->validation->set_radio('action', 'promote'); ?>/> <label for="action1">Set the Role of checked users to:</label>
		<select name="new_role" onchange="getElementById('action1').checked = 'true'">
			<option value='Registered' <?php echo $this->validation->set_select('new_role', 'Registered'); ?>>Registered</option>
			<option value='Editor' <?php echo $this->validation->set_select('new_role', 'Editor'); ?>>Editor</option>
			<option value='Administrator' <?php echo $this->validation->set_select('new_role', 'Administrator'); ?>>Administrator</option>
		</select>
	</li>
</ul>
<p class="submit"><input type="submit" value="Bulk Update &raquo;" /></p>
</form>

<h2 id="add-new-user">Add New User</h2>

<div class="narrow">

<p>Users can <a href="<?php out('admin_url', 'users/register'); ?>">register themselves</a>, or you can manually create users here.</p>
<form action="<?php out('admin_url', 'users/addnew'); ?>" method="post" name="adduser" id="adduser">
<table class="editform" width="100%" cellspacing="2" cellpadding="5">
	<tr>
		<th scope="row" width="33%">Username (required)</th>
		<td width="66%"><input name="user_login" type="text" id="user_login" value="" /></td>
	</tr>
	<tr>
		<th scope="row">E-mail (required)</th>
		<td><input name="email" type="text" id="email" value="" /></td>
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
				<option value='subscriber'>Registered</option>
				<option value='editor'>Editor</option>
				<option value='administrator'>Administrator</option>
			</select>
		</td>
	</tr>
</table>
<p class="submit">
	<input name="adduser" type="submit" id="addusersub" value="Add User &raquo;" />
</p>

</form>

</div>
</div>

<!--closing page content-->

<?php admin_theme_include('footer'); ?>
