<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
	<title><?php out('page_tag'); ?> : <?php out('site_name'); ?></title>
	<link rel="stylesheet" type="text/css" href="<?php out('theme_url', 'style.css'); ?>" />
	<link rel="stylesheet" type="text/css" href="<?php out('theme_url', 'print.css'); ?>" media="print" />
	<link rel="icon" href="<?php out('theme_url', 'favicon.ico'); ?>" type="image/x-icon" />
	<link rel="shortcut icon" href="<?php out('theme_url', 'favicon.ico'); ?>" type="image/x-icon" />
</head>
<body>
<div class="header">
	<h2><?php out('site_name'); ?> : <a href="<?php out('page_url', get('page_tag')); ?>"><?php out('page_tag'); ?></a></h2>
</div>