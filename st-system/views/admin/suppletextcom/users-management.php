<?php  if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>

<?php theme_include('header'); ?>

<!--starting page content-->
<div class="wrap">

<?php if(!empty($this->validation->error_string)): ?>
		<?php echo $this->validation->error_string; ?>
<?php endif; ?>

<?php out('message'); ?>

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
<p class="submit"><input name="bulkupdate" type="submit" value="Bulk Update &raquo;" /></p>
</form>

</div>
<!--closing page content-->

<?php theme_include('footer'); ?>
