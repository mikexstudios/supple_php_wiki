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
function get_theme_url_path($file='') {
	global $CI;
	
	if(empty($file))
	{
		return site_url(THEMES_DIR.$CI->settings->get('use_theme'));
	}
	return get_theme_url_path().'/'.$file;
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
function get_current_url($prefix='http://', $postfix='') {
	return $prefix.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'].$postfix;
}

function redirect_page($page, $handler='', $args='') {
	redirect(construct_page_url($page, $handler, $args));
}

$CI->template->add_function('page_url', 'construct_page_url');
function construct_page_url($page, $handler='', $args='') {
	$segments = array($page, $handler, $args);
	return site_url($segments);
}

function include_buffered($filename, $vars=array()) {
		global $CI;
		
		return $CI->load->view($filename, $vars, TRUE); //True means buffered
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

$CI->template->add_function('format', 'format_text');
function format_text($in_text) {
	global $CI;
	$CI->SyntaxParser->setSyntaxPath(ABSPATH.'/st-system/formatters/');
	$CI->SyntaxParser->loadSyntax();
	$CI->SyntaxParser->setText($in_text);
	$CI->SyntaxParser->applyAll();
	return $CI->SyntaxParser->getText();
}


$CI->template->add_function('execution_time', 'get_execution_time');
function get_execution_time() {
	global $CI;
	
	return $CI->benchmark->elapsed_time();
}

?>