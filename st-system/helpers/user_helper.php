<?php
/**
 * Helper functions related to user management
 */

$CI =& get_instance();

$CI->template->add_function('logged_in_username', 'get_logged_in_username');
function get_logged_in_username() {
	global $CI;
	
	return $CI->session->userdata('username');
}

$CI->template->add_function('user_info', 'get_user_info');
function get_user_info($in_key, $in_username='') {
	global $CI;
	
	$CI->load->model('users_model', 'users_model_theme');
	if(empty($in_username))
	{
		$in_username = get_logged_in_username();
	}
	
	//Check again if the username is set. This fixes the problem
	//where if the username is not set, strange values can be
	//returned.
	if(empty($in_username))
	{
		return false;
	}
	
	$CI->users_model_theme->username = $in_username;
	return $CI->users_model_theme->get_value($in_key);
}

$CI->template->add_function('user_role', 'get_user_role');
function get_user_role($in_username='') {
	$user_role = get_user_info('role', $in_username);
	if(!empty($user_role))
	{
		return $user_role; 
	}
	
	return 'Anonymous';
} 

function does_user_have_permission($lowest_permission_level='Administrator', $in_username='') {

	$user_role = get_user_role($in_username);
	
	//Convert permissions into numbers
	$user_role_number = permission_to_number($user_role);
	$lowest_permission_level_number = permission_to_number($lowest_permission_level);
	
	if($user_role_number >= $lowest_permission_level_number)
	{
		return true;
	}
	
	return false;
}

/**
 * Converts a permission (ie. Registered) to 
 * a number level. This is useful for comparing
 * permission levels to see which one is higher.
 */   
function permission_to_number($in_permission) {
	switch($in_permission)
	{
		case 'Administrator':
			return 1000;
			break;
		case 'Editor':
			return 500;
			break;
		case 'Registered':
			return 100;
		default: //Anonymous
			return 0;
	}
	
	return 0;
}


function get_page_read_roles($in_pagename) {
	global $CI;
	
	//Check to see if any custom ACL were set for the page
	$CI->load->model('page_metadata_model');
	$CI->page_metadata_model->pagename = $in_pagename;
	$page_permission = $CI->page_metadata_model->get_value('read_permission');
	if(empty($page_permission))
	{
		$page_permission = $CI->settings->get('default_read_permission');
	}
	
	return $page_permission;
}

function get_page_write_roles($in_pagename) {
	global $CI;
	
	//Check to see if any custom ACL were set for the page
	$CI->load->model('page_metadata_model');
	$CI->page_metadata_model->pagename = $in_pagename;
	$page_permission = $CI->page_metadata_model->get_value('write_permission');
	if(empty($page_permission))
	{
		$page_permission = $CI->settings->get('default_write_permission');
	}
	
	return $page_permission;
}


/**
 * Functions mainly useful for MU environment:
 */ 
$CI->template->add_function('user_wikis', 'get_user_wikis');
function get_user_wikis($in_username='') {
	$wikis = get_user_info('wikis', $in_username);
	if(!empty($wikis))
	{
		return comma_list_to_array($wikis);
	}
	
	return false;
}	

?>
