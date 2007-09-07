<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

//Not sure if there is a better way to load this:
include_once APPPATH.'controllers/show.php';

class Attachments extends Show {

	function Attachments()
	{
		parent::Show(); //Loads pages model
	}
	
	//Inherits _remap(), _set_page_info(...) from Show 
	
	function _prepForm() {
		//$this->load->library('validation');
		$this->validation->set_error_delimiters('<div class="error">', '</div>');
		
		//Set validation rules
		//Note we should also validate that the name does not already exist!
		$rules['body'] = ''; //We don't require this since the page can be empty.
		$rules['note'] = 'trim|max_length[500]|xss_clean'; //We can't do alpha_dash since we allow spaces!
		$fules['submit'] = 'required|trim|max_length[20]';
		$this->validation->set_rules($rules);
		
		//Also repopulate the form
		$fields['body'] = 'Page Content';
		$fields['note'] = 'Edit Note'; //These names correspond to what is shown in error message.
		$fields['submit'] = 'Submit/Preview Button'; //We need to set some name so that validation stores the value
		$this->validation->set_fields($fields);
	}
	
	function display($pagename) {
		$this->_set_page_info($pagename);
		
		if(does_current_page_exist() == false)
		{
			redirect($pagename);
		}
		
		//Check to see if user has permission to write to this page
		$page_read_roles = get_page_write_roles($pagename);
		if(does_user_have_permission($page_read_roles) === FALSE)
		{
			$this->pages_model->page['body'] = '<p>You do not have the permission to edit this page.</p>';
			$this->load->view('show');
		}
		else
		{
			$this->_prepForm();
			
			if ($this->validation->run() == TRUE)
			{
			
			}
			
			$this->load->view('attachments');
		}
	}

}
?>
