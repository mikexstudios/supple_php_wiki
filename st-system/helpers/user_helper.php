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
	$CI->users_model_theme->username = $in_username;
	return $CI->users_model_theme->get_value($in_key);
}

$CI->template->add_function('user_role', 'get_user_role');
function get_user_role($in_username='') {
	return get_user_info('role', $in_username);
} 

function get_page_metadata_access_roles() {
	global $CI;
	
	$comma_list = $CI->settings->get('page_metadata_access');
	$array_list = explode(',', $comma_list);
	foreach($array_list as $key => $each_element)
	{
		$array_list[$key] = trim($each_element);
	}
	
	return $array_list;
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

?>
