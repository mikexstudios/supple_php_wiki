<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Options extends Controller {

	function Options()
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

	function index()
	{
		$this->_initialize();
		
		//The general option is our default page
		$this->general();
	}
	
	function general()
	{
		$this->_initialize();
		$this->template->add_value('admin_page_title', 'Options &rsaquo; General'); //So header can display the correct title
		
		$this->validation->set_error_delimiters('<div id="error" class="updated fade"><p>', '</p></div>');
		
		//Set validation rules
		//Note we should also validate that the name does not already exist!
		$rules['site_name'] = 'trim|max_length[300]|xss_clean'; //We don't require this since the page can be empty.
		$rules['root_page'] = 'trim|required|max_length[200]'; //Add page name check here
		$this->validation->set_rules($rules);
		
		//Also repopulate the form
		$fields['site_name'] = 'Wiki title';
		$fields['root_page'] = 'Default wiki page';
		$this->validation->set_fields($fields);
		
		if ($this->validation->run() == FALSE)
		{
			$this->load->view('options-general');
		}
		else
		{
			//Fields submitted successfully. Update settings
			foreach(array_keys($fields) as $option_key)
			{
				$this->settings->set($option_key, $this->validation->$option_key);
			}
			
			//Set our custom message:
			$this->message->set_delimiters('<div id="message" class="updated fade"><p>', '</p></div>');
			$this->message->set_text('Options saved.');
			$this->load->view('options-general');
		}
	}
	
	function permissions()
	{
		$this->_initialize();
		$this->template->add_value('admin_page_title', 'Options &rsaquo; Permissions'); //So header can display the correct title
		
		$this->validation->set_error_delimiters('<div id="error" class="updated fade"><p>', '</p></div>');
		
		//Set validation rules
		$rules['default_read_permission'] = 'trim|required|max_length[200]';
		$rules['default_write_permission'] = 'trim|required|max_length[200]';
		$this->validation->set_rules($rules);
		
		//Also repopulate the form
		$fields['default_read_permission'] = 'Default read role(s)';
		$fields['default_write_permission'] = 'Default write role(s)';
		$this->validation->set_fields($fields);
		
		if ($this->validation->run() == FALSE)
		{
			$this->load->view('options-permissions');
		}
		else
		{
			//Fields submitted successfully. Update settings
			foreach(array_keys($fields) as $option_key)
			{
				$this->settings->set($option_key, $this->validation->$option_key);
			}
			
			//Set our custom message:
			$this->message->set_delimiters('<div id="message" class="updated fade"><p>', '</p></div>');
			$this->message->set_text('Options saved.');
			$this->load->view('options-permissions');
		}
	}
	
}
?>
