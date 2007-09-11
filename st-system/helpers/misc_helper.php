<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * This helper file is for small functions that don't really require
 * their own helper file.
 */  

$CI =& get_instance();

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
 * thus _needs_ to know (or assume) a character set because the special
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
 * @since		Wikka 1.1.6.3
 * @version		1.0
 * @license		http://www.gnu.org/copyleft/lgpl.html
 * 				GNU Lesser General Public License
 * @copyright	Copyright 2007, {@link http://wikkawiki.org/CreditsPage
 * 				Wikka Development Team}
 *
 * @access	public
 *
 * @param	string	$string	string to be converted
 * @param	integer	$quote_style
 * 			- ENT_COMPAT:   escapes &, <, > and double quote (default)
 * 			- ENT_NOQUOTES: escapes only &, < and >
 * 			- ENT_QUOTES:   escapes &, <, >, double and single quotes
 * @return	string	converted string
 */
 function htmlspecialchars_secure($string, $quote_style=ENT_COMPAT)
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
	$result = strtr($string,$aTransSpecchar);
	return $result;
 }

/**
 * Scans the specified directory and includes all files in that directory. 
 *
 * @access private
 * @param string $inDir Directory of files to be loaded. NOTE: The directory should be input with the trailing slash.
 * @param string $ext the extention of files to be loaded (defaults to '.php')
 */
function load_files_in_directory($inDir, $ext = '.php') {
	if ($handle = opendir($inDir)) 
	{
		//Need the !== so that directories called '0' don't break the loop
		while (false !== ($file = readdir($handle)))
		{
		    if (is_dir($inDir.$file))
		    {
                  if ($file != '.' && $file != '..')
                  {
                      load_files_in_directory($inDir.$file); // Recurse subdirectories
                  }
                  continue;
        }
			if (strpos($file, $ext) !== false) // Only php files, for safety.
			{
				//echo $inDir.$file."\n";
				include_once($inDir.$file);
			}
		}
		closedir($handle); 
	}
}

function is_wiki_name($in_pagename) {
	if(!empty($in_pagename) && preg_match('/[a-zA-Z0-9%:_-]+/', $in_pagename))
	{
		return true;
	}
	
	return false;
}

function does_page_exist($in_pagename) {
	global $CI;
	
	//$CI->load->model('pages_model', 'pages_model_pageexist');
	//$CI->pages_model_pageexist->pagename = $in_pagename;
	//$CI->pages_model_pageexist->loadPage();
	
	//This is quicker...
	$CI->db->select('id');
	$CI->db->from(ST_PAGES_TABLE);
	$CI->db->where('tag', $in_pagename);
	$CI->db->limit(1);
	$query = $CI->db->get();
	$id = element('id', $query->row_array());

	if(!empty($id))
	{
		return true;
	}
	
	return false;
}

function comma_list_to_array($in_comma_list) {
	if(empty($in_comma_list))
	{
		return array();
	}

	$array_list = explode(',', $in_comma_list);
	foreach($array_list as $key => $each_element)
	{
		$array_list[$key] = trim($each_element);
	}
	
	return $array_list;
}

function array_to_comma_list($in_array) {
	return implode(',', $in_array);
}

function add_to_comma_list($in_comma_list, $in_add_element) {
	$list = comma_list_to_array($in_comma_list);
	$list[] = trim($in_add_element);
	
	return array_to_comma_list($list);
}

function delete_from_comma_list($in_comma_list, $in_value) {
	$list = comma_list_to_array($in_comma_list);
	if(in_array($in_value, $list))
	{
		unset($list[array_search($in_value, $list)]);
	}
	
	return array_to_comma_list($list);
}

?>
