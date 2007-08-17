<?php  if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr">
<head>
	<title>Lost Password : <?php out('site_name'); ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<link rel="stylesheet" href="<?php out('theme_url', 'admin/login.css'); ?>" type="text/css" />
</head>
<body>

<div id="login">

<h1>Lost Password for <a href="<?php out('page_url', get('root_page')); ?>"><?php out('site_name'); ?></a></h1>

<?php if(!empty($this->validation->error_string)): ?>
	<?php echo $this->validation->error_string; ?>
<?php endif; ?>

<?php out('message'); ?>

<form name="loginform" id="loginform" action="<?php out('admin_url', 'users/lostpassword'); ?>" method="post">
	<p>
		<label>Username:<br />
		<input type="text" name="user_login" class="input" value="<?php echo $this->validation->user_login; ?>" size="20" tabindex="10" /></label>
	</p>
	<p>
		<label>Email:<br />
		<input type="text" name="user_email" class="input" value="<?php echo $this->validation->user_email; ?>" size="20" tabindex="20" /></label>
	</p>
	<p><strong>A password will be e-mailed to you.</strong></p>
	<p class="submit">
		<input type="submit" name="lostpassword" class="submit_button" value="Get New Password &raquo;" tabindex="100" />
	</p>
</form>

</div>

<ul>
	<li><a href="<?php out('admin_url', 'users/login'); ?>">Login</a></li>
	<li><a href="<?php out('admin_url', 'users/register'); ?>">Register</a></li>
</ul>

</body>
</html>
