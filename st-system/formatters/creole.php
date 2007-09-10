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

//Insert a newline at the beginning and end of the text. This will help
//in regex later since we can assume lines start and end with \n
$CI->syntaxparser->add_block_definition('beg_end_newline', '/(.+)/s', "\n".'$1'."\n", 40, false);

//Escape character here. We parse the escape character only if it is at the start
//of some word (so we have a whitespace char in front).
$CI->syntaxparser->add_block_definition('escape', '/(\s|^)~(.)/', 'escape_callback', 45, true);
function escape_callback(&$matches) {
	global $CI;
	
	//Protect against some XSS (When user tries to escape every character in XSS
	//in hopes that after unhashing the malicious code is assembled again).
	$matches[2] = htmlentities($matches[2], ENT_COMPAT, 'UTF-8');
	
	//If \s is a space, we remove it
	if(strcmp($matches[1], ' ')==0)
	{
		return $CI->syntaxparser->inline_hash($matches[2]);
	}
	
	return $matches[1].$CI->syntaxparser->inline_hash($matches[2]);
}

//We want to specify blocks first.

$CI->syntaxparser->add_block_definition('preformatted', '/\n{{{\n(.*)\n}}}\n/Us', 'preformatted_callback', 70, true);
function preformatted_callback(&$matches) {
	global $CI;

	

	//Currently taken from Preformatted.php from Creole of Pear::Text_wiki
	//@author Tomaiuolo Michele <tomamic@yahoo.it>
	
	// any sequence of closing curly braces separated
	// by some spaces, will have one space removed
	$find = "/} ( *)(?=})/";
	$replace = "}$1";
	$matches[1] = preg_replace($find, $replace, $matches[1]);
	
	// > any line consisting of only indented three closing curly braces
	// > will have one space removed from the indentation
	// > -- http://www.wikicreole.org/wiki/AddNoWikiEscapeProposal
	$find = "/\n( *) }}}/";
	$replace = "\n$1}}}";
	$matches[1] = preg_replace($find, $replace, $matches[1]);
	
	//There is more, but I didn't include it.
	
	return "\n".$CI->syntaxparser->block_hash("<pre>\n".$matches[1]."\n</pre>")."\n";
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

$CI->syntaxparser->add_block_definition('headings', '/^(={1,6}) *(.*)$/m', 'headings_callback', 100);
function headings_callback(&$matches) {
	global $CI;
	//print_r($matches);die();
	$level = strlen($matches[1]);
  $text = trim($matches[2], ' =');
  
  //The header can't accept any other block level elements inside so just inline:
  $text = $CI->syntaxparser->applyAllInlineDefs($text);
  
  
  return $CI->syntaxparser->block_hash('<h'.$level.'>'.$text.'</h'.$level.'>')."\n"; //Maybe we don't need the newlines
}

/**
 * Horizontal Rule
 * ---- -> <hr />
 */
$CI->syntaxparser->add_block_definition('horizontalrule', '/^[-]{4,}$/m', 'horizontalrule_callback', 200);
function horizontalrule_callback(&$matches) {
	global $CI;
	
	return $CI->syntaxparser->block_hash('<hr />');
}

/**
 * Lists
 * NOTE: Should come before the Strong syntax since ** is interpreted as the strong syntax. (Disregard for now)
 */
//Watch out for the \n at the beginning and the \n at the end of the regex. The list could be at the
//beginning or end of the long string so we should insert \n at the beginning and end of the string.
$CI->syntaxparser->add_block_definition('lists', '/\n(?:(?:\*|\#)(?:.+?\n)+)+/', 'lists_callback', 235, true);

function lists_callback(&$matches) {
	global $CI, $sp_list_type;
	
	$in_text = $matches[0];
	//die($matches[0]);
	
	//Define a few things:
	$tags_def['ul']['identifier'] = '*';
	$tags_def['ol']['identifier'] = '#';
	
	//Construct identifiers regex
	$identifiers = '';
	foreach($tags_def as $each_tag)
	{
		$identifiers .= '\\'.$each_tag['identifier'].'|';
	}
	$identifiers = trim($identifiers, '|');
	
	//Construct tags regex
	$tags = '';
	foreach(array_keys($tags_def) as $each_tag_key)
	{
		$tags .= $each_tag_key.'|';
	}
	$tags = trim($tags, '|');
	//die($tags);

	/**
	 * This ugly bit groups multiline list items into
	 * one array key.
	 */	 	 	 	
	$in_text = trim($in_text);
	$split_text = explode("\n", $in_text);
	//print_r($split_text);
	//die();
	$new_split_text = array();
	$new_split_text_count = 0;
	for($i=0; $i<count($split_text); $i++)
	{
		if(isset($new_split_text[$new_split_text_count]))
		{
			$new_split_text[$new_split_text_count] .= "\n".$split_text[$i];
		}
		else
		{
			$new_split_text[$new_split_text_count] = $split_text[$i];
		}
		//if(isset($split_text[$i+1]) && !preg_match('/\s*(?:'.$identifier.'|'.$anti_identifiers.')+\s+.*/', $split_text[$i+1]))
		if(isset($split_text[$i+1]) && !preg_match('/\s*(?:'.$identifiers.')+\s*.*/', $split_text[$i+1]))
		{
			//echo 'here'. $split_text[$i].'|'.$split_text[$i+1]."\n";
			//Do nothing. Since we don't increment the $new_split_text_count, we can
			//keep concatinating.
		}
		else
		{
			//Fix for detecting if a line is actually bold. If that's the case, we
			//just merge is with the previous line.
			if(isset($split_text[$i+1]) && lists_determine_if_bold($split_text[$i+1]))
			{
			}
			else
			{
				//$new_split_text[$new_split_text_count] = $split_text[$i];
				$new_split_text_count++;
			}
		}
	}
	//print_r($new_split_text);
	//die();
	
	//Check to see if we actually have a bold entry instead of a list
	if(count($new_split_text) == 1 && preg_match('/^\*\*.+$/s', $new_split_text[0]))
	{
		return "\n".$new_split_text[0]."\n";
	}
	
	//If the list starts out with a level greater than 1, then we pad the beginning
	//of the array with the previous levels.
	$new_split_first_entry = get_list_entry_info($new_split_text[0]);
	if($new_split_first_entry['level'] > 1)
	{
		//die($new_split_first_entry['level']);
		//die($new_split_first_entry['content'].print_r($new_split_first_entry));
		$temp_identifier = $tags_def[$new_split_first_entry['type']]['identifier'];
		for($i=1; $i<$new_split_first_entry['level']; $i++)
		{	
			//die($i.'asdf');
			array_unshift($new_split_text, str_repeat($temp_identifier, $i).' '); 
		}
	}
	
	//Recursively process lists
	$in_text = lists($new_split_text);
	//die($in_text);

	return "\n".$CI->syntaxparser->block_hash($in_text)."\n";
}
function lists_determine_if_bold($in_entry) {
	//$split_entry = preg_split('/(\*\*)/', $in_entry, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
	//print_r($split_entry);
	
	if(preg_match('/^\*\*[^\*]/', $in_entry) && preg_match_all('/(\*\*)/', $in_entry, $matches))
	{
		//print_r($matches);
		//echo $in_entry;
		//die();
		$num_of_delimiters = count($matches[1]); //[1] contains matches (as subarray)
		if($num_of_delimiters & 1) //bitwise check for oddness
		{
			//Odd. This means that we are in a list
			return false;
		}
		else
		{
			//Even
			return true;
		}
	}
	
	return false;
}
function lists($in_list_array, $ol_index=1) {
	global $CI;

	//Define some variables
	$tags_def['ul']['identifier'] = '*';
	$tags_def['ol']['identifier'] = '#';
	
	//Construct identifiers regex
	$identifiers = '';
	foreach($tags_def as $each_tag)
	{
		$identifiers .= '\\'.$each_tag['identifier'].'|';
	}
	$identifiers = trim($identifiers, '|');
	//Construct tags regex
	$tags = '';
	foreach(array_keys($tags_def) as $each_tag_key)
	{
		$tags .= $each_tag_key.'|';
	}
	$tags = trim($tags, '|');	
	
	//-------------------------------
	
	$first_entry = get_list_entry_info($in_list_array[0]);
	$list_type = $first_entry['type'];
	$list_level = $first_entry['level'];
	
	$type_tabs = str_repeat("\t", ($list_level-1)*2); //Calculate indents correctly
	$tabs = $type_tabs."\t";
	
	$inner_list = array();
	$end_type_tag = true; //Do we print the end type tag?
	//$ol_index = 1; //If a ul cuts into an ol, we can resume the index from this.
	if($list_type == 'ol' && $ol_index > 1)
	{
		$list_html = $type_tabs.'<'.$list_type.' start="'.$ol_index.'">'."\n";
	}
	else
	{
		$list_html = $type_tabs.'<'.$list_type.'>'."\n";
	}
	foreach($in_list_array as $key => $each_line)
	{
		$entry_info = get_list_entry_info($each_line);
		//Check level first
		if($entry_info['level'] == $list_level)
		{
			if($entry_info['type'] == $list_type)
			{
				$list_html .= $tabs.'<li>'.$CI->syntaxparser->applyAllInlineDefs($entry_info['content']);
				if(isset($in_list_array[$key+1]))
				{
					$next_entry_info = get_list_entry_info($in_list_array[$key+1]);
					if($next_entry_info['level'] == $list_level && $next_entry_info['type'] == $list_type)
					{
						$list_html .= '</li>'."\n";
					}
					else
					{
						//Nothing
						$list_html .= "\n";
					}
				}
				else
				{
					$list_html .= '</li>'."\n";
				}
			}
			else
			{
				//Same level, different type => new list
				$inner_list[] = $each_line;
				
				//Check next line
				if(isset($in_list_array[$key+1]))
				{
					$next_entry_info = get_list_entry_info($in_list_array[$key+1]);
					if($next_entry_info['type'] != $entry_info['type']) //$next_entry_info['level'] != $list_level && 
					{
						$list_html .= $type_tabs.'</'.$list_type.'>'."\n";
						$list_html .= lists($inner_list)."\n";//.$tabs.'</li>'."\n";
						//$list_html .= $type_tabs.'<'.$list_type.'>'."\n";
						$inner_list = array();
						$end_type_tag = false;
						
						//Put the rest of the array through lists
						$list_html .= lists(array_slice($in_list_array, $key+1), $ol_index+1)."\n";
						return $list_html; //This ends execution of this function
					}
				}
				else
				{
					$list_html .= $type_tabs.'</'.$list_type.'>'."\n";
					$list_html .= lists($inner_list);//.$tabs.'</li>'."\n";
					//$list_html .= $type_tabs.'<'.$list_type.'>'."\n";
					$inner_list = array();
					$end_type_tag = false;
				}
			}
		}
		else
		{
			//Diff level
			$inner_list[] = $each_line;
			
			//Check next line
			if(isset($in_list_array[$key+1]))
			{
				$next_entry_info = get_list_entry_info($in_list_array[$key+1]);
				if($next_entry_info['level'] == $list_level)
				{
					$list_html .= lists($inner_list)."\n".$tabs.'</li>'."\n";
					$inner_list = array();
				}
			}
			else
			{
				$list_html .= lists($inner_list)."\n".$tabs.'</li>'."\n";
				$inner_list = array();
			}
		}
		
	}
	if($end_type_tag)
	{
		$list_html .= $type_tabs.'</'.$list_type.'>';
		$end_type_tag = false;
	}
	
	return $list_html;
}
function get_list_entry_info($in_entry) {
	//Define some variables
	$tags_def['ul']['identifier'] = '*';
	$tags_def['ol']['identifier'] = '#';
	
	//Construct identifiers regex
	$identifiers = '';
	foreach($tags_def as $each_tag)
	{
		$identifiers .= '\\'.$each_tag['identifier'].'|';
	}
	$identifiers = trim($identifiers, '|');
	//Construct tags regex
	$tags = '';
	foreach(array_keys($tags_def) as $each_tag_key)
	{
		$tags .= $each_tag_key.'|';
	}
	$tags = trim($tags, '|');	
	
	if(preg_match('/\s*((?:'.$identifiers.')+)\s*(.*)/s', $in_entry, $matches))
	{
		$list_type_symbol = substr($matches[1], 0, 1); //Get first character
		switch($list_type_symbol)
		{
			case $tags_def['ul']['identifier']:
				$list_type = 'ul';
				break;
			case $tags_def['ol']['identifier']:
				$list_type = 'ol';
				break;
			default:
				$list_type = 'ul'; //Let's just assume this
		}
		$list_level = strlen($matches[1]);
		$list_content = $matches[2];
	}
	else
	{
		//Something is wrong.
		return 'Invalid list';
	}
	
	//Return as array
	$return_info['type'] = $list_type;
	$return_info['level'] = $list_level;
	$return_info['content'] = $list_content;
	
	return $return_info;

}

/**
 * Tables
 */
$CI->syntaxparser->add_block_definition('tables', '/\n(?:\|.+?\n)+/s', 'tables_callback', 250, true);
function tables_callback(&$matches) {
	global $CI;
	
	$table_html = "\n".'<table class="wiki_syntax_table">'."\n";
	
	//$syntax_rows = preg_split('/\n/', trim($matches[0])); //We trim to remove the beginning and end \n that we match
	$syntax_rows = explode("\n", trim($matches[0]));
	
	foreach($syntax_rows as $each_row) 
	{
		$table_html .= "<tr>\n";
		$row_elements = explode('|', trim($each_row, '|')); //We trim to remove the |'s on the end.
		foreach($row_elements as $each_cell)
		{
			$each_cell = trim($each_cell); //We trim again to remove any white space used to form the table.
			//Check for table headers
			if(preg_match('/^=(.+)/', $each_cell, $cell_matches))
			{
				$cell_matches[1] = $CI->syntaxparser->applyAllInlineDefs($cell_matches[1]);
				$table_html .= "<th>".$cell_matches[1]."</th>\n";	
			}
			else
			{
				$each_cell = $CI->syntaxparser->applyAllInlineDefs($each_cell);
				$table_html .= "<td>".$each_cell."</td>\n";
			} 
		}
		$table_html .= "</tr>\n";
	}
	
	$table_html .= "</table>\n";
	
	return $CI->syntaxparser->block_hash($table_html);
			
	//return "\n".'<pre>'.$matches[0].'</pre>'."\n";
} 

/**
 * Paragraph
 * (put <p> and </p>)
 */
$CI->syntaxparser->add_block_definition('paragraph', '/\n?(.+?)(\n\s*\n|\z)/s', 'paragraph_callback', 2020, true);
/**
 * @author Wordpress
 * $matches[2] contains the newlines 
 */ 
function paragraph_callback(&$matches) {
	global $CI;
	
	if(preg_match('/'.$CI->syntaxparser->block_token_pattern.'/', $matches[1]))
	{
		//This takes care of cases where one block is right under another with no
		//line break inbetween. Essentially, we are checking for the existance of
		//hashed blocks and then ignoring the hashed blocks (and working only on
		//the visible blocks). For example:
		//= Header =
		//Paragraph right underneath header without a newline inbetween.
		$split_by_token = preg_split('/(\s*'.$CI->syntaxparser->block_token_pattern.'\s*)/', $matches[1], -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
		
		//NOTE: Tokens (hashes) are stored in $split_by_token each as their own entry.
		$output = '';
		foreach($split_by_token as $each_split)
		{
			//echo $each_split."\n";
			if(!preg_match('/'.$CI->syntaxparser->block_token_pattern.'/', $each_split))
			{
				$each_split = $CI->syntaxparser->applyAllInlineDefs($each_split);
				$output .= '<p>'.$each_split.'</p>';
			}
			else
			{
				$output .= $each_split;
			}
		}
		return $output.$matches[2];
	}
	else if(!preg_match('/'.$CI->syntaxparser->block_token_pattern.'/', $matches[1]))
	{
		$matches[1] = $CI->syntaxparser->applyAllInlineDefs($matches[1]);
		return '<p>'.$matches[1].'</p>'.$matches[2];
	}

	return $matches[1].$matches[2]; //Why don't we need to prepend \n ?
}

//Specify inline elements

/**
 * Escape Character
 */  
//We parse as a block-level element

//Creole 1.0 defines the monospace/tt as part of preformatted. We match {{{ }}}.
//NOTE: This should be checked VERY carefully against the Creole specification.
//      I have a feeling that this is currently wrong.
//Creole 1.0 says that this doesn't HAVE to be monospace.
$CI->syntaxparser->add_inline_definition('tt', '/{{{(.*?)}}}/', 'tt_callback', 100, true);
function tt_callback(&$matches) {
	global $CI;
	//die($matches[1]);
	return $CI->syntaxparser->inline_hash('<tt>'.$matches[1].'</tt>');
}

/**
 * Escaping HTML
 */
//$CI->syntaxparser->add_inline_definition('escape_html_1', '/</', '&lt;', 102);
//$CI->syntaxparser->add_inline_definition('escape_html_2', '/>/', '&gt;', 103);

/**
 * Newlines
 * Convert all \n in to <br />. 
 */  
$CI->syntaxparser->add_inline_definition('newline', '/\n/', " <br />\n", 110); //Note the space before the <br />

/**
 * Image (inline)
 * {{myimage.png|text}} -> <img src="myimage.png" alt="text"> 
 */ 
$CI->syntaxparser->add_inline_definition('inlineimage', '/\{\{(.+?)\}\}/', 'inlineimage_callback', 130, true);
function inlineimage_callback(&$matches) {
	global $CI;
	
	//NOTE: Maybe we should add check for image format so that people don't
	//include scripts or other malicious stuff.
	
	//ALSO: Allow for local images case (remove http:// check in the beginning)
	
	//Match external url with alt text. This should come before the 
	//'just external url case' since \S+ also matches the | character.
	if(preg_match('/([a-z]+:\/\/\S+)\|(.+)/', $matches[1], $url_matches)) //if preg_match does not return 0
	{
		$url_matches[1] = $CI->input->xss_clean($url_matches[1]);
		$url_matches[1] = htmlentities($url_matches[1], ENT_QUOTES, 'UTF-8');
		$url_matches[2] = $CI->input->xss_clean($url_matches[2]);
		$url_matches[2] = htmlentities($url_matches[2], ENT_QUOTES, 'UTF-8');
		return $CI->syntaxparser->inline_hash('<img src="'.$url_matches[1].'" alt="'.$url_matches[2].'" />');
	}
	
	//Match just external url.
	if(preg_match('/([a-z]+:\/\/\S+)/', $matches[1], $url_matches))
	{
		$url_matches[1] = $CI->input->xss_clean($url_matches[1]);
		$url_matches[1] = htmlentities($url_matches[1], ENT_QUOTES, 'UTF-8');
		return $CI->syntaxparser->inline_hash('<img src="'.$url_matches[1].'" />');
		
	}
	
	//For everything else that doesn't seem to match.
	return '{{'.$matches[1].'}}';
}

/**
 * Links
 *  
 */
$CI->syntaxparser->add_inline_definition('links', '/\[\[(.+?)\]\]/', 'links_callback', 140, true);
function links_callback(&$matches) {
	global $CI;

	//For the http, etc. check, we should also include local paths (ie. images/logo.gif)
	//Recall: (?: means no capturing.

	//Match external url with link text. This should come before the 
	//'just external url case' since \S+ also matches the | character.
	if(preg_match('/^([a-z]+:\/\/\S+)\|(.+)/', $matches[1], $url_matches)) //if preg_match does not return 0
	{
		$url_matches[1] = $CI->input->xss_clean($url_matches[1]);
		$url_matches[1] = htmlentities($url_matches[1], ENT_QUOTES, 'UTF-8');
		$url_matches[2] = $CI->input->xss_clean($url_matches[2]);
		$url_matches[2] = htmlentities($url_matches[2], ENT_QUOTES, 'UTF-8');
		return $CI->syntaxparser->inline_hash('<a href="'.$url_matches[1].'" class="external">'.$url_matches[2].'</a>');
	}
	
	//Match just external url.
	if(preg_match('/^([a-z]+:\/\/\S+)/', $matches[1], $url_matches))
	{
		$url_matches[1] = $CI->input->xss_clean($url_matches[1]);
		$url_matches[1] = htmlentities($url_matches[1], ENT_QUOTES, 'UTF-8');
		return $CI->syntaxparser->inline_hash('<a href="'.$url_matches[1].'" class="external">'.$url_matches[1].'</a>');
	}
	
	//Match mailto: type links. NOTE: This could be dangerous if we don't check well.
	//javascript injection possible! This is crude!
	if(preg_match('/^([a-z]+:\S+@\S+)\|(.+)/', $matches[1], $url_matches)) //if preg_match does not return 0
	{
		$url_matches[1] = $CI->input->xss_clean($url_matches[1]);
		$url_matches[1] = htmlentities($url_matches[1], ENT_QUOTES, 'UTF-8');
		$url_matches[2] = $CI->input->xss_clean($url_matches[2]);
		$url_matches[2] = htmlentities($url_matches[2], ENT_QUOTES, 'UTF-8');
		return $CI->syntaxparser->inline_hash('<a href="'.$url_matches[1].'" class="external">'.$url_matches[2].'</a>');
	}
		if(preg_match('/^([a-z]+:\S+@\S+)/', $matches[1], $url_matches)) //if preg_match does not return 0
	{
		$url_matches[1] = $CI->input->xss_clean($url_matches[1]);
		$url_matches[1] = htmlentities($url_matches[1], ENT_QUOTES, 'UTF-8');
		return $CI->syntaxparser->inline_hash('<a href="'.$url_matches[1].'" class="external">'.$url_matches[1].'</a>');
	}
	
	//Match WikiLinks (The regex for these should be better...and safer)
	if(preg_match('/^(.+)\|(.+)/', $matches[1], $link_matches))
	{
		$link_matches[1] = $CI->input->xss_clean($link_matches[1]);
		$link_matches[1] = htmlentities($link_matches[1], ENT_QUOTES, 'UTF-8');
		//Translate text into a linkable wikiword
		$link_matches[1] = wiki_url_title($link_matches[1]);

		$link_matches[2] = $CI->input->xss_clean($link_matches[2]);
		$link_matches[2] = htmlentities($link_matches[2], ENT_QUOTES, 'UTF-8');
		
		//Check for wiki-page existance
		if(does_page_exist($link_matches[1]))
		{
			$return_url = '<a href="'.construct_page_url($link_matches[1]).'">'.$link_matches[2].'</a>';
		}
		else
		{
			$return_url = '<a href="'.construct_page_url($link_matches[1]).'" class="missingpage" title="Create this page">'.$link_matches[2].'</a>';
		}
		
		return $CI->syntaxparser->inline_hash($return_url);
	}	
	
	//Dangerous regex? Limit characters
	if(preg_match('/^(.+)/', $matches[1], $link_matches))
	{
		$link_matches[1] = $CI->input->xss_clean($link_matches[1]);
		$link_matches[1] = htmlentities($link_matches[1], ENT_QUOTES, 'UTF-8');
		
		//Check for wiki-page existance
		if(does_page_exist($link_matches[1]))
		{
			$return_url = '<a href="'.construct_page_url(wiki_url_title($link_matches[1])).'">'.$link_matches[1].'</a>';
		}
		else
		{
			$return_url = '<a href="'.construct_page_url(wiki_url_title($link_matches[1])).'" class="missingpage" title="Create this page">'.$link_matches[1].'</a>';
		}
		
		return $CI->syntaxparser->inline_hash($return_url);
	}	
	
	//For everything else that doesn't seem to match.
	return '[['.$matches[1].']]';
	
	//Interwiki links
	//(not implemented)
}

/**
 * WikiWord Links
 * @author Paul M. Jones <pmjones@php.net>
 */  
//This is good for most purposes, but does not take into account other languages
$upper = "A-Z";
$lower = "a-z0-9";
$either = "A-Za-z0-9";
$wikiword_regex =      
						"/(^|\s)(!?" .      // START WikiPage pattern (1) //Hmm need to check for a space or beginning of line
            "[$upper]" .       // 1 upper
            "[$either]*" .     // 0+ alpha or digit
            "[$lower]+" .      // 1+ lower or digit
            "[$upper]" .       // 1 upper
            "[$either]*" .     // 0+ or more alpha or digit
            ")/";               // END WikiPage pattern (/1)
$CI->syntaxparser->add_inline_definition('wikiwordlink', $wikiword_regex, 'wikiwordlink_callback', 150, true);
function wikiwordlink_callback(&$matches) {
	global $CI;
	
	//$matches[1] includes the whitespace characters.
	$matches[2] = $CI->input->xss_clean($matches[2]);
	$matches[2] = htmlentities($matches[2], ENT_QUOTES, 'UTF-8');
	
	//Check for wiki-page existance
	if(does_page_exist($matches[2]))
	{
		$return_url = '<a href="'.construct_page_url($matches[2]).'">'.$matches[2].'</a>';
	}
	else
	{
		$return_url = '<a href="'.construct_page_url($matches[2]).'" class="missingpage" title="Create this page">'.$matches[2].'</a>';
	}
	
	return $matches[1].$CI->syntaxparser->inline_hash($return_url);
}

/**
 * Raw URLs.
 * Turned into clickable links. However, single punctuation characters
 * (,.?!:;"') at the end of the URLs should not be considered part of the URL
 * NOTE: This should go towards the end where all other URLs are already parsed
 *       (like [[ links ]] and {{ images }}, etc.)  
 * The regex below is complicated because of the pesky escape character in the
 * front of a URL:
 * ie. ~http://www.server.com
 * Since the h after the ~ is hashed, the regex below checks for the case where
 * we have ~ and the hashed pattern. Since the hash pattern can change, we get
 * the regex from the SyntaxParser class. It's really inelegant. 
 * 
 * Perhaps we can just check for a delimiter or a space in front instead of 
 * checking for the whole token       
 */
$CI->syntaxparser->add_inline_definition('raw_url', '/(\s)('.$CI->syntaxparser->getTokenPattern().')?([a-z]+:\/\/)(\S+)/', 'raw_url_callback', 185, true); //The lesser complex version is: '([a-z]+:\/\/)(\S+)/'
function raw_url_callback(&$matches) {
	global $CI;
	
	//Check for escaped URL. If found, just return unlinked URL.
	if(!empty($matches[2])) //Even though we have ?, if ()? doesn't occur, $matches[1] will be empty
	{
		$matches[2] = $CI->syntaxparser->unhash($matches[2]);
		return $matches[1].$CI->syntaxparser->inline_hash($matches[2].$matches[3].$matches[4]); //Preserve the url so that // isn't interpreted as italics
	}
	
	//We won't consider single punctuation characters at the end of the URL
	if(preg_match('/(\S+)([,\.\?!:;"\'\)\(]+)(\S+)?/', $matches[4], $raw_url_matches)) 
	{
		//This is kind of crude, but works. Perhaps a recursive approach would be
		//more elegant.
		//For cases like: http://www.another.rawlink.org where .org is in the second (\S+)
		if(!empty($raw_url_matches[3])) 
		{
			$url = $CI->input->xss_clean($matches[3].$raw_url_matches[1].$raw_url_matches[2].$raw_url_matches[3]);
			$url = htmlentities($url, ENT_QUOTES, 'UTF-8');
			return $matches[1].$CI->syntaxparser->inline_hash('<a href="'.$url.'" class="external">'.$url.'</a>');
		}

		$url = $CI->input->xss_clean($matches[3].$raw_url_matches[1]);
		$url = htmlentities($url, ENT_QUOTES, 'UTF-8');
		return $matches[1].$CI->syntaxparser->inline_hash('<a href="'.$url.'" class="external">'.$url.'</a>').$raw_url_matches[2]; //We keep the punctuation on the end.
	}
	
	$url = $CI->input->xss_clean($matches[3].$matches[4]);
	$url = htmlentities($url, ENT_QUOTES, 'UTF-8');
	return $matches[1].$CI->syntaxparser->inline_hash('<a href="'.$url.'" class="external">'.$url.'</a>');
}

/**
 * Emphasis/Italics
 * // // -> <em> </em>
 * (Double check regex)
 * Note: The ordering here is very important. We need to take care of emphasis
 *       over lines and paragraphs after we do the normal inline stuff.  
 * Italics *MUST* be loaded before Bold according to Creole specifications. 
 */
$CI->syntaxparser->add_inline_definition('emphasis_inline', '/\/\/(.+?)\/\//', '<em>$1</em>', 339); //Maybe we need to make this ungreedy
$CI->syntaxparser->add_inline_definition('emphasis_cross_lines', '/\/\/(.+\n.*)\/\//', '<em>$1</em>', 340);
$CI->syntaxparser->add_inline_definition('emphasis_noclose', '/\/\/(.+\n?.*)/', '<em>$1</em>', 345); //Takes care of cases where // to end of line -> italics.


/**
 * Strong/Bold
 * ** ** -> <strong> </strong>
 * Note: The ordering here is very important. See reasoning for Emphasis. 
 */
$CI->syntaxparser->add_inline_definition('strong', '/\*\*(.*?)\*\*/', '<strong>$1</strong>', 350);
$CI->syntaxparser->add_inline_definition('strong_cross_lines', '/\*\*(.+\n.*)\*\*/', '<strong>$1</strong>', 351);
$CI->syntaxparser->add_inline_definition('strong_noclose', '/\*\*(.+\n?.*)/', '<strong>$1</strong>', 355);

/**
 * Line Breaks
 * \\ to <br />\n
 * (The problem with this current implementation is that if we have \\\
 *  that will translate to \n\ (where \\n might be preferred). We have to
 *  consult Creole more carefully.)
 *  I'm not sure why we need '/\\\\\\\/' to match correctly when the correct
 *  way, I thought, was '/\\\\/'. Is this a bug?    
 */
$CI->syntaxparser->add_inline_definition('linebreak', '/\\\\\\\/', "<br />\n", 400);   

//Inline Postfilters

//Unhash everything. This is absolutely necessary to reverse all of the hiding done by other functions.
$CI->syntaxparser->add_inline_definition('unhash_all', '/('.$CI->syntaxparser->getTokenPattern().')/', 'unhash_all_callback', 2000, true);
function unhash_all_callback(&$matches) {
	global $CI;
	
	$matches[1] = trim($matches[1], $CI->syntaxparser->block_delimiter);
	return $CI->syntaxparser->unhash($matches[1]);
}



?>
