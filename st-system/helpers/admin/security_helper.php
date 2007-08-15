<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Admin security functions
 */ 

$CI =& get_instance();

function is_refer_from_this_page() {
	global $CI;

	$CI->load->library('user_agent');
	if($CI->agent->is_referral() && preg_match('%^'.preg_quote(construct_admin_url(get_current_admin_pagename())).'.*$%', $CI->agent->referrer()))
	{
		return true;
	}
	
	return false;
}

?>
