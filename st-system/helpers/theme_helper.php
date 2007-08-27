<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

$CI =& get_instance();
$CI->load->library('template'); //Seems like template library has not been loaded yet.

/**
 * An alias for ->execute() that is used in templating
 * so that users have an easier time using "tags". Can accept
 * additional arguments which will be passed to ->execute().
 *
 * @param string $inTag Short variable-like name associated with a function such as 'getimagetag'.
 * @return mixed Returns whatever the function associated to the tag returns. Could possibly be nothing. Usually, expect a string.
 */
function get($inTag) {
	$CI =& get_instance();
	
	  if (func_num_args() > 1)
	  {
	      $args = func_get_args();
	      return call_user_func_array(array(&$CI->template, 'execute'), $args);
	  }
	
	return $CI->template->execute($inTag);
}

/**
 * Similar to get() as an alias for $CI->doAction, but prints
 * the output rather than returning it. Can accept
 * additional arguments which will be passed to ->doAction().
 * 
 * @param string $inTag Short variable-like name associated with a function such as 'getimagetag'.
 */
function out($inTag) {
	if (func_num_args() > 1)
	{
		$args = func_get_args();
		echo call_user_func_array('get', $args); 
		return;
	}
	
	echo get($inTag);

}

$CI->template->add_function('theme_system_path', 'get_theme_system_path');
function get_theme_system_path($file='') {
	global $CI;
	
	if(empty($file))
	{
		return $CI->load->_ci_view_path;
		//return ABSPATH.'/st-external/themes/'.$CI->settings->get('use_theme');
	}
	return get_theme_system_path().$file;
}

//Registering as Action eliminates the need for separate out_* functions.
$CI->template->add_function('theme_url', 'get_theme_url_path');
function get_theme_url_path($file='', $use_theme='') {
	global $CI;
	
	//If no theme is specified, we use the current theme
	if(empty($use_theme))
	{
		$use_theme = $CI->settings->get('use_theme');
	}
	
	$return_url = site_url(THEMES_DIR.$use_theme);
	
	if(!empty($file))
	{
		return $return_url.'/'.$file;
	}
	
	return $return_url;
}

/**
 * Used in theme files to include other theme files. This function provides
 * the correct paths.
 * 
 * @param string $file The file to be included.   
 */ 
function theme_include($file) {
	global $CI;
	
	$CI->load->view($file);
}

$CI->template->add_function('current_url', 'get_current_url');
function get_current_url() {
	global $CI;
	
  return site_url($CI->uri->uri_string());
	//return $prefix.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'].$postfix;
}

function redirect_page($page, $handler='', $args='') {
	redirect ($page.'/'.$handler.'/'.$args);
	//redirect(construct_page_url($page, $handler, $args));
}

$CI->template->add_function('page_url', 'construct_page_url');
function construct_page_url($page, $handler='', $args='') {
	$segments = array($page, $handler, $args);
	return site_url($segments);
}

function include_buffered($filename, $vars=array()) {
		global $CI;
		
		return $CI->load->file($filename, TRUE); //True means buffered
}

$CI->template->add_function('site_name', 'get_site_name');
function get_site_name() {
	global $CI;
	
	return $CI->settings->get('site_name');
}

$CI->template->add_function('root_page', 'get_default_page');
function get_default_page() {
	global $CI;
	
	return $CI->settings->get('root_page');
}

function base_path($in_file='') {
	if(empty($in_file))
		{ return ABSPATH; }
	
	return ABSPATH.$in_file;
}

$CI->template->add_function('this_page', 'get_current_pagename');
function get_current_pagename() {
	global $CI;
	
	//NOTE: URI automatically initialized by the system.
	return $CI->uri->segment(1);
}

$CI->template->add_function('format', 'format_text');
function format_text($in_text, $preprocess=false) {
	global $CI;
	
	$CI->load->library('syntaxparser');
	//$CI->load->helper('misc');
	$CI->syntaxparser->setSyntaxPath(base_path('/st-system/formatters/'));
	$CI->syntaxparser->loadSyntax();
	$CI->syntaxparser->setText($in_text);
	if($preprocess === true)
	{
		$CI->syntaxparser->applyAll(true);
	}
	else
	{
		$CI->syntaxparser->applyAll();
	}
	return $CI->syntaxparser->getText();
}

$CI->template->add_function('page_exists', 'does_current_page_exist');
/**
 * Assumes that this will be called from template so that page
 * information has already been set
 */  
function does_current_page_exist() {
	$temp_content = get('page_content');
	$temp_time = get('page_time');
	if(!empty($temp_content) && !empty($temp_time))
	{
		return true;
	}
	
	return false;
}

$CI->template->add_function('execution_time', 'get_execution_time');
function get_execution_time() {
	global $CI;
	
	return $CI->benchmark->elapsed_time();
}

$CI->template->add_function('database_queries', 'get_num_database_queries');
function get_num_database_queries() {
	global $CI;
	
	return $CI->db->total_queries();
}

$CI->template->add_function('form_value', 'get_form_value');
function get_form_value($in_name, $escape=true) {
	global $CI;

	if($escape === true)
	{
		$CI->load->helper('form');
		return form_prep($CI->validation->$in_name);
	}
	
	return $CI->validation->$in_name;
}

$CI->template->add_value('st_version', $CI->settings->get('version'));

/**
 * This should be used sparingly
 */ 
$CI->template->add_function('setting', 'get_setting');
function get_setting($in_key) {
	global $CI;
	
	return $CI->settings->get($in_key);
}

$CI->template->add_function('this_uri_fragment', 'get_this_uri_fragment');
function get_this_uri_fragment() {
	global $CI;
	
	return $CI->uri->uri_string();
}

//Moved user functions to user_helper.php

function load_page_metadata($in_pagename) {
	global $CI;
	
	$CI->load->model('page_metadata_model');
	$CI->page_metadata_model->pagename = $in_pagename;
	$page_metadata = $CI->page_metadata_model->get_all();
	foreach($page_metadata as $page_key => $page_value)
	{
		$CI->template->add_value($page_key, $page_value);
	}
}

$CI->template->add_function('current_revision', 'get_current_page_revision');
function get_current_page_revision() {
	global $CI;
	
	$action = $CI->uri->segment(2);
	if($action == 'show')
	{
		$revision_num = $CI->uri->segment(3);
		
		if($CI->validation->numeric($revision_num))
		{
			return $revision_num;
		}
	}
	
	return '';
}

?>
