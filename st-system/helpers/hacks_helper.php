<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

$CI =& get_instance();

//Set pages to output unicode
$CI->output->set_header("Content-Type: text/html; charset=UTF-8");

//For making database output unicode
$CI->load->database(); //Helpers are called before libraries
$CI->db->query("SET NAMES 'utf8'");  //We make SQL output unicode

//For changing the views path. Good thing this class var wasn't
//*really* made private :). But this has a potential to break
//in the future when everything is moved over to PHP 5.
$CI->load->library('settings');
$CI->load->_ci_view_path = ABSPATH.THEMES_DIR.$CI->settings->get('use_theme').'/'; //Need trailing slash

?>
