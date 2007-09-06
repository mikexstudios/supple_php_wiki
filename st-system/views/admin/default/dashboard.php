<?php  if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>

<?php theme_include('header'); ?>

<!--starting page content-->
<div class="wrap">

<h2>Welcome to suppleText</h2>

<p>Use these links to get started:</p>

<ul>
<?php $user_role = get_user_role(); ?>
	<li><a href="<?php out('admin_url', 'users/profile'); ?>">Update your profile or change your password</a></li>
<?php if($user_role == 'Administrator'): ?>
	<li><a href="<?php out('admin_url', 'presentation'); ?>">Change your site&#8217;s look or theme</a></li>
	<li><a href="<?php out('admin_url', 'options'); ?>">Change your wiki settings</a></li>
<?php endif; ?>
</ul>

<p>Need help with suppleText? Please see our 
<a href='http://www.suppletext.org/Documentation'>documentation</a> or visit 
the <a href='http://www.suppletext.org/forums/'>support forums</a>.</p>

</div>
<!--closing page content-->

<?php theme_include('footer'); ?>
