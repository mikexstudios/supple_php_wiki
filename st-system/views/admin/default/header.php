<?php  if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"  dir="ltr">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?php out('admin_page_title'); ?> : suppleText</title>
	<!-- 
	suppleText's administration XHTML and CSS code is based off of Wordpress'
	administration theme (http://wordpress.com). Thanks, Wordpress!
	-->
	<link rel="stylesheet" type="text/css" href="<?php out('admin_theme_url', 'admin.css'); ?>" />

	<script type="text/javascript" src="<?php out('admin_theme_url', 'niftycube.js'); ?>"></script>
	<script type="text/javascript">
		window.onload=function() {
			Nifty("div.wrap h2","big");
			Nifty("div.error","tl bottom normal");
			Nifty("ul#adminmenu","top normal");
		}
	</script>
</head>
<body>
<div id="header">
<h1><a href="<?php out('page_url', get('root_page')); ?>"><?php out('site_name'); ?></a> <span>(<a href="<?php out('page_url', get('root_page')); ?>">View site &raquo;</a>)</span></h1>
</div>

<div id="user_info"><p>Hello, <strong><?php out('logged_in_username'); ?></strong>. [<a href="<?php out('admin_url', 'users/logout'); ?>" title="Log out of this account">Sign Out</a>, <a href="<?php out('admin_url', 'users/profile'); ?>">My Profile</a>] </p></div>

<ul id="adminmenu">
<?php //Remember to add class="current" to the select page!
	$top_menu = get('top_menu'); 
	foreach($top_menu as $menu_entry):
?>
	<li><a href="<?php out('admin_url', $menu_entry['link_to']); ?>" <?php if($menu_entry['link_to']==get('this_admin_page')) {echo 'class="current"';} ?>><?php echo $menu_entry['name']; ?></a></li>
<?php
	endforeach;
?>
</ul>


<?php
	$top_menu = get('sub_menu', get('this_admin_page'));
	$subpage_name = get('this_admin_subpage'); 
	if(!empty($top_menu)): 
		if(empty($subpage_name)) 
			{ $subpage_name = element('link_to', reset($top_menu)); } //reset() gets first element of array non-destructively
		else
			{ $subpage_name = get('this_admin_page').'/'.get('this_admin_subpage'); }
?>
<ul id="submenu">
<?php foreach($top_menu as $menu_entry): ?>
	<li><a href="<?php out('admin_url', $menu_entry['link_to']); ?>" <?php if($menu_entry['link_to']==$subpage_name) {echo 'class="current"';} ?>><?php echo $menu_entry['name']; ?></a></li>
<?php endforeach; ?>
</ul>
<?php else: //If we don't have a submenu ?>
<div id="minisub"></div>
<?php endif; ?>

<!--
<div id='moderated' class='updated fade'>
<p>1 comment marked as spam</p>
</div>
-->
