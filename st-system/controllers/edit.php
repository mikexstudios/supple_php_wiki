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
			
			if ($this->validation->run() == FALSE)
			{
				$this->load->view('edit');
			}
			else
			{
				//Check for storage, otherwise, assume it is preview
				if(strcmp($this->validation->submit, 'Store')===0) //Hm, hard-coding these forces templates to use these values?
				{
					//Set author
					if($this->authorization->is_logged_in())
					{
						$author = $this->session->userdata('username');
					}
					else
					{
						$author = $this->input->ip_address();
					}
												
					$this->load->library('syntaxparser');
					$this->syntaxparser->setSyntaxPath(base_path('/st-system/formatters/'));
					$this->syntaxparser->loadSyntax();
					$preprocessed_text = $this->syntaxparser->apply_all_preprocessors($this->validation->body);
					
					//The form is successful! This is where we make changes.
					$this->pages_model->pagename = $pagename;
					$this->pages_model->time = now(); //now() uses the date helper
					$this->pages_model->author = $author; //For now
					//For some reason putting hsc_secure in form processing (above) doesn't work
					//since the escaped characters are "unescaped" again!
					$this->pages_model->note = $this->validation->hsc_secure($this->validation->note);
					$this->pages_model->body = $preprocessed_text;
					$this->pages_model->insert();
					
					redirect($pagename);
				}
				else if(strcmp($this->validation->submit, 'Re-edit')===0)
				{
					//die('here');
					$this->load->view('edit');
				}
				else
				{
					//Otherwise, we preview
					$this->pages_model->page['body'] = format_text($this->validation->body, true);
					$this->load->view('preview');
				}
				
			}
		}
	}

}
?>
