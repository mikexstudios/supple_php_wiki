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
 * (But below implementation could be a little slow:)
 */ 
$Supple->SyntaxParser->addRule('trim_spaces', '/(.*)/m', 'trim_spaces_callback', 30, true);
function trim_spaces_callback(&$matches) {
	return trim($matches[1]);
} 

/**
 * Preformatted
 * {{{
 * text in here
 * }}}   
 * is not wiki formatted. 
 * (Note the U is for ungreedy. s is for the . to take into account all
 *  characters including newlines.)  
 */
 /*
$Supple->SyntaxParser->addRule('preformatted', '/\n{{{\n(.*)\n}}}\n/Us', 'preformatted_callback', 100, true);
$preformatted_storage = array();
$preformatted_storage_count = 0;
function preformatted_callback(&$matches) {
	//Currently taken from Preformatted.php from Creole of Pear::Text_wiki
	//@author Tomaiuolo Michele <tomamic@yahoo.it>
	
	global $preformatted_storage, $preformatted_storage_count;
	
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
	
	//We want to remove the matched text and place it in an array. In place of
	//the text, we put a delimiter. Later, we have a post_preformatted_callback
	//which will insert the text back in.
	$preformatted_storage[$preformatted_storage_count] = $matches[1];
	$preformatted_storage_count++; //Increment for the next time preformatted is called.
	return "\xFF".$preformatted_storage_count."\xFF"; //The token
}  
*/

//Creole 1.0 defines the monospace/tt as part of preformatted. We match {{{ }}}.
//NOTE: This should be checked VERY carefully against the Creole specification.
//      I have a feeling that this is currently wrong.
$Supple->SyntaxParser->addRule('tt', '/{{{({*?.*}*?)}}}/U', '<tt>\1</tt>', 110);

/**
 * Line Breaks
 * \\ to \n
 * (The problem with this current implementation is that if we have \\\
 *  that will translate to \n\ (where \\n might be preferred). We have to
 *  consult Creole more carefully.)   
 */
$Supple->SyntaxParser->addRule('linebreak', '/\\\\/', "\n", 120);  


//Won't implement Raw, Footnote

//Won't implement Table for now. Should also have option of tables being
//done in HTML.

//Need to include URL stuff here.

/**
 * Image (inline)
 * {{myimage.png|text}} -> <img src="myimage.png" alt="text"> 
 */ 
$Supple->SyntaxParser->addRule('inlineimage', '/{{(.*)(\|(.*))?}}/U', '<img src="\1" alt="\2" />', 180); 

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
  
  return '<h'.$level.'>'.$text.'</h'.$level.'>'."\n\n"; //Maybe we don't need the newlines
}


/**
 * Horizontal Rule
 * ---- -> <hr />
 */
$Supple->SyntaxParser->addRule('horizontalrule', '/^[-]{4,}$/m', '<hr />'."\n", 200);


//Skip Lists, for now

/**
 * Emphasis/Italics
 * // // -> <em> </em>
 * (Double check regex) 
 */
$Supple->SyntaxParser->addRule('emphasis', '/\/\/(.+?)\/\//', '<em>\1</em>', 250);

/**
 * Strong/Bold
 * ** ** -> <strong> </strong>
 */
$Supple->SyntaxParser->addRule('strong', '/\*\*(.*?)\*\*/', '<strong>\1</strong>', 260);


/**
 * Postfilters
 */ 
//Remove the last <br />
$Supple->SyntaxParser->addRule('remove_last_br', '/<br \/>$/', '', 2000);

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