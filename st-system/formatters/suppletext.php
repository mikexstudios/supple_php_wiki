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
	
	//Do not return anything
}  


?>
