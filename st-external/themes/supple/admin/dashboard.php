<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"  dir="ltr">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Dashboard : suppleText</title>
	<link rel="stylesheet" type="text/css" href="<?php out('theme_url', 'admin/admin.css'); ?>" />
</head>
<body>
<div id="header">
<h1><a href="<?php out('page_url', get('root_page')); ?>"><?php out('site_name'); ?></a></h1>
</div>

<div id="user_info"><p>Hello, <strong><?php out('logged_in_username'); ?></strong>. [<a href="<?php out('admin_url', 'users/logout'); ?>" title="Log out of this account">Sign Out</a>, <a href="<?php out('admin_url', 'users/myprofile'); ?>">My Profile</a>] </p></div>

<ul id="adminmenu">
<?php //Remember to add class="current" to the select page!
	$top_menu = get('top_menu'); 
	foreach($top_menu as $menu_entry):
?>
	<li><a href="<?php out('admin_url', $menu_entry['link_to']); ?>"><?php echo $menu_entry['name']; ?></a></li>
<?php
	endforeach;
?>
</ul>


<?php
	$top_menu = get('sub_menu', get('this_admin_page'));
	if(!empty($top_menu)): 
?>
<ul id="submenu">
<?php foreach($top_menu as $menu_entry): ?>
	<li><a href="<?php out('admin_url', $menu_entry['link_to']); ?>"><?php echo $menu_entry['name']; ?></a></li>
<?php endforeach; ?>
</ul>
<?php endif; ?>

<div id='moderated' class='updated fade'>
<p>1 comment marked as spam<br/>
</p></div>

<div class="wrap">

<p>Currently there are no comments for you to moderate.</p>

</div>


<div id="footer">
<p class="logo"><a href="http://wordpress.org/" id="wordpress-logo"><img src="images/wordpress-logo.png" alt="WordPress" /></a></p>
<p class="docs"><a href="http://codex.wordpress.org/">Documentation</a> &#8212; <a href="http://wordpress.org/support/">Support Forums</a><br />

2.2 &#8212; 1.22 seconds</p>
</div>
<script type="text/javascript">if(typeof wpOnload=='function')wpOnload();</script>
</body>
</html>
