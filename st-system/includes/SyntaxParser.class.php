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

	function SyntaxParser() {
		
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
		uasort($this->rules, array(&$this, 'sortRules_callback'));	
	}
	
	/**
	 * Used by sortRules(...)/usort() to compare two elements. 
	 *	 	
	 * @access private
	 */	 	
	function sortRules_callback($a, $b) {
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
