<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Presentation extends Controller {

	function Presentation()
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
		
		//Check if the user has the permissions to access this page
		if(!does_user_have_permission(get_user_role())) //Defaults to Administrator
		{
			show_404();
		}
	}

	function index()
	{
		$this->_initialize();
		
		//The general option is our default page
		$this->themes();
	}
	
	function themes()
	{
		$this->_initialize();
		$this->template->add_value('admin_page_title', 'Presentation &rsaquo; Themes'); //So header can display the correct title
		
		//Check for user input
		$action = $this->uri->segment(4);
		$selected_theme = $this->uri->segment(5);
		if($action == 'activate' && is_refer_from_this_page())
		{
			//Validate data, make changes
			$selected_theme = trim($selected_theme);
			if(preg_match('/[A-Za-z0-9-_\.\[\]\(\) ]/', $selected_theme) && does_theme_exist($selected_theme)) //Some characters we check here will never be reached because of URL disallowed characters
			{
				$this->settings->set('use_theme', $selected_theme);
				
				$this->message->set_delimiters('<div id="message" class="updated fade"><p>', '</p></div>');
				$this->message->set_text('New theme activated. <a href="'.base_url().'">View site &raquo;</a>');
			}
			else
			{
				//Set our custom message:
				$this->message->set_delimiters('<div id="error" class="updated fade"><p>', '</p></div>');
				$this->message->set_text('Invalid theme selected!');
			}
		}

		//No matter if we have input or not, we always show the page
		$this->load->view('presentation-themes');
	}
	
}
?>
