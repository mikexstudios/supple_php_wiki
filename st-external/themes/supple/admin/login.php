<?php  if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr">
<head>
	<title>Login : <?php out('site_name'); ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<link rel="stylesheet" href="<?php out('theme_url', 'admin/login.css'); ?>" type="text/css" />
</head>
<body>

<div id="login">

<h1>Login to <a href="<?php out('page_url', get('root_page')); ?>"><?php out('site_name'); ?></a></h1>

<?php if(!empty($this->validation->error_string)): ?>
	<?php echo $this->validation->error_string; ?>
<?php endif; ?>

<form name="loginform" id="loginform" action="<?php out('current_url'); ?>" method="post">
	<p>
		<label>Username:<br />
		<input type="text" name="user_login" class="input" value="<?php echo $this->validation->user_login; ?>" size="20" tabindex="10" /></label>
	</p>
	<p>
		<label>Password:<br />

		<input type="password" name="user_password" class="input" value="" size="20" tabindex="20" /></label>
	</p>
	<p><label><input name="rememberme" type="checkbox" value="true" <?php echo $this->validation->set_checkbox('rememberme', 'true'); ?> tabindex="90" /> Remember me</label></p>
	<p class="submit">
		<input type="submit" name="st-submit" class="submit_button" value="Login &raquo;" tabindex="100" />
	</p>
</form>

</div>

<ul>
	<!-- <li><a href="http://local.suppletext.org/wordpress_trunk/wp-login.php?action=lostpassword" title="Password Lost and Found">Lost your password?</a></li> -->
</ul>


</body>
</html>
