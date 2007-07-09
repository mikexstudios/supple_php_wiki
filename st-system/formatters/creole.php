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

//We add rules to the SyntaxParser in a certain order

/** 
 * Prefilter things 
 * (well, these things should be moved out of this creole class)
 */ 

//str_replace in this case is faster, but oh well, we sacrifice some.
//Also, I've seen this done with (\r|\r\n), but this is slower since two
//checks are done at each step.
$Supple->SyntaxParser->addRule('to_unix_lineendings', '/\r\n?/', "\n", 10);

//Replace 4 consecutive spaces at the beginning of a line with tab character.
//Since the text is all one long string, we find the start of lines by the \n
//and then we count four consecutive spaces.
$Supple->SyntaxParser->addRule('spaces_to_tab', '/\n[ ]{4}/', "\n\t", 20); 

/**
 * Trim
 * Remove whitespace from the beginning and end of a string
 * Actually trim is pretty important since future regex depend on
 * lines ending cleanly with \n.  
 * (But below implementation could be a little slow)
 * NOTE: Trim conflicts with spaces_to_tab! 
 */ 
$Supple->SyntaxParser->addRule('trim_spaces', '/(.*)/m', 'trim_spaces_callback', 30, true);
function trim_spaces_callback(&$matches) {
	return trim($matches[1]);
} 

//Insert a newline at the beginning and end of the text. This will help
//in regex later since we can assume lines start and end with \n
$Supple->SyntaxParser->addRule('beg_end_newline', '/(.+)/s', "\n".'$1'."\n", 40);

/**
 * Preformatted
 * {{{
 * text in here
 * }}}   
 * is not wiki formatted. 
 * (Note the U is for ungreedy. s is for the . to take into account all
 *  characters including newlines.)  
 */

$Supple->SyntaxParser->addRule('preformatted', '/\n{{{\n(.*)\n}}}\n/Us', 'preformatted_callback', 100, true);
function preformatted_callback(&$matches) {
	global $Supple;

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
	
	return "\n".$Supple->SyntaxParser->hash("<pre>\n".$matches[1]."\n</pre>")."\n";
}  

//Escape character here. We parse the escape character only if it is at the start
//of some word (so we have a whitespace char in front).
$Supple->SyntaxParser->addRule('escape', '/(\s)~(.)/', 'escape_callback', 105, true);
function escape_callback(&$matches) {
	global $Supple;
	
	//If \s is a space, we remove it
	if(strcmp($matches[1], ' ')==0)
	{
		return $Supple->SyntaxParser->hash($matches[2]);
	}
	
	return $matches[1].$Supple->SyntaxParser->hash($matches[2]);
} 

//Creole 1.0 defines the monospace/tt as part of preformatted. We match {{{ }}}.
//NOTE: This should be checked VERY carefully against the Creole specification.
//      I have a feeling that this is currently wrong.
//Creole 1.0 says that this doesn't HAVE to be monospace.
$Supple->SyntaxParser->addRule('tt', '/{{{({*?.*?}*)}}}/', 'tt_callback', 110, true); //Removed the U modifier. Seems to still work.
function tt_callback(&$matches) {
	global $Supple;
	return $Supple->SyntaxParser->hash('<tt>'.$matches[1].'</tt>');
}

/**
 * Snippets
 * well, they are the Wikka/Wakka "actions", but we don't have a better 
 * name for them yet.
 */
$Supple->SyntaxParser->addRule('snippets', '/<<<(.+)>>>/U', 'snippets_callback', 120, true); 
function snippets_callback(&$matches) {
	global $Supple;
	
	$action = trim($matches[1]);

	// search for parameters separated by spaces or newlines - Wikka #371
	if (preg_match('/\s/', $action))
	{
		// parse input for action name and parameters
		preg_match('/^([A-Za-z0-9]*)\s+(.*)$/s', $action, $matches);
		// extract $action and $vars_temp ("raw" attributes)
		list(, $action, $args) = $matches;

		//Call action, pass the args to it
		return $Supple->SyntaxParser->doSnippet($action, $args);
		
	}
	if (!preg_match('/^[a-zA-Z0-9]+$/', $action))
	{
		return '<em class="error">Unknown action; the action name must not contain special characters.</em>';
	}
	
	return $Supple->SyntaxParser->hash($Supple->SyntaxParser->doSnippet($action));
}

//Won't implement Raw, Footnote

//Won't implement Table for now. Should also have option of tables being
//done in HTML.

/**
 * Image (inline)
 * {{myimage.png|text}} -> <img src="myimage.png" alt="text"> 
 */ 
//$Supple->SyntaxParser->addRule('inlineimage', '/{{(.+)(?:\|(.*))?}}/U', '<img src="$1" alt="$2" />', 180); 
$Supple->SyntaxParser->addRule('inlineimage', '/\{\{(.+?)\}\}/', 'inlineimage_callback', 130, true);
function inlineimage_callback(&$matches) {
	global $Supple;
	
	//NOTE: Maybe we should add check for image format so that people don't
	//include scripts or other malicious stuff.
	
	//Match external url with alt text. This should come before the 
	//'just external url case' since \S+ also matches the | character.
	if(preg_match('/([a-z]+:\/\/\S+)\|(.+)/', $matches[1], $url_matches)) //if preg_match does not return 0
	{
		$url_matches[1] = $Supple->SyntaxParser->hash($url_matches[1]);
		$url_matches[2] = $Supple->SyntaxParser->hash($url_matches[2]);
		return '<img src="'.$url_matches[1].'" alt="'.$url_matches[2].'" />';
	}
	
	//Match just external url.
	if(preg_match('/([a-z]+:\/\/\S+)/', $matches[1], $url_matches))
	{
		$url_matches[1] = $Supple->SyntaxParser->hash($url_matches[1]);
		return '<img src="'.$url_matches[1].'" />';
	}
	
	//For everything else that doesn't seem to match.
	return $matches[1];
}


/**
 * Links
 *  
 */
$Supple->SyntaxParser->addRule('links', '/\[\[(.+?)\]\]/', 'links_callback', 140, true);
function links_callback(&$matches) {
	global $Supple;

	//I'm not sure if we can do a if(  = preg_replace) so we will just do a
	//preg_match first.
	//For the http, etc. check, we should also include local paths (ie. images/logo.gif)
	//Recall: (?: means no capturing.

	//Match external url with link text. This should come before the 
	//'just external url case' since \S+ also matches the | character.
	if(preg_match('/([a-z]+:\/\/\S+)\|(.+)/', $matches[1], $url_matches)) //if preg_match does not return 0
	{
		$url_matches[1] = $Supple->SyntaxParser->hash($url_matches[1]); 
		$url_matches[2] = $Supple->SyntaxParser->hash($url_matches[2]);
		return '<a href="'.$url_matches[1].'">'.$url_matches[2].'</a>';
	}
	
	//Match just external url.
	if(preg_match('/([a-z]+:\/\/\S+)/', $matches[1], $url_matches))
	{
		$url_matches[1] = $Supple->SyntaxParser->hash($url_matches[1]);
		return '<a href="'.$url_matches[1].'">'.$url_matches[1].'</a>';
	}
	
	//Match mailto: type links. NOTE: This could be dangerous if we don't check well.
	//javascript injection possible! This is crude!
	if(preg_match('/([a-z]+:\S+)\|(.+)/', $matches[1], $url_matches)) //if preg_match does not return 0
	{
		$url_matches[1] = $Supple->SyntaxParser->hash($url_matches[1]); 
		$url_matches[2] = $Supple->SyntaxParser->hash($url_matches[2]);
		return '<a href="'.$url_matches[1].'">'.$url_matches[2].'</a>';
	}
		if(preg_match('/([a-z]+:\S+)/', $matches[1], $url_matches)) //if preg_match does not return 0
	{
		$url_matches[1] = $Supple->SyntaxParser->hash($url_matches[1]); 
		return '<a href="'.$url_matches[1].'">'.$url_matches[1].'</a>';
	}
	
	//Match WikiLinks (The regex for these should be better...and safer)
	if(preg_match('/(\S+)\|(.+)/', $matches[1], $link_matches))
	{
		$link_matches[2] = $Supple->SyntaxParser->hash($link_matches[2]);
		return '<a href="'.$Supple->SyntaxParser->hash(SITEURL.'/index.php?wiki='.$link_matches[1]).'">'.$link_matches[2].'</a>';
	}	
	
	if(preg_match('/(\S+)/', $matches[1], $link_matches))
	{
		$link_matches[1] = $Supple->SyntaxParser->hash($link_matches[1]); //Crude hack since we nest hashes. Should fix this up later.
		return '<a href="'.$Supple->SyntaxParser->hash(SITEURL.'/index.php?wiki='.$link_matches[1]).'">'.$link_matches[1].'</a>';
	}	
	
	//For everything else that doesn't seem to match.
	return $matches[1];
	
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
$Supple->SyntaxParser->addRule('wikiwordlink', $wikiword_regex, 'wikiwordlink_callback', 150, true);
function wikiwordlink_callback(&$matches) {
	global $Supple;
	
	//$matches[1] includes the whitespace characters.
	return $matches[1].'<a href="'.$Supple->SyntaxParser->hash(SITEURL.'/index.php?wiki='.$matches[2]).'">'.$matches[2].'</a>';
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
 */
$Supple->SyntaxParser->addRule('raw_url', '/(?:'.$Supple->SyntaxParser->getTokenPattern().')?([a-z]+:\/\/)(\S+)/', 'raw_url_callback', 185, true); //The lesser complex version is: '([a-z]+:\/\/)(\S+)/'
function raw_url_callback(&$matches) {
	global $Supple;
	
	//Check for escaped URL. If found, just return unlinked URL.
	if(!empty($matches[1])) //Even though we have ?, if ()? doesn't occur, $matches[1] will be empty
	{
		$matches[1] = $Supple->SyntaxParser->unhash($matches[1]);
		return $matches[1].$matches[2].$matches[3];
	}
	
	//We won't consider single punctuation characters at the end of the URL
	if(preg_match('/(\S+)([,\.\?!:;"\']+)(\S+)?/', $matches[3], $raw_url_matches)) 
	{
		//This is kind of crude, but works. Perhaps a recursive approach would be
		//more elegant.
		//For cases like: http://www.another.rawlink.org where .org is in the second (\S+)
		if(!empty($raw_url_matches[3])) 
		{
			$url = $Supple->SyntaxParser->hash($matches[2].$raw_url_matches[1].$raw_url_matches[2].$raw_url_matches[3]);
			return '<a href="'.$url.'">'.$url.'</a>';
		}
		$url = $Supple->SyntaxParser->hash($matches[2].$raw_url_matches[1]);
		return '<a href="'.$url.'">'.$url.'</a>'.$raw_url_matches[2]; //We keep the punctuation on the end.
	}
	
	$url = $Supple->SyntaxParser->hash($matches[2].$matches[3]);
	return '<a href="'.$url.'">'.$url.'</a>';
}

/**
 * Headings
 * The trick is to match the first couple of ===='s and use the callback function
 * to determine the length of the ===='s. Syntax between the ===='s are not parsed
 * according to Creole. So we should move this earlier.
 *  
 * @author Paul M. Jones <pmjones@php.net>
 * @author Tomaiuolo Michele <tomamic@yahoo.it> 
 * @author Michael Huynh (http://www.mikexstudios.com) 
 */

$Supple->SyntaxParser->addRule('headings', '/^(={1,6}) *(.*?) *=*$/m', 'headings_callback', 190, true);
function headings_callback(&$matches) {
	$level = strlen($matches[1]);
  $text = trim($matches[2]);
  
  return '<h'.$level.'>'.$text.'</h'.$level.'>'; //Maybe we don't need the newlines
}

/**
 * Horizontal Rule
 * ---- -> <hr />
 */
$Supple->SyntaxParser->addRule('horizontalrule', '/^[-]{4,}$/m', '<hr />', 200);

/**
 * Lists
 * NOTE: Should come before the Strong syntax since ** is interpreted as the strong syntax. (Disregard for now)
 */
//Watch out for the \n at the beginning and the \n at the end of the regex. The list could be at the
//beginning or end of the long string so we should insert \n at the beginning and end of the string.
$Supple->SyntaxParser->addRule('unordered_lists', '/\n(?:\*.+?\n)+/s', 'unordered_lists_callback', 235, true);
$Supple->SyntaxParser->addRule('ordered_lists', '/\n(?:\#.+?\n)+/s', 'ordered_lists_callback', 236, true);
function unordered_lists_callback(&$matches) {

	return lists($matches[0], 'unordered');	

} 
function ordered_lists_callback(&$matches) {

	//return 'asdf';
	return lists($matches[0], 'ordered');	

} 
function lists($in_text, $type) {
	global $Supple;
	
	$inner_list = ''; //Make empty
	
	//If we match ** by itself without surrounding *, then we know that it
	//is the 'strong' modifier. Therefore, we return without doing anything.
	if(preg_match('/\n\*\*.+$/', $in_text))
	{
		return $in_text; //We should more rigorously do the newline stuff. Right now, it's a lot of guesswork.
	}
	
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
	
	//The weird thing is that two new lines are inserted in the XHTML output
	//even though only one \n is here.
	$list_html = "\n<$tag>\n";
	//See http://us.php.net/manual/en/function.preg-match-all.php
	//to figure out code below:
	if(preg_match_all('/'.$identifier.'(.+)\n/', $in_text, $list_matches))
	{
		foreach($list_matches[1] as $each_line)
		{
			if(preg_match('/('.$identifier.'.+)/', $each_line))
			{
				$inner_list .= $each_line."\n";
			}
			else if(!empty($inner_list)) //We finally get to a line that doesn't start with *
			{
				//Reset inner_list
				//$inner_list = '';
				$list_html .= '<li>'.preg_replace_callback('/(?:'.$identifier.'.+?\n)+/s', $type.'_lists_callback', $inner_list).'</li>'."\n";
				$inner_list = '';
				$list_html .= '<li>'.$each_line.'</li>'."\n"; //We need this to output the each_line for the line right after an inner list.
			}
			else
			{
				$list_html .= '<li>'.$each_line.'</li>'."\n";
			}
			//$list_html .= '<li>'.preg_replace_callback('/(?:\*.+?)+/s', 'unordered_lists_callback', $each_line).'</li>'."\n";
			//$list_html .= '<li>'.$each_line.'</li>'."\n";
		}
	}
	$list_html .= "</$tag>\n";
		
	return $list_html;	
}

//Ugly hack to connect the lists (inner lists). Later on, we should indent lists inside of lists.
$Supple->SyntaxParser->addRule('unordered_lists_postprocess', '/<\/li>\n<li>\n\n(<ul>.+?<\/ul>)\n/s', "\n".'$1', 237);
$Supple->SyntaxParser->addRule('ordered_lists_postprocess', '/<\/li>\n<li>\n\n(<ol>.+?<\/ol>)\n/s', "\n".'$1', 238);


/**
 * Tables
 */
$Supple->SyntaxParser->addRule('tables', '/\n(?:\|.+?\n)+/s', 'tables_callback', 250, true);
function tables_callback(&$matches) {
	$table_html = "\n<table>\n";
	
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
	
	return $table_html;
			
	//return "\n".'<pre>'.$matches[0].'</pre>'."\n";
} 


/**
 * Emphasis/Italics
 * // // -> <em> </em>
 * (Double check regex)
 * Note: The ordering here is very important. We need to take care of emphasis
 *       over lines and paragraphs after we do the normal inline stuff.  
 * Italics *MUST* be loaded before Bold according to Creole specifications. 
 */
$Supple->SyntaxParser->addRule('emphasis_inline', '/\/\/(.+?)\/\//', '<em>$1</em>', 339); //Maybe we need to make this ungreedy
$Supple->SyntaxParser->addRule('emphasis_cross_lines', '/\/\/(.+\n.*)\/\//', '<em>$1</em>', 340);
$Supple->SyntaxParser->addRule('emphasis_cross_paragraph', '/\/\/(.+)\n{2,}?/', '<em>$1</em>'."\n\n", 345);


/**
 * Strong/Bold
 * ** ** -> <strong> </strong>
 * Note: The ordering here is very important. See reasoning for Emphasis. 
 */
$Supple->SyntaxParser->addRule('strong', '/\*\*(.*?)\*\*/', '<strong>$1</strong>', 350);
$Supple->SyntaxParser->addRule('strong_cross_lines', '/\*\*(.+\n.*)\*\*/', '<strong>$1</strong>', 351);
$Supple->SyntaxParser->addRule('strong_cross_paragraph', '/\*\*(.+)\n{2,}?/', '<strong>$1</strong>'."\n\n", 355);


/**
 * Line Breaks
 * \\ to <br />\n
 * (The problem with this current implementation is that if we have \\\
 *  that will translate to \n\ (where \\n might be preferred). We have to
 *  consult Creole more carefully.)
 *  I'm not sure why we need '/\\\\\\\/' to match correctly when the correct
 *  way, I thought, was '/\\\\/'. Is this a bug?    
 */
$Supple->SyntaxParser->addRule('linebreak', '/\\\\\\\/', "<br />\n", 400);   

/**
 * Postfilters
 */ 
 
//Remove the last <br />. Not totally sure why we need this right now.
$Supple->SyntaxParser->addRule('remove_last_br', '/<br \/>$/', '', 2000);

//Unhash everything. This is absolutely necessary to reverse all of the hiding done by other functions.
$Supple->SyntaxParser->addRule('unhash_all', '/'.$Supple->SyntaxParser->getTokenPattern().'/', 'unhash_call_callback', 2010, true);
function unhash_call_callback(&$matches) {
	global $Supple;
	
	return $Supple->SyntaxParser->unhash($matches[1]);
}

/**
 * Paragraph
 * (put <p> and </p>)
 */
//$Supple->SyntaxParser->addRule('paragraph', '/(.+?)\n\n/s', '<p>\1</p>'."\n\n", 300);  
//$Supple->SyntaxParser->addRule('paragraph', '/(.+)/s', 'wpautop', 300, true);  
//$Supple->SyntaxParser->addRule('paragraph', '/\n?(.+?)(?:\n\s*\n|\z)/s', "<p>$1</p>\n\n", 2020); // make paragraphs, including one at the end
$Supple->SyntaxParser->addRule('paragraph', '/\n?(.+?)(\n\s*\n|\z)/s', 'paragraph_callback', 2020, true);
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
$Supple->SyntaxParser->addRule('paragraph_newline', '/<p>(.*)<\/p>/Us', 'paragraph_newline_callback', 2030, true);
function paragraph_newline_callback(&$matches) {
	global $Supple;
	
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
	return $Supple->SyntaxParser->unhash_contents($matches[1]);
} 
function paragraph_newline_hash_callback(&$matches) {
	global $Supple;
	
	return $Supple->SyntaxParser->hash($matches[1]);
}

//When non-paragraph items are separated by more than one newline, then we
//assume that the user is intentionally inserting a newline:
//See #10: http://dev.suppletext.org/ticket/10
$Supple->SyntaxParser->addRule('intentional_newline', '/\n\n(\n+)\n/', 'intentional_newline_callback', 2050, true);
function intentional_newline_callback(&$matches) {
	return "\n".str_replace("\n", "<br />\n", $matches[1])."\n";
}


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