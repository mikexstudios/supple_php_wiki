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

//$Supple->registerAction('theme_system_path', 'get_theme_system_path');
function get_theme_system_path($file='') {
	global $Supple;
	
	if(empty($file))
	{
		return ABSPATH.'/st-external/themes/'.$Supple->Settings->getSetting('use_theme');
	}
	return get_theme_system_path().'/'.$file;
}

//Registering as Action eliminates the need for separate out_* functions.
//$Supple->registerAction('theme_url', 'get_theme_url_path');
function get_theme_url_path($file='') {
	global $Supple;
	
	if(empty($file))
	{
		return SITE_URL.'/st-external/themes/'.$Supple->Settings->getSetting('use_theme');
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
	$CI =& get_instance();
	
	//$CI->load->view($file);
}

//$Supple->registerAction('current_url', 'get_current_url');
function get_current_url($prefix='http://', $postfix='') {
	return $prefix.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'].$postfix;
}

function redirect_page($page, $handler='') {
	redirect(construct_page_url($page, $handler));
}

//$Supple->registerAction('page_url', 'construct_page_url');
function construct_page_url($page, $handler='', $args='') {
	global $Supple;
	
	//Construct URL:
	if($Supple->Settings->getSetting('is_rewrite'))
	{
		$url = SITE_URL.'/'.$page;
	}
	else
	{
		$url = SITE_URL.'/index.php?wiki='.$page;
	}
	if(!empty($handler))
	{
		$url .= '/'.$handler;
	}
	if(!empty($args))
	{
		$url .= '&'.$args;
	}
	
	return $url;
}

function include_buffered($filename) {
		ob_start(); 
		include($filename);
		$output = ob_get_contents();
		ob_end_clean();
		return $output;
}

//$Supple->registerAction('site_name', 'get_site_name');
function get_site_name() {
	global $Supple;
	return $Supple->Settings->getSetting('site_name');
}

//$Supple->registerAction('root_page', 'get_default_page');
function get_default_page() {
	global $Supple;
	return $Supple->Settings->getSetting('root_page');
}

//$Supple->registerAction('format', 'format_text');
function format_text($in_text) {
	global $Supple;
	$Supple->SyntaxParser->setSyntaxPath(ABSPATH.'/st-system/formatters/');
	$Supple->SyntaxParser->loadSyntax();
	$Supple->SyntaxParser->setText($in_text);
	$Supple->SyntaxParser->applyAll();
	return $Supple->SyntaxParser->getText();
}


//$Supple->registerAction('execution_time', 'get_execution_time');
function get_execution_time($digits=4) {
	global $tstart;
	
	$tend = substr(microtime(),11).substr(microtime(),1,9); 
	//calculate the difference
	$totaltime = ($tend - $tstart);
	
	return sprintf('%.'.strval($digits).'f', $totaltime);

}

?>
