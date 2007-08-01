<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class MY_Input extends CI_Input {

	function MY_Input() {
		parent::CI_Input();
	}

	/**
	 * We override this and add the @ character in the input
	 * since some cookies may use the user@site format.
	 */
	function _clean_input_keys($str)
	{	
		 if ( ! preg_match("/^[a-z0-9:@_\/-]+$/i", $str)) //Added @
		 {
			exit('Disallowed Key Characters.');
		 }

		return $str;
	}
	
}

?>
