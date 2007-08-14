<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Users extends Controller {

	function Users()
	{
		parent::Controller();	
		$this->load->model('users_model');
	}
	
	//We don't need to remap here since we are using traditional
	//URI format.
	
	function _initialize() {
		$this->load->library('authorization');
		if(!$this->authorization->is_logged_in())
		{
			//Not logged in, redirect to login page.
			redirect('/st-admin/users/login');
		}
	}
	
	function index()
	{
		$this->_initialize();
		
		//Otherwise, bring to profile
		show_error('You are already logged in!');
	}
	
	function login() {
		//Prepare Form:
		$this->load->library('validation');
		$this->validation->set_error_delimiters('<div class="error">', '</div>');
		
		//Set validation rules
		//Note we should also validate that the name does not already exist!
		$rules['user_login'] = "trim|required|alpha_dash|min_length[4]|max_length[50]";
		$rules['user_password'] = "trim|required|max_length[100]";
		$rules['redirect_to'] = 'trim|max_length[150]|xss_filter';
		//$rules['rememberme'] = 'strcmp[true]'; //There's no way to compare input string.
		$this->validation->set_rules($rules);
		
		//Also repopulate the form
		$fields['user_login'] = 'Username'; //These names correspond to what is shown in error message.
		$fields['user_password'] = 'Password';
		$fields['rememberme'] = 'Remember me checkbox';
		$this->validation->set_fields($fields);

		if ($this->validation->run() == FALSE)
		{
			$this->load->view('admin/login');
		}
		else
		{
			//User submitted the form
			$this->load->library('authorization');
			//die($this->validation->user_login.' '.$this->validation->user_password);
			if($this->authorization->validate($this->validation->user_login, $this->validation->user_password))
			{
				//User was authenticated
				$this->authorization->set_logged_in($this->validation->user_login);
				
				//Redirect to page where we came from
				$redirect_to = $this->session->userdata('login_redirect_to');
				$this->session->set_userdata('login_redirect_to', ''); //Clear the redirect to.
				redirect($redirect_to);
			}
			
			//User was not authenticated! Hack to get custom error message:
			$this->validation->_error_array[] = 'Incorrect Username or Password.';
			$this->validation->run();
			$this->load->view('admin/login');
		}
	}
	
}
?>
