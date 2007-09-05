<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Pages extends Controller {

	function Pages()
	{
		parent::Controller();	
	}
	
	//We don't need to remap here since we are using traditional
	//URI format.

	function _initialize() {
		$this->load->helper('admin/autoload');
		
		$this->load->library('authorization');
		if(!$this->authorization->is_logged_in())
		{
			//Set where to redirect to after login
			$this->session->set_userdata('redirect_to', $this->uri->uri_string());
			
			//Not logged in, redirect to login page.
			redirect('/st-admin/users/login');
		}
	}

	function index() {
		$this->_initialize();
		
		//The general option is our default page
		$this->permissions();
	}
	
	function permissions() {
		$this->_initialize();
		$this->template->add_value('admin_page_title', 'Pages &rsaquo; Permissions'); //So header can display the correct title
		
		$this->validation->set_error_delimiters('<div id="error" class="updated fade"><p>', '</p></div>');
		
		//Set validation rules
		$rules['page_name'] = 'trim|required|max_length[200]';
		$this->validation->set_rules($rules);
		
		//Also repopulate the form
		$fields['page_name'] = 'Page Name';
		$this->validation->set_fields($fields);
		
		if($this->validation->run() === TRUE)
		{
			//Fields submitted successfully. 
			
			//Convert spaces and other special chars to wiki equivalents.
			$this->load->helper('syntax');
			$this->validation->page_name = wiki_url_title($this->validation->page_name);
			redirect('/st-admin/pages/changepermissions/'.$this->validation->page_name);
		}
		
		
		$this->load->view('pages-permissions');

	}
	
	function changepermissions() {
	
		$this->_initialize();
		$this->template->add_value('admin_page_title', 'Pages &rsaquo; Change Permissions'); //So header can display the correct title
		
		//Check for page name
		$page_name = $this->uri->segment(4);
		
		//Validate page name
		if(is_wiki_name($page_name) && does_page_exist($page_name))
		{
			$this->validation->set_error_delimiters('<div id="error" class="updated fade"><p>', '</p></div>');
			
			//Set validation rules
			$rules['read_permission'] = 'trim|max_length[200]';
			$rules['write_permission'] = 'trim|max_length[200]';
			$this->validation->set_rules($rules);
			
			//Also repopulate the form
			$fields['read_permission'] = 'Read role(s)';
			$fields['write_permission'] = 'Write role(s)';
			$this->validation->set_fields($fields);
			
			if($this->validation->run() === TRUE)
			{	
				$this->load->model('page_metadata_model');
				$this->page_metadata_model->pagename = $page_name;
				
				//We should validate that the input permissions are actually valid
				
				if(!empty($this->validation->read_permission))
				{
					$this->page_metadata_model->set_value('read_permission', $this->validation->read_permission);
				}
				else
				{
					$this->page_metadata_model->delete_key('read_permission');
				}
				
				if(!empty($this->validation->write_permission))
				{
					$this->page_metadata_model->set_value('write_permission', $this->validation->write_permission);
				}
				else
				{
					$this->page_metadata_model->delete_key('write_permission');
				}
			}	
			
			$this->template->add_value('page_name', $page_name);
			$this->load->view('pages-changepermissions');
		}
		else
		{
			$this->message->set_delimiters('<div id="error" class="updated fade"><p>', '</p></div>');
			$this->message->set_text('You entered an invalid page!');
			$this->permissions();
		}
	
	}
	
}
?>
