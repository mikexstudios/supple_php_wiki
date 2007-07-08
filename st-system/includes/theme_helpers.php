<?php

/**
 * Wrapper around hsc_secure() which preserves entity references.
 *
 * The first two parameters for this function as the same as those for 
 * htmlspecialchars() in PHP: the text to be treated, and an optional
 * parameter determining how to handle quotes; both these parameters are 
 * passed on to our hsc_secure() replacement for htmlspecialchars().
 * 
 * Since hsc_secure() does not need a character set parameter, we don't
 * have that here any more either.
 * 
 * A third 'doctype' parameter is for local use only and determines how 
 * pre-existing entity references are treated after hsc_secure() has done 
 * its work: numeic entity references are always "unescaped' since they are
 * valid for both HTML and XML doctypes; for XML the named entity references
 * for the special characters are unescaped as well, while for for HTML any
 * named entity reference is unescaped. This parameter is optional and 
 * defaults to HTML.   
 *
 * The function first applies hsc_secure() to the input string and then 
 * "unescapes" character entity references and numeric character references 
 * (both decimal and hexadecimal).
 * Entities are recognized also if the ending semicolon is omitted at the 
 * end or before a newline or tag but for consistency the semicolon is 
 * always added in the output where it was omitted.
 *
 * Usage note:
 * Where code should be rendered <em>as code</em> hsc_secure() should be 
 * used directly so that entity references are also rendered as such instead 
 * of as their corresponding characters.
 * 
 * Documentation note:
 * It seems the $doctype parameter was added in 1.1.6.2; version should have 
 * been bumped up to 1.1, and the param documented. We'll assume the updated
 * version was indeed 1.1, and put this one using hsc_secure() at 1.2 (at 
 * the same time updating the 'XML' doctype with apos as named entity).
 *
 * @access	public
 * @since	Wikka 1.1.6.0
 * @version	1.2
 *
 * @uses	Wakka::hsc_secure()
 * @param	string	$text required: text to be converted
 * @param	integer	$quote_style optional: quoting style - can be ENT_COMPAT 
 * 			(default, escape only double quotes), ENT_QUOTES (escape both 
 * 			double and single quotes) or ENT_NOQUOTES (don't escape any 
 * 			quotes)
 * @param	string $doctype 'HTML' (default) or 'XML'; for XML only the XML
 * 			standard entities are unescaped so we'll have valid XML content
 * @return	string	converted string with escaped special characted but 
 * 			entity references intact
 * 
 * @todo	(maybe) recognize valid html entities and only leave those 
 * 			alone, thus transform &error; to &amp;error;
 * @todo	(later - maybe) support full range of situations where (in SGML) 
 * 			a terminating ; may legally be omitted (end, newline and tag are 
 * 			merely the most common ones); such usage is quite rare though 
 * 			and may not be worth the effort
 */
function htmlspecialchars_ent($text,$quote_style=ENT_COMPAT,$doctype='HTML')
{
	// re-establish default if overwritten because of third parameter
	// [ENT_COMPAT] => 2
    // [ENT_QUOTES] => 3
    // [ENT_NOQUOTES] => 0
	if (!in_array($quote_style,array(ENT_COMPAT,ENT_QUOTES,ENT_NOQUOTES))) {
		$quote_style = ENT_COMPAT;	
	}
	
	// define patterns
	$terminator = ';|(?=($|[\n<]|&lt;))';	// semicolon; or end-of-string, newline or tag
	$numdec = '#[0-9]+';					// numeric character reference (decimal)
	$numhex = '#x[0-9a-f]+';				// numeric character reference (hexadecimal)
	if ($doctype == 'XML')					// pure XML allows only named entities for special chars
	{
		// only valid named entities in XML (case-sensitive)
		$named = 'lt|gt|quot|apos|amp';			
		$ignore_case = '';
		$entitystring = $named.'|'.$numdec.'|'.$numhex;
	}
	else									// (X)HTML
	{
		$alpha  = '[a-z]+';					// character entity reference TODO $named='eacute|egrave|ccirc|...'
		$ignore_case = 'i';					// names can consist of upper and lower case letters
		$entitystring = $alpha.'|'.$numdec.'|'.$numhex;
	}
	$escaped_entity = '&amp;('.$entitystring.')('.$terminator.')';

	// execute our replacement hsc_secure() function, passing on optional parameters
	$output = hsc_secure($text,$quote_style);

	// "repair" escaped entities
	// modifiers: s = across lines, i = case-insensitive
	$output = preg_replace('/'.$escaped_entity.'/s'.$ignore_case,"&$1;",$output);

	// return output
	return $output;
}

/**
 * Secure replacement for PHP built-in function htmlspecialchars().
 * 
 * See ticket #427 (http://wush.net/trac/wikka/ticket/427) for the rationale 
 * for this replacement function.
 * 
 * The INTERFACE for this function is almost the same as that for
 * htmlspecialchars(), with the same default for quote style; however, there
 * is no 'charset' parameter. The reason for this is as follows:
 * 
 * The PHP docs say:
 * 	"The third argument charset defines character set used in conversion."
 * 
 * I suspect PHP's htmlspecialchars() is working at the byte-value level and
 * thus _needs_ to know (or asssume) a character set because the special 
 * characters to be replaced could exist at different code points in
 * different character sets. (If indeed htmlspecialchars() works at 
 * byte-value level that goes some  way towards explaining why the 
 * vulnerability would exist in this function, too, and not only in 
 * htmlentities() which certainly is working at byte-value level.)
 * 
 * This replacement function however works at character level and should
 * therefore be "immune" to character set differences - so no charset 
 * parameter is needed or provided. If a third parameter is passed, it will
 * be silently ignored.
 * 
 * In the OUTPUT there is a minor difference in that we use '&#39;' instead
 * of PHP's '&#039;' for a single quote: this provides compatibility with
 * 	get_html_translation_table(HTML_SPECIALCHARS, ENT_QUOTES)
 * (see comment by mikiwoz at yahoo dot co dot uk on 
 * http://php.net/htmlspecialchars); it also matches the entity definition 
 * for XML 1.0 
 * (http://www.w3.org/TR/xhtml1/dtds.html#a_dtd_Special_characters). 
 * Like PHP we use a numeric character reference instead of '&apos;' for the 
 * single quote. For the other special characters we use the named entity 
 * references, as PHP is doing.
 * 
 * And finally:
 * The name for this function was basically inspired by waawaamilk (GeSHi), 
 * kindly provided by BenBE (GeSHi), happily acknowledged by WikkaWiki Dev 
 * Team and finally used by JavaWoman. :)
 * 
 * @author 		{@link http://wikkawiki.org/JavaWoman Marjolein Katsma}
 *
 * @since		Wikka 1.1.6.3
 * @version		1.0
 * @license		http://www.gnu.org/copyleft/lgpl.html 
 * 				GNU Lesser General Public License
 * @copyright	Copyright 2007, {@link http://wikkawiki.org/CreditsPage 
 * 				Wikka Development Team}
 * 
 * @access	public
 * @param	string	$string	string to be converted
 * @param	integer	$quote_style 
 * 			- ENT_COMPAT:   escapes &, <, > and double quote (default)
 * 			- ENT_NOQUOTES: escapes only &, < and >
 * 			- ENT_QUOTES:   escapes &, <, >, double and single quotes
 * @return	string	converted string   
 */
 function hsc_secure($string, $quote_style=ENT_COMPAT)
 {
 	// init
 	$aTransSpecchar = array('&' => '&amp;',
 							'"' => '&quot;',
 							'<' => '&lt;',
							'>' => '&gt;'
							);			// ENT_COMPAT set
	if (ENT_NOQUOTES == $quote_style)	// don't convert double quotes
	{
		unset($aTransSpecchar['"']);
	}
	elseif (ENT_QUOTES == $quote_style)	// convert single quotes as well
	{
		$aTransSpecchar["'"] = '&#39;';	// (apos) htmlspecialchars() uses '&#039;'
	}

	// return translated string
	return strtr($string,$aTransSpecchar);
 }

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
function construct_page_url($page, $handler='', $args='') {
	//Construct URL:
	$url = SITEURL.'/index.php?wiki='.$page;
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

$Supple->registerAction('site_name', 'get_site_name');
function get_site_name() {
	global $Supple;
	return $Supple->Settings->getSetting('site_name');
}

?>