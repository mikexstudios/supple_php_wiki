<?php

/**
 * An alias for $Supple->doAction() that is used in templating
 * so that users have an easier time using "tags". Can accept
 * additional arguments which will be passed to ->doAction().
 *
 * @param string $inTag Short variable-like name associated with a function such as 'getimagetag'.
 * @return mixed Returns whatever the function associated to the tag returns. Could possibly be nothing. Usually, expect a string.
 */
function get($inTag) {
	global $Supple;
	
	  if (func_num_args() > 1)
	  {
	      $args = func_get_args();
	      return call_user_func_array(array(&$Supple, 'doAction'), $args);
	  }
	
	return $Supple->doAction($inTag);
}

/**
 * Similar to get() as an alias for $Supple->doAction, but prints
 * the output rather than returning it. Can accept
 * additional arguments which will be passed to ->doAction().
 * 
 * @param string $inTag Short variable-like name associated with a function such as 'getimagetag'.
 */
function out($inTag) {
	global $Supple;
	
	if (func_num_args() > 1)
	{
	    $args = func_get_args();
	    echo call_user_func_array(array(&$Supple, 'doAction'), $args);
	    return;
	}
	
	echo $Supple->doAction($inTag);
}

$Supple->registerAction('theme_system_path', 'get_theme_system_path');
function get_theme_system_path($file='') {
	if(empty($file))
	{
		return ABSPATH.'/st-external/themes/default';
	}
	return get_theme_system_path().'/'.$file;
}

//Registering as Action eliminates the need for separate out_* functions.
$Supple->registerAction('theme_url', 'get_theme_url_path');
function get_theme_url_path($file='') {
	if(empty($file))
	{
		return SITEURL.'/st-external/themes/default';
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
	//global $themePath;
	
	include get_theme_system_path($file);
}

$Supple->registerAction('current_url', 'get_current_url');
function get_current_url($prefix='http://', $postfix='') {
	return $prefix.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'].$postfix;
}

/**
 * Perform a redirection to another page.
 *
 * On IIS server, and if the page has sent any cookies, the redirection must not be performed
 * by using the 'Location:' header. We use meta http-equiv OR javascript OR link (Credits MarceloArmonas).
 * @author {@link http://wikkawiki.org/DotMG Mahefa Randimbisoa} (added IIS support)
 * @access	public
 * @since	Wikka 1.1.6.2
 *
 * @param	string	$url optional: destination URL; if not specified redirect to the same page.
 */
function redirect($url) {
	if ((eregi('IIS', $_SERVER['SERVER_SOFTWARE'])) && ($this->cookies_sent))
	{
		$redirlink = '<a href="'.$url.'">'.'this link'.'</a>';
		die('<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
				<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en"><head><title>'.sprintf('Redirected to %s',$this->Href($url)).'</title>'.
				'<meta http-equiv="refresh" content="0; url=\''.$url.'\'" /></head><body><div><script type="text/javascript">window.location.href="'.$url.'";</script>'.
				'</div><noscript>'.sprintf('If your browser does not redirect you, please follow %s',$redirlink).'</noscript></body></html>');
	}
	else
	{
		session_write_close(); # Always use session_write_close() before any header('Location: ...')
		header('Location: '.$url);
	}
	exit;
}

function redirect_page($page, $handler='') {
	redirect(construct_page_url($page, $handler));
}

$Supple->registerAction('page_url', 'construct_page_url');
function construct_page_url($page, $handler='') {
	//Construct URL:
	$url = SITEURL.'/index.php?wiki='.$page;
	if(!empty($handler))
	{
		$url .= '/'.$handler;
	}
	
	return $url;
}

?>