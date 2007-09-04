<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * An alphanumeric string identifying the wiki. Important in MU environments.
 * Do NOT set this if you are running a one wiki installation
 */  
$config['wiki_tag'] = ''; 

$config['admin_theme'] = 'default';

//We override settings here!
require ABSPATH.'st-external/st-config.php';

?>
