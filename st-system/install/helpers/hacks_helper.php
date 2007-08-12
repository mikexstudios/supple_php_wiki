<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

$CI =& get_instance();

//Set pages to output unicode
$CI->output->set_header("Content-Type: text/html; charset=UTF-8");

//Load constants (includes database tables)
include_once ABSPATH.'st-system/config/constants.php';

?>
