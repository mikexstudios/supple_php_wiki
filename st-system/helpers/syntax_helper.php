<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * For helper functions related to syntax.
 */  

global $CI;

/**
 * Takes a title and replaces special characters
 * so that the title can be used in links.
 */   
function wiki_url_title($in_title) {
	$in_title = str_replace(' ', '_', $in_title);
	
	return $in_title;
}

/**
 * Page vars is used in suppleText syntax to add temporary metadata to
 * a page.
 */  
$page_vars = array();
function add_page_var($in_tag, $in_value) {	
	global $page_vars;
	
	$page_vars[$in_tag] = $in_value;
}

$CI->template->add_function('page_var', 'get_page_var');
function get_page_var($in_tag) {
	global $page_vars;
	
	return $page_vars[$in_tag];
}

?>
