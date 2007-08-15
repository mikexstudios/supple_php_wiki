<?php  if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>

<?php admin_theme_include('header'); ?>

<?php out('message'); ?>

<!--starting page content-->
<div class="wrap">

<h2>User List by Role</h2>
	
<form action="" method="get" name="search" id="search">
	<p><input type="text" name="usersearch" id="usersearch" value="" /> <input type="submit" value="Search Users &raquo;" class="button" /></p>
</form>
	
<h3>1 shown below</h3>

<form action="" method="post" name="updateusers" id="updateusers">
<input type="hidden" name="_wpnonce" value="176098e012" /><input type="hidden" name="_wp_http_referer" value="/wordpress_trunk/wp-admin/users.php" /><table class="widefat">
<tbody>
<tr>
	<th colspan="7"><h3>Administrator</h3></th>
</tr>
<tr class="thead">
	<th>ID</th>
	<th>Username</th>
	<th>Name</th>

	<th>E-mail</th>
	<th>Website</th>
	<th colspan="2" style="text-align: center">Actions</th>
</tr>
</tbody>
<tbody id="role-administrator">
	<tr id='user-1' class="alternate">
		<td><input type='checkbox' name='users[]' id='user_1' value='1' /> <label for='user_1'>1</label></td>

		<td><label for='user_1'><strong>admin</strong></label></td>
		<td><label for='user_1'> </label></td>
		<td><a href='mailto:mike.huynh@gmail.com' title='e-mail: mike.huynh@gmail.com'>mike.huynh@gmail.com</a></td>
		<td><a href='http://' title='website: http://'></a></td>
		<td align='center'><a href='edit.php?author=1' title='View posts by this author' class='edit'>View 1 post</a></td>
		<td><a href='user-edit.php?user_id=1&wp_http_referer=%2Fwordpress_trunk%2Fwp-admin%2Fusers.php' class='edit'>Edit</a></td>

	</tr>
</tbody>
</table>


	<h3>Update Selected</h3>
	<ul style="list-style:none;">
		<li><input type="radio" name="action" id="action0" value="delete" /> <label for="action0">Delete checked users.</label></li>
		<li>

			<input type="radio" name="action" id="action1" value="promote" /> <label for="action1">Set the Role of checked users to:</label>
			<select name="new_role" onchange="getElementById('action1').checked = 'true'">
	<option value='administrator'>Administrator</option>
	<option value='editor'>Editor</option>
	<option value='author'>Author</option>
	<option value='contributor'>Contributor</option>

	<option value='subscriber'>Subscriber</option></select>
		</li>
	</ul>
	<p class="submit"><input type="submit" value="Bulk Update &raquo;" /></p>
</form>


<h2 id="add-new-user">Add New User</h2>

<div id="ajax-response"></div>

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
