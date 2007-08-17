<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Message {
	//var $CI;
	
	var $delimiters = array();
	var $text = array();
	
	function Message() {
		//$this->CI =& get_instance();
		$this->delimiters['pre'] = '';
		$this->delimiters['post'] = '';
	}
	
	function set_delimiters($in_pre, $in_post) {
		$this->delimiters['pre'] = $in_pre;
		$this->delimiters['post'] = $in_post;
	}
	
	function set_text($in_text) {
		$this->text[] = $in_text;
	}
	
	function get() {
		$return_html = '';
		foreach($this->text as $each_text)
		{
			$return_html .= $this->delimiters['pre'].$each_text.$this->delimiters['post']."\n";
		}
		
		return $return_html;
	}

}


?>
