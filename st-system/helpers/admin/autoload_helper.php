<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

$CI =& get_instance();

//A makeshift autoload for admin

$CI->load->helper(array('admin/theme', 'admin/menu', 'admin/security'));
$CI->load->library('message');

//We want to override the views location (for our admin template files)
$CI->load->_ci_view_path = ABSPATH.'st-system/views/admin/'.$CI->config->item('admin_theme').'/'; //Need trailing slash

?>
