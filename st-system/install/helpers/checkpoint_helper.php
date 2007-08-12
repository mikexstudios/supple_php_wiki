<?php
/**
 * Checkpoint helper
 * Includes functions which checks the installation at each step to make sure
 * previous steps have been completed.
 */

$CI =& get_instance();

function check_installed() {
	//We check if st-config.php file exists
	if(file_exists(ABSPATH.'st-external/st-config.php')) 
	{
		error_already_installed();
	}		
}

function check_config_writable() {
	if(!is_writable(ABSPATH.'st-external/'))
	{
		error_directory_writable();
	}
}

function check_step1_completed() {
	global $CI;
	
	$CI->load->library('session');
	
	if($CI->session->userdata('step1_completed') === TRUE) 
	{
		//return true; //Let the next return handle success for both.
	}
	else
	{
		error_no_step('1');
	}
	
	if(file_exists(ABSPATH.'st-external/st-config.temp.php'))
	{
		return true;
	}
	else
	{
		error_no_config_temp();
	}
	
	return false;
}

function check_step2_completed() {
	global $CI;
	
	$CI->load->library('session');
	
	if($CI->session->userdata('step2_completed') === TRUE) 
	{
		return true;
	}
	
	error_no_step('2');
	return false;
}   

function check_step3_completed() {
	global $CI;
	
	$CI->load->library('session');
	
	if($CI->session->userdata('step3_completed') === TRUE) 
	{
		return true;
	}
	
	error_no_step('3');
	return false;
}   


?>
