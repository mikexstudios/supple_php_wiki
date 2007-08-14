<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Admin theme helper functions
 */ 

$CI =& get_instance();

$CI->template->add_function('logged_in_username', 'get_logged_in_username');
function get_logged_in_username() {
	global $CI;
	
	return $CI->session->userdata('username');
}

$CI->template->add_function('this_admin_page', 'get_current_admin_pagename');
function get_current_admin_pagename() {
	global $CI;
	
	//NOTE: URI automatically initialized by the system.
	$pagename = $CI->uri->segment(2);
	if(!empty($pagename))
	{
		return $CI->uri->segment(2);
	}
	
	return 'dashboard'; //Default admin page
}

$CI->template->add_function('this_admin_subpage', 'get_current_admin_subpagename');
function get_current_admin_subpagename() {
	global $CI;
	
	//NOTE: URI automatically initialized by the system.
	$subpagename = $CI->uri->segment(3);
	if(!empty($subpagename))
	{
		return $CI->uri->segment(3);
	}
	
	return '';
}

$CI->template->add_function('admin_url', 'construct_admin_url');
function construct_admin_url($page, $handler='', $args='') {
	return construct_page_url('st-admin/'.$page, $handler, $args);
}

$CI->template->add_function('top_menu', 'get_top_menu');
function get_top_menu() {
	global $CI;
	
	return $CI->adminmenu->get_top_level();
}

$CI->template->add_function('sub_menu', 'get_sub_menu');
function get_sub_menu($in_associated_page='') {
	global $CI;
	
	return $CI->adminmenu->get_sub_level($in_associated_page);
}

function admin_theme_include($file) {
	theme_include('admin/'.$file);
}

?>
