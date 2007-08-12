<?php
/**
 * Load DB Helper
 * Helps connect to the database.
 */

$CI =& get_instance(); 

function load_db() {
	global $CI;
	
	$include_check = include_once ABSPATH.'st-external/st-config.temp.php';
	if($include_check === FALSE)
	{
		error_no_step1();
	}
	
	//Augment imported db info
	$db['suppletext']['pconnect'] = FALSE;
	$db['suppletext']['db_debug'] = FALSE; //FALSE - We use our own custom error messages
	$db['suppletext']['active_r'] = TRUE;
	$db['suppletext']['cache_on'] = FALSE;
	$db['suppletext']['cachedir'] = "";
	
	//Okay, we assume database can be loaded successfully
	$CI->load->database($db['suppletext']);
	
	return $db['suppletext']; //Return database info

}  


?>
