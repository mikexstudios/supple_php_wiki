<?php

$CI =& get_instance();

/**
 * Comments
 * Not displayed. Not parsed
 */  
$CI->syntaxparser->add_block_definition('comments', '/\n?<comment>.*?<\/comment>\n?/s', '', 47, false);

/**
 * Metadata
 * Undisplayed items associated with a page. Think of these as "page settings"
 */
$CI->syntaxparser->add_block_definition('metadata', '/\n@@([a-zA-Z0-9_-]+)\s*=\s*(.+)@@\s*\n/', 'metadata_callback', 50, true);
function metadata_callback(&$matches) {
	global $CI;
	
	$matches[1] = $CI->input->xss_clean($matches[1]);
	$matches[1] = htmlentities($matches[1], ENT_QUOTES);
	$matches[2] = $CI->input->xss_clean($matches[2]);
	$matches[2] = htmlentities($matches[2], ENT_QUOTES);
	
	$CI->template->add_value($matches[1], $matches[2]);
	
	return "\n";
}  

/**
 * Indent
 * Replace spaces at the beginning of a block with a surrounding <div> with class="indent"
 */  
$CI->syntaxparser->add_block_definition('indent', '/^[ ]{4}(.*)$/m', 'indent_callback', 60); 
function indent_callback(&$matches) {
	global $CI;
	
	$return_html = "\n".$CI->syntaxparser->hash('<div class="indent">'."\n");
	$return_html .= $matches[1]."\n\n";
	$return_html .= $CI->syntaxparser->hash('</div>'."\n"); 
	
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
				return $output;
			}
			
			return '**Unknown action: '.$action.'**';
		}
	}
	
	$output = $CI->syntaxparser->doAction($action);
	if($output !== FALSE)
	{
		return $output;
	}
	
	return '**Unknown action: '.$action.'**';
}

//Replace 4 consecutive spaces at the beginning of a line with tab character.
//Since the text is all one long string, we find the start of lines by the \n
//and then we count four consecutive spaces.
$CI->syntaxparser->add_inline_definition('spaces', '/([ ]{2,})/', 'spaces_callback', 20, true);
function spaces_callback(&$matches) {
	$length = strlen($matches[1]);
	
	return ' '.str_repeat('&nbsp;', $length-1);
}

?>
