<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

$CI =& get_instance();

$CI->load->library('AdminMenu');

//Add menu entries here
$CI->adminmenu->add_top_level('dashboard', 'Dashboard', 100);
$CI->adminmenu->add_top_level('presentation', 'Presentation', 200);
$CI->adminmenu->add_top_level('plugins', 'Plugins', 300);
$CI->adminmenu->add_top_level('users', 'Users', 400);
$CI->adminmenu->add_top_level('options', 'Options', 500);
	$CI->adminmenu->add_sub_level('options/general', 'General', 100, 'options');


?>
