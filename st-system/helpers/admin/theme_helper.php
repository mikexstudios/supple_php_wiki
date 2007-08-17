<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Admin theme helper functions
 */ 

$CI =& get_instance();

$CI->template->add_function('theme_data', 'get_theme_data');
function get_theme_data($in_theme_name) {
	return @include_once ABSPATH.THEMES_DIR.$in_theme_name.'/theme-info.php';
}

$CI->template->add_function('theme_directories', 'get_theme_directories');
function get_theme_directories() {
	global $CI;
	$CI->load->helper('directory');
	
	$map = directory_map(ABSPATH.THEMES_DIR, TRUE); //TRUE = only top level dir
	//print_r($map);
	
	return $map;
}

$CI->template->add_function('avaliable_themes', 'get_avaliable_themes');
function get_avaliable_themes() {
	global $CI;
	
	//Check if directories also exclude current theme
	$theme_directories_temp = get_theme_directories();
	$current_theme = $CI->settings->get('use_theme');
	$theme_directories = array();
	foreach($theme_directories_temp as $each_theme_directory)
	{
		if(is_dir(ABSPATH.THEMES_DIR.$each_theme_directory) && $each_theme_directory != $current_theme)
		{
			$theme_directories[] = $each_theme_directory;
		}
	}
	
	return $theme_directories;
}

function does_theme_exist($in_theme_name) {
	if(file_exists(ABSPATH.THEMES_DIR.$in_theme_name))
	{
		return true;
	}
	
	return false;
}


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

/* //Moved to the regular theme_helper
$CI->template->add_function('setting', 'get_setting');
function get_setting($in_key) {
	global $CI;
	
	return $CI->settings->get($in_key);
}
*/

$CI->template->add_function('message', 'get_message');
function get_message() {
	global $CI;
	
	return $CI->message->get();
}

$CI->template->add_function('all_users_info', 'get_all_users_info');
function get_all_users_info() {
	global $CI;
	
	$CI->load->model('users_model');
	return $CI->users_model->get_all();
}

$CI->template->add_function('user_info', 'get_user_info');
function get_user_info($in_username, $in_key) {
	global $CI;
	
	$CI->load->model('users_model', 'users_model_theme');
	$CI->users_model_theme->username = $in_username;
	return $CI->users_model_theme->get_value($in_key);
}

$CI->template->add_function('full_domain_name', 'get_site_full_domain_name');
function get_site_full_domain_name() {
	//Get base url of site
	if(preg_match('%^\S+://(\S+\.\S+?)/.*$%', base_url(), $matches))
	{
		$site_domain_name = $matches[1];
	}
	else
	{
		$site_domain_name = 'example.com';
	}
	
	return $site_domain_name;
}

?>
