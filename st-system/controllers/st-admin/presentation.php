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
			$this->session->set_userdata('login_redirect_to', $this->uri->uri_string());
			
			//Not logged in, redirect to login page.
			redirect('/st-admin/users/login');
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
		
		
		$this->load->view('admin/presentation-themes');
	}
	
}
?>
