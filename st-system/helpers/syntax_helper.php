<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * For helper functions related to syntax.
 */  

/**
 * Takes a title and replaces special characters
 * so that the title can be used in links.
 */   
function wiki_url_title($in_title) {
	$in_title = str_replace(' ', '_', $in_title);
	
	return $in_title;
}



?>
