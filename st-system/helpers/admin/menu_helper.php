<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

$CI =& get_instance();

$CI->load->library('AdminMenu');

//Add menu entries here. We limit the display of some of the menu items by
//user level.

if(does_user_have_permission('Registered'))
{
	$CI->adminmenu->add_top_level('dashboard', 'Dashboard', 100);


	$CI->adminmenu->add_top_level('users', 'Users', 400);
		$CI->adminmenu->add_sub_level('users/profile', 'My Profile', 300, 'users');
	
	if(does_user_have_permission('Editor'))
	{
		$CI->adminmenu->add_top_level('pages', 'Pages', 150);
			$CI->adminmenu->add_sub_level('pages/permissions', 'Permissions', 100, 'pages');
	}
	
	if(does_user_have_permission('Administrator'))
	{
		$CI->adminmenu->add_top_level('presentation', 'Presentation', 200);
			$CI->adminmenu->add_sub_level('presentation/themes', 'Themes', 100, 'presentation');
			
		if($CI->config->item('disable_plugin_admin') !== true)
		{
		$CI->adminmenu->add_top_level('plugins', 'Plugins', 300);
		}
		
		$CI->adminmenu->add_sub_level('users/management', 'Management', 100, 'users');
		$CI->adminmenu->add_sub_level('users/addnew', 'Add New User', 200, 'users');
		
		$CI->adminmenu->add_top_level('options', 'Options', 500);
			$CI->adminmenu->add_sub_level('options/general', 'General', 100, 'options');
			$CI->adminmenu->add_sub_level('options/permissions', 'Permissions', 200, 'options');
	}
}


?>
