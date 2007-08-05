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

//We want to specify blocks first.

$CI->syntaxparser->add_block_definition('preformatted', '/\n{{{\n(.*)\n}}}\n/Us', 'preformatted_callback', 100, true);
function preformatted_callback(&$matches) {
	global $CI;

	//Currently taken from Preformatted.php from Creole of Pear::Text_wiki
	//@author Tomaiuolo Michele <tomamic@yahoo.it>
	
	global $preformatted_storage, $preformatted_storage_count, $preformatted_token;
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
	
	return "\n".$CI->syntaxparser->hash("<pre>\n".$matches[1]."\n</pre>")."\n";
}  

$CI->syntaxparser->add_block_definition('headings', '/^(={1,6}) *(.*?) *=*$/m', 'headings_callback', 100);
function headings_callback(&$matches) {
	global $CI;
	
	$level = strlen($matches[1]);
  $text = trim($matches[2]);
  
  //The header can't accept any other block level elements inside so just inline:
  $text = $CI->syntaxparser->applyAllInlineDefs($text);
  
  return $CI->syntaxparser->hash('<h'.$level.'>'.$text.'</h'.$level.'>'); //Maybe we don't need the newlines
}

/**
 * Horizontal Rule
 * ---- -> <hr />
 */
$CI->syntaxparser->add_block_definition('horizontalrule', '/^[-]{4,}$/m', 'horizontalrule_callback', 200);
function horizontalrule_callback(&$matches) {
	global $CI;
	
	return $CI->syntaxparser->hash('<hr />');
}

/**
 * Lists
 * NOTE: Should come before the Strong syntax since ** is interpreted as the strong syntax. (Disregard for now)
 */
//Watch out for the \n at the beginning and the \n at the end of the regex. The list could be at the
//beginning or end of the long string so we should insert \n at the beginning and end of the string.
$CI->syntaxparser->add_block_definition('unordered_lists', '/\n(?:\*.+?\n)+/s', 'unordered_lists_callback', 235, true);
$CI->syntaxparser->add_block_definition('ordered_lists', '/\n(?:\#.+?\n)+/s', 'ordered_lists_callback', 236, true);
function unordered_lists_callback(&$matches) {
	//die($matches[0]);
	return lists($matches[0], 'unordered');	

} 
function ordered_lists_callback(&$matches) {

	//return 'asdf';
	return lists($matches[0], 'ordered');	

} 
function lists($in_text, $type) {
	global $CI;
	
	$inner_list = ''; //Make empty
	
	/*
	//If we match ** by itself without surrounding *, then we know that it
	//is the 'strong' modifier. Therefore, we return without doing anything.
	if(preg_match('/\n\*\*.+$/', $in_text))
	{
		return 'here'.$in_text; //We should more rigorously do the newline stuff. Right now, it's a lot of guesswork.
	}
	*/
	
	//We should do something similar for ##.
	
	//This function is recursive! If we don't stop it, it will run infinitely!
	
	//Split by each line
	if(strcmp($type, 'unordered')==0)
	{
		$tag = 'ul';
		$identifier = '\*';
	}
	else if(strcmp($type, 'ordered')==0)
	{
		$tag = 'ol';
		$identifier = '\#';
	}
	else
	{
		return '';
	}

	$in_text = preg_replace_callback('/\n\s*('.$identifier.'+)\s+(.*)/', 'lists_callback', $in_text);
	while(preg_match('|</li></ul><ul><li><ul>|', $in_text)) //Note that we have a <ul> on the end of this. We match multilevel list
	{
		$in_text = preg_replace('|</li></ul><ul><li>|', '', $in_text);
	}
	
	//This is for lists with a single level. We assume that if the list is multi-
	//level, the </ul><ul>'s are already removed.
	$in_text = preg_replace('|</ul><ul>|', '', $in_text);
	
	//Returned HTML is ugly. Maybe HTML Tidy it sometime.
	return "\n".$CI->syntaxparser->hash($in_text);
}
function lists_callback(&$matches) {
	global $CI;
	
	$level = strlen($matches[1]);
	$text = trim($matches[2]); //Maybe only trim by one space to allow for users to force space.
	$text = $CI->syntaxparser->applyAllInlineDefs($text);
	/*
	if($level == 1)
	{
		return '<li>'.$text.'</li>';
	}
	*/
	
	$pre = '';
	$post = '';
	$list_html = '';
	for($i=0; $i<$level; $i++)
	{
			$pre .= '<ul><li>';
			$post .= '</li></ul>';
	}
	
	$list_html = $pre.$text.$post;
	//echo($list_html); 
	//<ul><li>one</li></ul><ul><li>two</li></ul><ul><li>three</li></ul>
	//<ul><li>1</li></ul><ul><li><ul><li>2</li></ul></li></ul><ul><li><ul><li><ul><li>3</li></ul></li></ul></li></ul><ul><li><ul><li><ul><li><ul><li>4</li></ul></li></ul></li></ul></li></ul><ul><li><ul><li><ul><li><ul><li><ul><li>5</li></ul></li></ul></li></ul></li></ul></li></ul>
	return $list_html;
}

//Ugly hack to connect the lists (inner lists). Later on, we should indent lists inside of lists.
//$CI->syntaxparser->addRule('unordered_lists_postprocess', '/<\/li>\n<li>\n(<ul>.+?<\/ul>)\n/s', "\n".'$1', 237);
//$CI->syntaxparser->addRule('ordered_lists_postprocess', '/<\/li>\n<li>\n(<ol>.+?<\/ol>)\n/s', "\n".'$1', 238);


/**
 * Tables
 */
$CI->syntaxparser->add_block_definition('tables', '/\n(?:\|.+?\n)+/s', 'tables_callback', 250, true);
function tables_callback(&$matches) {
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
				$table_html .= "<th>".$cell_matches[1]."</th>\n";	
			}
			else
			{
				$table_html .= "<td>".$each_cell."</td>\n";
			} 
		}
		$table_html .= "</tr>\n";
	}
	
	$table_html .= "</table>\n";
	
	return $CI->syntaxparser->hash($table_html);
			
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
	
	if(preg_match('/'.$CI->syntaxparser->token_pattern.'/', $matches[1]))
	{
		//This takes care of cases where one block is right under another with no
		//line break inbetween. Essentially, we are checking for the existance of
		//hashed blocks and then ignoring the hashed blocks (and working only on
		//the visible blocks). For example:
		//= Header =
		//Paragraph right underneath header without a newline inbetween.
		$split_by_token = preg_split('/(\s*'.$CI->syntaxparser->token_pattern.'\s*)/', $matches[1], -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
		/*
		echo $matches[1]."\n\n";
		print_r($split_by_token);
		die();
		*/
		$output = '';
		foreach($split_by_token as $each_split)
		{
			if(!preg_match('/'.$CI->syntaxparser->token_pattern.'/', $each_split))
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
	else if(!preg_match('/'.$CI->syntaxparser->token_pattern.'/', $matches[1]))
	{
		$matches[1] = $CI->syntaxparser->applyAllInlineDefs($matches[1]);
		return '<p>'.$matches[1].'</p>'.$matches[2];
	}
	
	/*
	if(preg_match('/((?:'.$CI->syntaxparser->token_pattern.'\s+)*)(.+)((?:'.$CI->syntaxparser->token_pattern.'\s+)*)/s', $matches[1], $with_token_matches))
	{
		die($with_token_matches[0]."\n\n".$with_token_matches[1]."\n\n".$with_token_matches[2]."\n\n".$with_token_matches[3]);

		//$with_token_matches[2] = $CI->syntaxparser->applyAllInlineDefs($with_token_matches[2]);
		//return $with_token_matches[1].'<p>'.$with_token_matches[2].'</p>'.$with_token_matches[3].$matches[2];
	}
	*/
	
	return $matches[1].$matches[2]; //Why don't we need to prepend \n ?
}



//Specify inline elements

//Escape character here. We parse the escape character only if it is at the start
//of some word (so we have a whitespace char in front).
$CI->syntaxparser->add_inline_definition('escape', '/(\s)~(.)/', 'escape_callback', 50, true);
function escape_callback(&$matches) {
	global $CI;
	
	//Protect against some XSS (When user tries to escape every character in XSS
	//in hopes that after unhashing the malicious code is assembled again).
	$matches[1] = htmlentities($matches[1]);
	
	//If \s is a space, we remove it
	if(strcmp($matches[1], ' ')==0)
	{
		return $CI->syntaxparser->hash($matches[2]);
	}
	
	return $matches[1].$CI->syntaxparser->hash($matches[2]);
} 

//Creole 1.0 defines the monospace/tt as part of preformatted. We match {{{ }}}.
//NOTE: This should be checked VERY carefully against the Creole specification.
//      I have a feeling that this is currently wrong.
//Creole 1.0 says that this doesn't HAVE to be monospace.
$CI->syntaxparser->add_inline_definition('tt', '/{{{({*?.*?}*)}}}/', 'tt_callback', 100, true);
function tt_callback(&$matches) {
	global $CI;
	
	return $CI->syntaxparser->hash('<tt>'.$matches[1].'</tt>');
}

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
		$url_matches[1] = htmlentities($url_matches[1], ENT_QUOTES);
		$url_matches[2] = $CI->input->xss_clean($url_matches[2]);
		$url_matches[2] = htmlentities($url_matches[2], ENT_QUOTES);
		return $CI->syntaxparser->hash('<img src="'.$url_matches[1].'" alt="'.$url_matches[2].'" />');
	}
	
	//Match just external url.
	if(preg_match('/([a-z]+:\/\/\S+)/', $matches[1], $url_matches))
	{
		$url_matches[1] = $CI->input->xss_clean($url_matches[1]);
		$url_matches[1] = htmlentities($url_matches[1], ENT_QUOTES);
		return $CI->syntaxparser->hash('<img src="'.$url_matches[1].'" />');
		
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
	if(preg_match('/([a-z]+:\/\/\S+)\|(.+)/', $matches[1], $url_matches)) //if preg_match does not return 0
	{
		$url_matches[1] = $CI->input->xss_clean($url_matches[1]);
		$url_matches[1] = htmlentities($url_matches[1], ENT_QUOTES);
		$url_matches[2] = $CI->input->xss_clean($url_matches[2]);
		$url_matches[2] = htmlentities($url_matches[2], ENT_QUOTES);
		return $CI->syntaxparser->hash('<a href="'.$url_matches[1].'">'.$url_matches[2].'</a>');
	}
	
	//Match just external url.
	if(preg_match('/([a-z]+:\/\/\S+)/', $matches[1], $url_matches))
	{
		$url_matches[1] = $CI->input->xss_clean($url_matches[1]);
		$url_matches[1] = htmlentities($url_matches[1], ENT_QUOTES);
		return $CI->syntaxparser->hash('<a href="'.$url_matches[1].'">'.$url_matches[1].'</a>');
	}
	
	//Match mailto: type links. NOTE: This could be dangerous if we don't check well.
	//javascript injection possible! This is crude!
	if(preg_match('/([a-z]+:\S+)\|(.+)/', $matches[1], $url_matches)) //if preg_match does not return 0
	{
		$url_matches[1] = $CI->input->xss_clean($url_matches[1]);
		$url_matches[1] = htmlentities($url_matches[1], ENT_QUOTES);
		$url_matches[2] = $CI->input->xss_clean($url_matches[2]);
		$url_matches[2] = htmlentities($url_matches[2], ENT_QUOTES);
		return $CI->syntaxparser->hash('<a href="'.$url_matches[1].'">'.$url_matches[2].'</a>');
	}
		if(preg_match('/([a-z]+:\S+)/', $matches[1], $url_matches)) //if preg_match does not return 0
	{
		$url_matches[1] = $CI->input->xss_clean($url_matches[1]);
		$url_matches[1] = htmlentities($url_matches[1], ENT_QUOTES);
		return $CI->syntaxparser->hash($CI->input->xss_clean('<a href="'.$url_matches[1].'">'.$url_matches[1].'</a>'));
	}
	
	//Match WikiLinks (The regex for these should be better...and safer)
	if(preg_match('/(\S+)\|(.+)/', $matches[1], $link_matches))
	{
		$link_matches[1] = $CI->input->xss_clean($link_matches[1]);
		$link_matches[1] = htmlentities($link_matches[1], ENT_QUOTES);
		$link_matches[2] = $CI->input->xss_clean($link_matches[2]);
		$link_matches[2] = htmlentities($link_matches[2], ENT_QUOTES);
		return $CI->syntaxparser->hash($CI->input->xss_clean('<a href="'.construct_page_url($link_matches[1]).'">'.$link_matches[2].'</a>'));
	}	
	
	if(preg_match('/(\S+)/', $matches[1], $link_matches))
	{
		$link_matches[1] = $CI->input->xss_clean($link_matches[1]);
		$link_matches[1] = htmlentities($link_matches[1], ENT_QUOTES);
		return $CI->syntaxparser->hash($CI->input->xss_clean('<a href="'.construct_page_url($link_matches[1]).'">'.$link_matches[1].'</a>'));
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
						"/(\s)(!?" .            // START WikiPage pattern (1) //Hmm need to check for a space or newline?
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
	$matches[2] = htmlentities($matches[2], ENT_QUOTES);
	return $matches[1].$CI->syntaxparser->hash('<a href="'.construct_page_url($matches[2]).'">'.$matches[2].'</a>');
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
$CI->syntaxparser->add_inline_definition('raw_url', '/('.$CI->syntaxparser->getTokenPattern().')?([a-z]+:\/\/)(\S+)/', 'raw_url_callback', 185, true); //The lesser complex version is: '([a-z]+:\/\/)(\S+)/'
function raw_url_callback(&$matches) {
	global $CI;
	
	//Check for escaped URL. If found, just return unlinked URL.
	if(!empty($matches[1])) //Even though we have ?, if ()? doesn't occur, $matches[1] will be empty
	{
		$matches[1] = $CI->syntaxparser->unhash($matches[1]);
		return $CI->input->xss_clean($matches[1].$matches[2].$matches[3]);
	}
	
	//We won't consider single punctuation characters at the end of the URL
	if(preg_match('/(\S+)([,\.\?!:;"\']+)(\S+)?/', $matches[3], $raw_url_matches)) 
	{
		//This is kind of crude, but works. Perhaps a recursive approach would be
		//more elegant.
		//For cases like: http://www.another.rawlink.org where .org is in the second (\S+)
		if(!empty($raw_url_matches[3])) 
		{
			$url = $CI->input->xss_clean($matches[2].$raw_url_matches[1].$raw_url_matches[2].$raw_url_matches[3]);
			$url = htmlentities($url, ENT_QUOTES);
			return $CI->syntaxparser->hash('<a href="'.$url.'">'.$url.'</a>');
		}

		$url = $CI->input->xss_clean($matches[2].$raw_url_matches[1]);
		$url = htmlentities($url, ENT_QUOTES);
		return $CI->syntaxparser->hash('<a href="'.$url.'">'.$url.'</a>').$raw_url_matches[2]; //We keep the punctuation on the end.
	}
	
	$url = $CI->input->xss_clean($matches[2].$matches[3]);
	$url = htmlentities($url, ENT_QUOTES);
	return $CI->syntaxparser->hash('<a href="'.$url.'">'.$url.'</a>');
}



//Inline Postfilters

//Unhash everything. This is absolutely necessary to reverse all of the hiding done by other functions.
$CI->syntaxparser->add_inline_definition('unhash_all', '/('.$CI->syntaxparser->getTokenPattern().')/', 'unhash_all_callback', 2000, true);
function unhash_all_callback(&$matches) {
	global $CI;
	
	$matches[1] = trim($matches[1], $CI->syntaxparser->delimiter);
	return $CI->syntaxparser->unhash($matches[1]);
}



?>
