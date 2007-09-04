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

$CI->template->add_function('have_admin_access', 'does_current_user_have_admin_access');
function does_current_user_have_admin_access() {
	global $CI;
	
	return $CI->authorization->is_logged_in();
}

/**
 * Function mainly useful for MU environment
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

$CI->template->add_function('user_info', 'get_user_info');
function get_user_info($in_key, $in_username='') {
	global $CI;
	
	$CI->load->model('users_model', 'users_model_theme');
	if(empty($in_username))
	{
		$in_username = get_logged_in_username();
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

function get_page_metadata_access_roles() {
	global $CI;
	
	$comma_list = $CI->settings->get('page_metadata_access');
	return comma_list_to_array($comma_list);
}

function does_user_have_permission($user_role, $list_of_valid_permissions) {
	
	//Admin can access anything, so we augment the permission list with 
	//'Administrator'
	$list_of_valid_permissions[] = 'Administrator';
	
	foreach($list_of_valid_permissions as $each_role)
	{
		if($user_role == $each_role)
		{
			return true;
		}
	}
	
	return false;
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
	
	return comma_list_to_array($page_permission);
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
	
	return comma_list_to_array($page_permission);
}

?>
