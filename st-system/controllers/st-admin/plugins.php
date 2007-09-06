<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Plugins extends Controller {

	function Plugins()
	{
		parent::Controller();	
	}
	
	//We don't need to remap here since we are using traditional
	//URI format.

	function _initialize() {
		if($this->config->item('disable_user_admin') === true)
		{
			show_404();
		}
	
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
		
		//Otherwise, bring to profile
		$this->template->add_value('admin_page_title', 'Plugins'); //So header can display the correct title
		$this->load->view('plugins.php');
	}
	
}
?>
