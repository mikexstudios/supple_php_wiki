<?php  if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>

<?php theme_include('header'); ?>

<?php if(!empty($this->validation->error_string)): ?>
		<?php echo $this->validation->error_string; ?>
<?php endif; ?>

<?php out('message'); ?>

<!--starting page content-->
<div class="wrap">

<?php $username = get('logged_in_username'); ?>

<h2>Profile and Personal Options</h2>
<form action="<?php out('admin_url', 'users/profile'); ?>" name="profile" id="your-profile" method="post">

<fieldset>
<legend>Name</legend>
	<p><label>Username: (no editing)<br />
	<input type="text" name="user_login" value="<?php echo $username; ?>" disabled="disabled" />
	</label></p>
	
	<p><label>E-mail: (required)<br />
	<input type="text" name="email" value="<?php out('user_info', 'email', $username); ?>" /></label></p>
</fieldset>

<fieldset>
<legend>Update Your Password</legend>
	<p class="desc">If you would like to change your password type a new one twice below. Otherwise leave this blank.</p>
	<p><label>New Password:<br />
	<input type="password" name="pass1" size="16" value="" />
	
	</label></p>
	<p><label>Type it one more time:<br />
	<input type="password" name="pass2" size="16" value="" />
	</label></p>
</fieldset>


<br clear="all" />

	<table width="99%"  border="0" cellspacing="2" cellpadding="3" class="editform">
		  </table>
<p class="submit"><input type="submit" value="Update Profile &raquo;" name="updateprofile" /></p>
</form>

</div>
<!--closing page content-->

<?php theme_include('footer'); ?>
