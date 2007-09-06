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
		
		//Check to see if user has permission to read this page
		$page_read_roles = get_page_read_roles($pagename);
		if(does_user_have_permission($page_read_roles))
		{
			if(does_current_page_exist())
			{
				$this->load->view('revisions');
			}
			else
			{
				redirect($pagename);
			}
		}
		else
		{
			$this->pages_model->page['body'] = '<p>You do not have the permission to view this page.</p>';
			$this->load->view('show');
		}
	}
	


}
?>
