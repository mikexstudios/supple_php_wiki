<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * An alphanumeric string identifying the wiki. Important in MU environments.
 * Do NOT set this if you are running a one wiki installation
 */  
$config['wiki_tag'] = ''; 

/**
 * The following specifies whether or not certain admin pages and options are
 * disabled. Important in MU environments. Do NOT change this if you don't
 * know what you are doing.
 */  
$config['disable_user_admin'] = false;
$config['disable_plugin_admin'] = false;
$config['simple_admin_options'] = false;

/**
 * Specifies the theme used to display the admin interface. You can drop
 * the files in /st-system/views/admin/[admin theme name]. Then change it here.
 */  
$config['admin_theme'] = 'default';

//We override settings here!
require ABSPATH.'st-external/st-config.php';

?>
