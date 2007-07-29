<?php
/**
 * For converting Wiki syntax into XHTML syntax.
 * 
 * @author Michael Huynh (http://www.mikexstudios.com)
 * @package suppleText
 * @version $Id:$
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * 
 */

$CI =& get_instance();

/** 
 * Prefilter things 
 * (well, these things should be moved out of this creole class)
 */ 

//str_replace in this case is faster, but oh well, we sacrifice some.
//Also, I've seen this done with (\r|\r\n), but this is slower since two
//checks are done at each step.
$CI->syntaxparser->addRule('to_unix_lineendings', '/\r\n?/', "\n", 10);

//Replace 4 consecutive spaces at the beginning of a line with tab character.
//Since the text is all one long string, we find the start of lines by the \n
//and then we count four consecutive spaces.
$CI->syntaxparser->addRule('spaces_to_tab', '/\n[ ]{4}/', "\n\t", 20); 

/**
 * Trim
 * Remove whitespace from the beginning and end of a string
 * Actually trim is pretty important since future regex depend on
 * lines ending cleanly with \n.  
 * (But below implementation could be a little slow)
 * NOTE: Trim conflicts with spaces_to_tab! 
 */ 
$CI->syntaxparser->addRule('trim_spaces', '/(.*)/m', 'trim_spaces_callback', 30, true);
function trim_spaces_callback(&$matches) {
	return trim($matches[1]);
} 

//Insert a newline at the beginning and end of the text. This will help
//in regex later since we can assume lines start and end with \n
$CI->syntaxparser->addRule('beg_end_newline', '/(.+)/s', "\n".'$1'."\n", 40);


/**
 * Snippets
 * well, they are the Wikka/Wakka "actions", but we don't have a better 
 * name for them yet.
 */
$CI->syntaxparser->addRule('snippets', '/<<<(.+)>>>/U', 'snippets_callback', 120, true); 
function snippets_callback(&$matches) {
	global $CI;
	
	$action = trim($matches[1]);

	// search for parameters separated by spaces or newlines - Wikka #371
	if (preg_match('/\s/', $action))
	{
		// parse input for action name and parameters
		preg_match('/^([A-Za-z0-9]*)\s+(.*)$/s', $action, $matches);
		// extract $action and $vars_temp ("raw" attributes)
		list(, $action, $args) = $matches;
		
		//Call action, pass the args to it
		return $CI->syntaxparser->doAction($action, $args);
		
	}
	if (!preg_match('/^[a-zA-Z0-9_]+$/', $action))
	{
		return 'Unknown action; the action name must not contain special characters.';
	}

	return $CI->syntaxparser->doAction($action);
}


/**
 * Escaping HTML
 */
$CI->syntaxparser->addRule('escape_html_1', '/</', '&lt;', 125);
$CI->syntaxparser->addRule('escape_html_2', '/>/', '&gt;', 126);


/**
 * Postfilters
 */ 
 
//Remove the last <br />. Not totally sure why we need this right now.
$CI->syntaxparser->addRule('remove_last_br', '/<br \/>$/', '', 2000);

//Unhash everything. This is absolutely necessary to reverse all of the hiding done by other functions.
$CI->syntaxparser->addRule('unhash_all', '/'.$CI->syntaxparser->getTokenPattern().'/', 'unhash_all_callback', 2010, true);
function unhash_all_callback(&$matches) {
	global $CI;
	
	return $CI->syntaxparser->unhash($matches[1]);
}

/**
 * Paragraph
 * (put <p> and </p>)
 */
//$CI->syntaxparser->addRule('paragraph', '/(.+?)\n\n/s', '<p>\1</p>'."\n\n", 300);  
//$CI->syntaxparser->addRule('paragraph', '/(.+)/s', 'wpautop', 300, true);  
//$CI->syntaxparser->addRule('paragraph', '/\n?(.+?)(?:\n\s*\n|\z)/s', "<p>$1</p>\n\n", 2020); // make paragraphs, including one at the end
$CI->syntaxparser->addRule('paragraph', '/\n?(.+?)(\n\s*\n|\z)/s', 'paragraph_callback', 2020, true);
/**
 * @author Wordpress
 * $matches[2] contains the newlines 
 */ 
function paragraph_callback(&$matches) {
	//We check to see if the block that is passed in begin or ends with any
	//of the block-level tags defined below:
	$allblocks = '(?:table|thead|tfoot|caption|colgroup|tbody|tr|td|th|div|dl|dd|dt|ul|ol|li|pre|select|form|map|area|blockquote|address|math|style|input|p|h[1-6]|hr)';
	//if(!preg_match('!^\s*</?' . $allblocks . '[^>]*>!', $matches[1]))
	//if(!preg_match('!</?' . $allblocks . '[^>]*>\s*$!', $matches[1]))
	if((!preg_match('!^\s*</?' . $allblocks . '[^>]*>!', $matches[1])) && (!preg_match('!</?' . $allblocks . '[^>]*>\s*$!', $matches[1])))
	{
		return '<p>'.$matches[1].'</p>'.$matches[2];
	}
	
	//return $matches[1]."\n\n";
	return $matches[1].$matches[2]."\n"; //Extra \n needed for non-paragraph items
}


/**
 * Paragraph Newlines
 * Convert all \n in paragraphs to <br />. 
 */  
$CI->syntaxparser->addRule('paragraph_newline', '/<p>(.*)<\/p>/Us', 'paragraph_newline_callback', 2030, true);
function paragraph_newline_callback(&$matches) {
	global $CI;
	
	//Rehash certain elements in the paragraph so that newlines are not
	//converted. Like <pre> tags
	$rehash_elements = '(?:pre)'; //Add more. Erg, we can't have li in here either. We don't need ul and ol in here now since we don't put them in paragraphs.
	$matches[1] = preg_replace_callback('/(<'.$rehash_elements.'>.+<\/'.$rehash_elements.'>)\n?/Us', 'paragraph_newline_hash_callback', $matches[1]);
	
	//We run into <br />\n since that is the result of \\\n (a line break)
	//The solution is to remove the <br /> in the <br />\n and let the next
	//preg_replace change that into a <br />.
	//We do NOT modify the 'linebreak' rule from <br />\n to just \n since
	//the \\ can be used in headers and other block-level elements.
	$matches[1] = preg_replace('/<br \/>\n/', "\n", $matches[1]);
	
	//Replace newlines
	$matches[1] = '<p>'.preg_replace('/\n/', '<br />'."\n", $matches[1]).'</p>';
	
	//Now we unhash everything
	return $CI->syntaxparser->unhash_contents($matches[1]);
} 
function paragraph_newline_hash_callback(&$matches) {
	global $CI;
	
	return $CI->syntaxparser->hash($matches[1]);
}

//When non-paragraph items are separated by more than one newline, then we
//assume that the user is intentionally inserting a newline:
//See #10: http://dev.suppletext.org/ticket/10
$CI->syntaxparser->addRule('intentional_newline', '/\n\n(\n+)\n/', 'intentional_newline_callback', 2050, true);
function intentional_newline_callback(&$matches) {
	return "\n".str_replace("\n", "<br />\n", $matches[1])."\n";
}

/**
 * XSS Attacks Filtering. Okay, we'll disable it here.
 */
/*
$CI->syntaxparser->addRule('xss_filter', '/(.+)/', 'xss_filter_callback', 2100, true);
function xss_filter_callback(&$matches) {
	global $CI;
	
	return $CI->input->xss_clean($matches[1]);
} 
*/

/* Things to implement:
    var $rules = array(
        'Prefilter',
        'Delimiter', //We don't use this.
        'Preformatted',
        'Tt',
        'Trim', //Skipped
        'Break',
        'Raw', //Skipped
        'Footnote', //Skipped
        'Table', //Skipped
        'Newline' //Skipped,
        'Blockquote', //Skipped
        'Newline', //Skipped, again?
        'Url', //Don't quite understand these.
        'Wikilink', //Don't quite understand these.
        'Image',
        'Heading',
        'Center', //Skip for now
        'Horiz',
        'List', //Skip for now
        //'Table',
        'Address', //Skip
        'Paragraph', //Skip
        'Superscript', //Skip
        'Subscript', //Skip
        'Underline', //Skip
        'Emphasis',
        'Strong',
        'Tighten' //Skip
    );
*/

?>
