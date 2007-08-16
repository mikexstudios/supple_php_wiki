<?php  if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>

<?php admin_theme_include('header'); ?>

<?php out('message'); ?>

<!--starting page content-->
<div class="wrap">

<h2>User List by Role</h2>

<form action="" method="post" name="updateusers" id="updateusers">
<table class="widefat">

<tbody>
<tr class="thead">
	<th>ID</th>
	<th>Username</th>
	<th>E-mail</th>
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
		<td><a href='mailto:<?php echo $each_user_info['email']; ?>' title='e-mail: <?php echo $each_user_info['email']; ?>'><?php echo $each_user_info['email']; ?></a></td>
		<td><a href='<?php out('admin_url', 'users/profile/'.$each_username); ?>' class='edit'>Edit</a></td>
	</tr>
<?php
	endforeach;	
?>
</tbody>
</table>

<h3>Update Selected</h3>
<ul style="list-style:none;">
	<li><input type="radio" name="action" id="action0" value="delete" /> <label for="action0">Delete checked users.</label></li>
	<li>
		<input type="radio" name="action" id="action1" value="promote" /> <label for="action1">Set the Role of checked users to:</label>
		<select name="new_role" onchange="getElementById('action1').checked = 'true'">
			<option value='registered'>Registered</option>
			<option value='editor'>Editor</option>
			<option value='administrator'>Administrator</option>
		</select>
	</li>
</ul>
<p class="submit"><input type="submit" value="Bulk Update &raquo;" /></p>
</form>

<h2 id="add-new-user">Add New User</h2>

<div class="narrow">

<p>Users cannot currently <a href="http://local.suppletext.org/wordpress_trunk/wp-admin/options-general.php#users_can_register">register themselves</a>, but you can manually create users here.</p><form action="#add-new-user" method="post" name="adduser" id="adduser">
<input type="hidden" name="_wpnonce" value="afcef61782" /><input type="hidden" name="_wp_http_referer" value="/wordpress_trunk/wp-admin/users.php" /><table class="editform" width="100%" cellspacing="2" cellpadding="5">
	<tr>
		<th scope="row" width="33%">Username (required)<input name="action" type="hidden" id="action" value="adduser" /></th>

		<td width="66%"><input name="user_login" type="text" id="user_login" value="" /></td>
	</tr>
	<tr>
		<th scope="row">First Name </th>
		<td><input name="first_name" type="text" id="first_name" value="" /></td>
	</tr>
	<tr>
		<th scope="row">Last Name </th>

		<td><input name="last_name" type="text" id="last_name" value="" /></td>
	</tr>
	<tr>
		<th scope="row">E-mail (required)</th>
		<td><input name="email" type="text" id="email" value="" /></td>
	</tr>
	<tr>
		<th scope="row">Website</th>

		<td><input name="url" type="text" id="url" value="" /></td>
	</tr>

	<tr>
		<th scope="row">Password (twice) </th>
		<td><input name="pass1" type="password" id="pass1" />
		<br />
		<input name="pass2" type="password" id="pass2" /></td>
	</tr>

	<tr>
		<th scope="row">Role</th>
		<td><select name="role" id="role">
			
	<option selected='selected' value='subscriber'>Subscriber</option>
	<option value='administrator'>Administrator</option>
	<option value='editor'>Editor</option>

	<option value='author'>Author</option>
	<option value='contributor'>Contributor</option>			</select>
		</td>
	</tr>
</table>
<p class="submit">
	<input type="hidden" name="wp_http_referer" value="/wordpress_trunk/wp-admin/users.php" />	<input name="adduser" type="submit" id="addusersub" value="Add User &raquo;" />
</p>

</form>

</div>
</div>

<!--closing page content-->

<?php admin_theme_include('footer'); ?>
