<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

//Not sure if there is a better way to load this:
include_once APPPATH.'controllers/show.php';

class Edit extends Show {

	function Edit()
	{
		parent::Show(); //Loads pages model
	}
	
	//Inherits _remap(), _set_page_info(...) from Show 
	
	function _prepForm() {
		//$this->load->library('validation');
		$this->validation->set_error_delimiters('<div class="error">', '</div>');
		
		//Set validation rules
		//Note we should also validate that the name does not already exist!
		$rules['body'] = '';
		$rules['note'] = 'trim|max_length[500]|xss_clean'; //We can't do alpha_dash since we allow spaces!
		$this->validation->set_rules($rules);
		
		//Also repopulate the form
		$fields['body'] = 'Page Content';
		$fields['note'] = 'Edit Note'; //These names correspond to what is shown in error message.
		$this->validation->set_fields($fields);
	}
	
	function display($pagename) {
		$this->_set_page_info($pagename);
		
		$this->_prepForm();
		
		if ($this->validation->run() == FALSE)
		{
			$this->load->view('edit');
		}
		else
		{
			$this->load->helper('date');
		
			//The form is successful! This is where we make changes.
			$this->pages_model->copy_to_archives();
			$this->pages_model->delete();
			//Now create new record
			$this->pages_model->pagename = $pagename;
			$this->pages_model->time = now(); //now() uses the date helper
			$this->pages_model->author = 'Anonymous'; //For now
			$this->pages_model->note = $this->validation->note;
			$this->pages_model->body = $this->validation->body;
			$this->pages_model->insert();
			
			redirect($pagename);
		}
	}

}
?>
