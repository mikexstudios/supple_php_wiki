<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Template {
	
	var $functions;
	var $values;

	function Template()
	{
		log_message('debug', "Template Class Initialized");
	}  	
	
	function add_function($tag, $in_function_name) {
		$this->functions[$tag] = $in_function_name; 
	}
	
	function add_value($tag, $in_value) {
		$this->values[$tag] = $in_value;
	}

	
	/**
	 * Executes the function or returns value associated with the tag.
	 *
	 * @access public
	 * @param string $tag Short variable-like name associated with a function such as 'getimagetag'.
	 * @param mixed $arg1,... Optional arguments that are associated with the tag.
	 * @return mixed Returns whatever the function associated to the tag returns. Could possibly be nothing.
	 */
	function execute($tag) {
		if(!empty($this->functions[$tag]))
		{
			$args = array_slice(func_get_args(), 1); //Get all arguments after the first one ($tag).
	
			//Check to see if tag exists
			if ((is_string($this->functions[$tag]) && !function_exists($this->functions[$tag])) ||
	            (is_array($this->functions[$tag]) && !method_exists($this->functions[$tag][0], $this->functions[$tag][1])))
			{
			  echo "Error: Invalid action '$tag'";
			  return '';
			}
	
			//Call associated function
			return call_user_func_array($this->functions[$tag], $args);
		}
		else if(!empty($this->values[$tag]))
		{
			return $this->values[$tag];
		}
		
		return ''; //Nothing
	}  	

}
// END Template class
?>
