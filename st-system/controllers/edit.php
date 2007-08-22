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
		
		$this->_prepForm();
		
		if ($this->validation->run() == FALSE)
		{
			//Get page metadata from database and insert text at top of page
			$page_metadata_roles = get_page_metadata_access_roles();
			$user_role = get_user_role();
			if(does_user_have_permission($user_role, $page_metadata_roles))
			{
				$this->load->model('page_metadata_model');
				$this->page_metadata_model->pagename = $pagename;
				$page_metadata = $this->page_metadata_model->get_all();
				
				$metadata_syntax = '';
				foreach($page_metadata as $metadata_key => $metadata_value)
				{
					$metadata_syntax .= '@@'.$metadata_key.' = '.$metadata_value.'@@'."\n";
				}
				$this->pages_model->page['body'] = $metadata_syntax."\n".$this->pages_model->page['body'];
			}
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
				
				/**
				 * Do preprocesser parsing. We only do this if the user has permission
				 */				 
				$page_metadata_roles = get_page_metadata_access_roles();
				$user_role = get_user_role();
				if(does_user_have_permission($user_role, $page_metadata_roles))
				{
					//We clear existing page metadata values
					$this->load->model('page_metadata_model');
					$this->page_metadata_model->pagename = $pagename;
					$this->page_metadata_model->delete_all();
				}	
						
				$this->load->library('syntaxparser');
				$this->syntaxparser->setSyntaxPath(base_path('/st-system/formatters/'));
				$this->syntaxparser->loadSyntax();
				$preprocessed_text = $this->syntaxparser->apply_all_preprocessors($this->validation->body);
				
				//The form is successful! This is where we make changes.
				//Now create new record
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
?>
