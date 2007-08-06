<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * For converting Wiki syntax into XHTML syntax.
 * 
 * @author Douglas S. Blank (Edventure Course Management System)
 * @author Michael Huynh (http://www.mikexstudios.com)
 * @package suppleText
 * @version $Id:$
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * 
 */
 
class SyntaxParser {
	var $CI;
	var $text;
	var $blockdefs = array();
	var $inlinedefs = array();
	var $delimiter = "\xFF"; //Maybe something else might work better to allow for non UTF-8
	var $token_pattern;
	var $hashed_text = array();
	var $syntax_path, $syntax_path_loaded;

	function SyntaxParser() {
		$this->CI =& get_instance();
		
		$this->CI->load->helper('syntax');
		
		$this->token_pattern = $this->delimiter.'(?:[a-z0-9]+)'.$this->delimiter;
		log_message('debug', "SyntaxParser Class Initialized");
	}	
	
	function setSyntaxPath($in_path) {
		$this->syntax_path = $in_path;
	}
	
	function loadSyntax($in_path = '') {
		if(empty($in_path))
		{
			$in_path = $this->syntax_path;
		}
		
		//Check if we already loaded the syntax path
		if($this->syntax_path_loaded[md5($in_path)])
		{
			return;
		}
		
		//load_files_in_directory($in_path);
		include_once $this->syntax_path.'/test.php';
		$this->syntax_path_loaded[md5($in_path)] = true; //Set so we know it has already been loaded
	}
	
	function setText($in_text) {
		$this->text = $in_text;
	}
	
	function getText() {
		return $this->text;
	}
	
	/**
	 * Defines with regex what constitutes a block
	 */	 	
	function add_block_definition($in_tag, $in_pattern, $in_callback, $in_priority, $is_callback=true) {
		//It might be a good idea to cast to specific data formats here.
		//Also add check for adding a tag that already exists.
	    $this->blockdefs[$in_tag] = array('pattern' => $in_pattern, 
														 'replacement' => $in_callback, 
														 'priority' => $in_priority, 
														 'is_callback' => $is_callback);
	}
	
	/**
	 * Rules get applied inside blocks
	 */	 	
	function add_inline_definition($in_tag, $in_pattern, $in_replacement, $in_priority, $is_callback=false) {
		//It might be a good idea to cast to specific data formats here.
		//Also add check for adding a tag that already exists.
	    $this->inlinedefs[$in_tag] = array('pattern' => $in_pattern, 
														 'replacement' => $in_replacement, 
														 'priority' => $in_priority,
														 'is_callback' => $is_callback);
	}
	
	//--------------------------------

	function applyBlockDef($in_tag, $in_text) {
		//Check for callback
		if($this->blockdefs[$in_tag]['is_callback']==true)
		{
			//Maybe we can add a doesfunctionexist check here.
			return preg_replace_callback($this->blockdefs[$in_tag]['pattern'], $this->blockdefs[$in_tag]['replacement'] , $in_text);
		}
		else if($this->blockdefs[$in_tag]['is_callback']==false) //We could have just done an else too.
		{
			return preg_replace($this->blockdefs[$in_tag]['pattern'], $this->blockdefs[$in_tag]['replacement'] , $in_text);
		}
	}

	//We choose this design decision for speed. The other method would be to manually
	function applyInlineDef($in_tag, $in_text) {
		//Check for callback
		if($this->inlinedefs[$in_tag]['is_callback']==true)
		{
			//Maybe we can add a doesfunctionexist check here.
			return preg_replace_callback($this->inlinedefs[$in_tag]['pattern'], $this->inlinedefs[$in_tag]['replacement'] , $in_text);
		}
		else if($this->inlinedefs[$in_tag]['is_callback']==false) //We could have just done an else too.
		{
			return preg_replace($this->inlinedefs[$in_tag]['pattern'], $this->inlinedefs[$in_tag]['replacement'] , $in_text);
		}
	}
	
	/**
	 * This and sortRules can be abstracted in the future
	 */	 	
	function sortBlockDefs() {
		uasort($this->blockdefs, array(&$this, '_priority_sort_callback'));	
	}
	
	/**
	 * @access private
	 */	 	
	function sortInlineDefs() { //Currently only ascending
		//Because we have a multi-dimensional array, we can use array_multisort or
		//uasort. uasort is more elegant since we don't have to create "columns" for 
		//array_multisort. We use uasort instead of usort in order to keep the "keys"
		//associated to the arrays instead of having them replaced by number indicies.
		uasort($this->inlinedefs, array(&$this, '_priority_sort_callback'));	
	}
	
	function sort_by_priority(&$in) { //Currently only ascending
		//Because we have a multi-dimensional array, we can use array_multisort or
		//uasort. uasort is more elegant since we don't have to create "columns" for 
		//array_multisort. We use uasort instead of usort in order to keep the "keys"
		//associated to the arrays instead of having them replaced by number indicies.
		uasort($in, array(&$this, '_priority_sort_callback'));	
	}
	
	/**
	 * Used by sortRules(...)/usort() to compare two elements. 
	 *	 	
	 * @access private
	 */	 	
	function _priority_sort_callback($a, $b) {
		if($a['priority'] == $b['priority'])
			{ return 0; }
		else if($a['priority'] < $b['priority'])
			{ return -1; }
		else //a > b
			{ return 1; }			
	}
	
	function applyAll() {
		$this->text = $this->applyAllBlockDefs($this->text);
		$this->text = $this->unhash_contents($this->text);
	}
	
	function applyAllBlockDefs($in_text) {
		//Sort blocks by priority
		$this->sort_by_priority($this->blockdefs);
		
		foreach(array_keys($this->blockdefs) as $tag)
		{			
			$in_text = $this->applyBlockDef($tag, $in_text);
		}
		
		return $in_text;
	}
	
	//Sort by priority. Then apply each.
	function applyAllInlineDefs($in_text) {
		$this->sort_by_priority($this->inlinedefs); //Sort by priority first.

		//According to comments in php manual:
		//http://us2.php.net/manual/en/control-structures.foreach.php#54311
		//This is faster than a foreach(... as $key => $value).
		foreach(array_keys($this->inlinedefs) as $tag)
		{			
			$in_text = $this->applyInlineDef($tag, $in_text);
		}
		
		return $in_text;
	}

	/**
	 * @author Michel Fortin
	 */	 	
	function hash($text) {
		# Swap back any tag hash found in $text so we do not have to `unhash`
		# multiple times at the end.
		$text = $this->unhash_contents($text);
		
		# Then hash the block.
		$key = md5($text);
		//die('orig: '.$key);
		$this->hashed_text[$key] = $text;

		return $this->delimiter.$key.$this->delimiter; # String that will replace the tag.
	}

	function unhash($key) {
	
		if(!empty($this->hashed_text[$key]))
		{
			return $this->hashed_text[$key];			
		}
		
		return $key;

	}
	
	function unhash_contents($text) {
		return preg_replace_callback('/('.$this->token_pattern.')/', array(&$this, '_unhash_contents_callback'), $text);
		
		//The below code isn't very good because it doesn't get rid of the \n\n's
		//around the hash key.
		//return str_replace(array_keys($this->hashed_text), 
		//				   array_values($this->hashed_text), $text);
	}
	
	/**
	 * @access private
	 */	 	
	function _unhash_contents_callback(&$matches) {
		$matches[1] = trim($matches[1], $this->delimiter);
		return $this->unhash($matches[1]);
	}
	
	function getTokenPattern() {
		return $this->token_pattern;
	}
	

	//Actions: <<<actionname parameters parameter2>>>
	function doAction($in_action, $in_args='')
	{
		global $action, $args; //Set these to global so that the included file can use them.
		$action = $in_action;
		$args = $in_args;
		
		return include_buffered(base_path('st-system/actions/'.$action.'.php'));
	}

}


/*
//Testing
$x = new SyntaxParser();
$x->setText('The dog //ate// the moon.'."\n and other");
$x->addRule('emphasis', '/\/\/(.*?)\/\//', '<em>\1</em>\n'."\n", 10);
#$x->applyRule('emphasis');
$x->addRule('newline', '/\n/', 'newline', 30);
#$x->applyRule('newline');
$x->addRule('test', '/other/', 'test', 20);
#$x->applyRule('test');
echo $x->getText();
echo "<br /><br />";
$x->applyAll();
echo $x->getText();
//$x->sortRules();
*/

?>
