<?php
/**
 * Optional wiki markup for Creole.
 * See: http://www.wikicreole.org/wiki/CreoleAdditions
 * Unimplemented: Alternate Link Syntax 
 */

/**
 * Monospace
 */
 
//$CI->syntaxparser->add_inline_definition('monospace', '/##(.*?)##/', 'monospace_callback', 101, true);
function monospace_callback(&$matches) {
	global $CI;
	
	//Should convert to HTML entities any special characters:
	$matches[1] = htmlentities($matches[1], ENT_QUOTES, 'UTF-8');
	
	return $CI->syntaxparser->inline_hash('<tt>'.$matches[1].'</tt>');
}   

/**
 * Underline
 */
$CI->syntaxparser->add_inline_definition('underline', '/__(.*?)__/', '<u>$1</u>', 480); 


/**
 * Superscript
 */
$CI->syntaxparser->add_inline_definition('superscript', '/\^\^(.*?)\^\^/', '<sup>$1</sup>', 500); 

/**
 * Subscript
 */
$CI->syntaxparser->add_inline_definition('superscript', '/,,(.*?),,/', '<sub>$1</sub>', 510); 

//TODO:
//Indented paragraphs
//Definition lists

?>
