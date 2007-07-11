<?php
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
	var $text;
	var $rules;
	var $delimiter = "\xFF";
	var $token_pattern;
	var $hashed_text = array();
	var $syntax_path, $syntax_path_loaded;

	function SyntaxParser() {
		$this->token_pattern = $this->delimiter.'([a-z0-9]+)'.$this->delimiter;
	}	
	
	function setSyntaxPath($in_path) {
		$this->syntax_path = $in_path;
	}
	
	function loadSyntax($in_path = '') {
		global $Supple;
		
		if(empty($in_path))
		{
			$in_path = $this->syntax_path;
		}
		
		//Check if we already loaded the syntax path
		if($this->syntax_path_loaded[md5($in_path)])
		{
			return;
		}
		
		$Supple->loadFilesInDirectory($in_path);
		$this->syntax_path_loaded[md5($in_path)] = true; //Set so we know it has already been loaded
	}
	
	function setText($in_text) {
		$this->text = $in_text;
	}
	
	function getText() {
		return $this->text;
	}
	
	function addRule($in_tag, $in_pattern, $in_replacement, $in_priority, $is_callback=false) {
		//It might be a good idea to cast to specific data formats here.
		//Also add check for adding a tag that already exists.
    $this->rules[$in_tag] = array('pattern' => $in_pattern, 
													 'replacement' => $in_replacement, 
													 'priority' => $in_priority,
													 'is_callback' => $is_callback);
	}
	
	//We choose this design decision for speed. The other method would be to manually
	function applyRule($in_tag) {
		//Check for callback
		if($this->rules[$in_tag]['is_callback']==true)
		{
			//Maybe we can add a doesfunctionexist check here.
			$this->text = preg_replace_callback($this->rules[$in_tag]['pattern'], $this->rules[$in_tag]['replacement'] , $this->text);
		}
		else if($this->rules[$in_tag]['is_callback']==false) //We could have just done an else too.
		{
			$this->text = preg_replace($this->rules[$in_tag]['pattern'], $this->rules[$in_tag]['replacement'] , $this->text);
		}
	}
	
	/**
	 * @access private
	 */	 	
	function sortRules() { //Currently only ascending
		//Because we have a multi-dimensional array, we can use array_multisort or
		//uasort. uasort is more elegant since we don't have to create "columns" for 
		//array_multisort. We use uasort instead of usort in order to keep the "keys"
		//associated to the arrays instead of having them replaced by number indicies.
		uasort($this->rules, array(&$this, '_sortRules_callback'));	
	}
	
	/**
	 * Used by sortRules(...)/usort() to compare two elements. 
	 *	 	
	 * @access private
	 */	 	
	function _sortRules_callback($a, $b) {
		if($a['priority'] == $b['priority'])
			{ return 0; }
		else if($a['priority'] < $b['priority'])
			{ return -1; }
		else //a > b
			{ return 1; }			
	}
	
	//Sort by priority. Then apply each.
	function applyAll() {
		$this->sortRules(); //Sort by priority first.

		//According to comments in php manual:
		//http://us2.php.net/manual/en/control-structures.foreach.php#54311
		//This is faster than a foreach(... as $key => $value).
		foreach(array_keys($this->rules) as $tag)
		{			
			$this->applyRule($tag);
		}
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
		return preg_replace_callback('/'.$this->token_pattern.'/', array(&$this, '_unhash_contents_callback'), $text);
		
		//The below code isn't very good because it doesn't get rid of the \n\n's
		//around the hash key.
		//return str_replace(array_keys($this->hashed_text), 
		//				   array_values($this->hashed_text), $text);
	}
	
	/**
	 * @access private
	 */	 	
	function _unhash_contents_callback(&$matches) {
		return $this->unhash($matches[1]);
	}
	
	function getTokenPattern() {
		return $this->token_pattern;
	}
	

	//Actions: <<<actionname parameters parameter2>>>
	function doSnippet($in_action, $in_args='')
	{
		global $action, $args; //Set these to global so that the included file can use them.
		$action = $in_action;
		$args = $in_args;
		
		return include_buffered(ABSPATH.'/st-system/actions/'.$action.'.php');
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
