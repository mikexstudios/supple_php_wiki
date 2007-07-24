<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

//Not sure if there is a better way to load this:
include_once APPPATH.'controllers/show.php';

class Revisions extends Show {

	function Revisions()
	{
		parent::Show(); //Loads pages model
	}
	
	//Inherits _remap(), _set_page_info(...) from Show 
	
	function display($pagename) {
		$this->_set_page_info($pagename);
	
		$this->load->helper('string'); //For alternator
		
		$this->load->view('revisions');
	}
	


}
?>
