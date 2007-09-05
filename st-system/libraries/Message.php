<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Message {
	var $CI;
	
	var $delimiters = array();
	var $text = array();
	var $return_html = '';
	
	function Message() {
		$this->CI =& get_instance();
		
		$this->delimiters['pre'] = '';
		$this->delimiters['post'] = ''; 
		/*
		//Set default delimiters
		$this->delimiters['pre'] = $this->CI->config->item('default_message_delimiter_pre');
		$this->delimiters['post'] = $this->CI->config->item('default_message_delimiter_post');
		*/
	}
	
	function set_delimiters($in_pre, $in_post) {
		$this->delimiters['pre'] = $in_pre;
		$this->delimiters['post'] = $in_post;
	}
	
	function set_text($in_text) {
		$this->return_html .= $this->delimiters['pre'].$in_text.$this->delimiters['post']."\n";
	}
	
	function get() {
		//Check for any session messages
		$this->set_session_message();
	
		return $this->return_html;
	}
	
	function set_session_message() {
		/* This is kind of an ugly hack right now. We should make this more
		   elegant later */
	
		$pre = $this->CI->session->ro_userdata('message_delimiter_pre');
		$post = $this->CI->session->ro_userdata('message_delimiter_post');
		$session_message = $this->CI->session->ro_userdata('message');
		if(!empty($session_message))
		{
			$this->set_delimiters($pre, $post);
			$this->set_text($session_message);
		}
		
		
	}

}


?>
