<?php

$CI =& get_instance();

/**
 * Preprocessors
 */
 
//str_replace in this case is faster, but oh well, we sacrifice some.
//Also, I've seen this done with (\r|\r\n), but this is slower since two
//checks are done at each step.
$CI->syntaxparser->add_preprocessor_definition('to_unix_lineendings', '/\r\n?/', "\n", 30, false); 

/**
 * Trim
 * Remove whitespace from the beginning and end of a string
 * Actually trim is pretty important since future regex depend on
 * lines ending cleanly with \n.  
 * (But below implementation could be a little slow)
 * NOTE: Trim conflicts with spaces_to_tab! 
 */ 
//$CI->syntaxparser->add_preprocessor_definition('trim_spaces', '/(.*)/m', 'trim_spaces_callback', 30, true);
function trim_spaces_callback(&$matches) {
	return trim($matches[1]);
}  
 
$CI->syntaxparser->add_preprocessor_definition('signature', '/~~~~(\s+)/', 'signature_callback', 100, true);
function signature_callback(&$matches) {
	global $CI;
	
	//the format is like: --mikexstudios 16:44, 20 August 2007 (UTC)
	//Set author
	if($CI->authorization->is_logged_in())
	{
		$author = $CI->session->userdata('username');
	}
	else
	{
		$author = $CI->input->ip_address();
	}
	
	//We are using wikipedia style time
	$date_time = mdate('%H:%i, %d %F %Y (UTC)', now());
	
	return $author.' '.$date_time.$matches[1];
	
} 

$CI->syntaxparser->add_preprocessor_definition('html', '/<html>(.*?)<\/html>/s', 'preprocess_html_callback', 200, true);
function preprocess_html_callback(&$matches) {
	global $CI;

	$CI->load->library('HTMLPurifier');
	$config = HTMLPurifier_Config::createDefault();
	$config->set('Core', 'DefinitionCache', null); //Disable caching for now
	$config->set('Core', 'AcceptFullDocuments', false);
	$config->set('HTML', 'TidyLevel', 'none');
	$config->set('Output', 'Newline', "\n");
	$clean_html = $CI->htmlpurifier->purify($matches[1], $config);
	
	return '<html>'.$clean_html.'</html>';
}

/**
 * Prefilters
 */ 

//Insert a newline at the beginning and end of the text. This will help
//in regex later since we can assume lines start and end with \n
$CI->syntaxparser->add_block_definition('beg_end_newline', '/(.+)/s', "\n".'$1'."\n", 40, false);

/**
 * HTML
 */
$CI->syntaxparser->add_block_definition('html', '/(\n?)<html>(.*?)<\/html>(\n?)/s', 'html_callback', 42, true);
function html_callback(&$matches) {
	global $CI;
	
	$matches[2] = trim($matches[2]); //Remove any possible newlines after <html> and before </html>
	
	//We assume if there are newlines before and after, that the user wants
	//the HTML to be a block level element (un-paragraphed)
	if($matches[1] == "\n" && $matches[3] == "\n")
	{
		return $matches[1].$CI->syntaxparser->block_hash($matches[2]).$matches[3]; 
	}
	
	//We can assume that the HTML is safe since when we submitted our edit, it has
	//been cleaned by HTMLPurifier.
	return $CI->syntaxparser->inline_hash($matches[2]); 
} 

/**
 * Comments
 * Not displayed. Not parsed
 */  
$CI->syntaxparser->add_block_definition('comments', '/\n?<!--.*?-->\n?/s', '', 47, false);

/**
 * Inline Metadata (not stored in database table)
 * Undisplayed items associated with a page. Think of these as "quick page settings"
 */
$CI->syntaxparser->add_block_definition('inline_metadata', '/@@([a-zA-Z0-9_-]+)\s*=\s*(.+)@@\s*/m', 'block_metadata_callback', 50, true);
function block_metadata_callback(&$matches) {
	global $CI;
	//die($matches[0]);
	$matches[1] = $CI->input->xss_clean($matches[1]);
	$matches[1] = htmlentities($matches[1], ENT_QUOTES);
	$matches[2] = $CI->input->xss_clean($matches[2]);
	$matches[2] = htmlentities($matches[2], ENT_QUOTES);
	
	$CI->template->add_value($matches[1], $matches[2]);
	
	//return $matches[0]; //Return it without any modification
	return '';
}  

/**
 * Indent
 * Replace spaces at the beginning of a block with a surrounding <div> with class="indent"
 */  
$CI->syntaxparser->add_block_definition('indent', '/^[ ]{4}(.*)$/m', 'indent_callback', 60); 
function indent_callback(&$matches) {
	global $CI;
	
	$return_html = "\n".$CI->syntaxparser->block_hash('<div class="indent">'."\n");
	$return_html .= $matches[1]."\n\n";
	$return_html .= $CI->syntaxparser->block_hash('</div>'."\n"); 
	
	return $return_html;
}

/**
 * Snippets
 * well, they are the Wikka/Wakka "actions", but we don't have a better 
 * name for them yet.
 */
$CI->syntaxparser->add_block_definition('snippets', '/<<(.+?)>>/', 'snippets_callback', 75); 
function snippets_callback(&$matches) {
	global $CI;
	
	$action = trim($matches[1]);
	
	if (!preg_match('/^[ a-zA-Z0-9_:]+$/', $action))
	{
		//return 'Unknown action; the action name must not contain special characters.';
		return '<<'.$action.'>>';
	}
	
	$action = htmlentities($action, ENT_QUOTES);

	// search for parameters separated by spaces or newlines - Wikka #371
	if (preg_match('/\s/', $action))
	{
		// parse input for action name and parameters
		if(preg_match('/^([A-Za-z0-9]*)\s+(.*)$/s', $action, $matches))
		{
			// extract $action and $vars_temp ("raw" attributes)
			list(, $action, $args) = $matches;
			
			//Call action, pass the args to it
			$output = $CI->syntaxparser->doAction($action, $args);
			if($output !== FALSE)
			{
				return $CI->syntaxparser->block_hash($output);
			}
			
			return '**Unknown action: '.$action.'**';
		}
	}
	
	$output = $CI->syntaxparser->doAction($action);
	if($output !== FALSE)
	{
		return $CI->syntaxparser->block_hash($output);
	}
	
	return '**Unknown action: '.$action.'**';
}

//When non-paragraph items are separated by more than one newline, then we
//assume that the user is intentionally inserting a newline:
//See #10: http://dev.suppletext.org/ticket/10
$CI->syntaxparser->add_block_definition('intentional_newline', '/\n(\n+)\n/', 'intentional_newline_callback', 80, true);
function intentional_newline_callback(&$matches) {
	global $CI;
	
	$num_of_br = strlen($matches[1]);
	$br_html = '';
	for($i=0;$i<$num_of_br;$i++)
	{
		$br_html .= "<br />\n";
	}
	
	return "\n\n".$CI->syntaxparser->block_hash($br_html)."\n";
}

/**
 * Div
 * (Currently, only the class attribute is supported) 
 */
$CI->syntaxparser->add_block_definition('div', '/<div(?: class\s*=\s*"(\S+)")?'.'>(.*?)<\/div>/s', 'div_callback', 80, true); 
function div_callback(&$matches) {
	global $CI;
	
	//Validate the class name
	$matches[1] = trim($matches[1]);
	if(preg_match('/[A-Za-z0-9-_ ]+/', $matches[1]))
	{
		$matches[1] = $CI->input->xss_clean($matches[1]); //Try to catch js injection
		$pre = $CI->syntaxparser->block_hash('<div class="'.$matches[1].'">');
	}
	else
	{
		$pre = $CI->syntaxparser->block_hash('<div>');
	}
	
	$post = $CI->syntaxparser->block_hash('</div>');
	
	return $pre.$matches[2].$post;
}


/**
 * Inline
 */ 

//Replace 4 consecutive spaces at the beginning of a line with tab character.
//Since the text is all one long string, we find the start of lines by the \n
//and then we count four consecutive spaces.
$CI->syntaxparser->add_inline_definition('spaces', '/([ ]{2,})/', 'spaces_callback', 55, true); //Needs to come after inline escape
function spaces_callback(&$matches) {
	$length = strlen($matches[1]);
	
	return ' '.str_repeat('&nbsp;', $length-1);
}

/**
 * Escaping HTML
 */
$CI->syntaxparser->add_inline_definition('escape_html_1', '/</', '&lt;', 102);
$CI->syntaxparser->add_inline_definition('escape_html_2', '/>/', '&gt;', 103);

/**
 * Newlines
 * Convert all \n in to <br />. 
 */  
$CI->syntaxparser->add_inline_definition('newline', '/\n/', " <br />\n", 110); //Note the space before the <br />

/**
 * Highlighting
 * (This should be processed before other inline markup elements) 
 */
$CI->syntaxparser->add_inline_definition('highlight', '/!!(.*?)!!/', '<span class="highlight">$1</span>', 300); 

//Unhash everything. This is absolutely necessary to reverse all of the hiding done by other functions.
$CI->syntaxparser->add_inline_definition('unhash_all', '/('.$CI->syntaxparser->getTokenPattern().')/', 'unhash_all_callback', 2000, true);
function unhash_all_callback(&$matches) {
	global $CI;
	
	$matches[1] = trim($matches[1], $CI->syntaxparser->block_delimiter);
	return $CI->syntaxparser->unhash($matches[1]);
}

?>
