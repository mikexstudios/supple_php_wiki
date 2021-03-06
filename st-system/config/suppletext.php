<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Default message delimiters. Used in admin control panel.
 */
/*
$config['default_message_delimiter_pre'] = '<div id="message" class="updated fade"><p>';
$config['default_message_delimiter_post'] = '</p></div>';
*/

/**
 * Are we in a Multi-User/Wiki (MU) Environment?
 */
$config['is_mu'] = false; 

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
