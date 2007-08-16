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
		$this->load->helper('admin/autoload');
		
		$this->load->library('authorization');
		if(!$this->authorization->is_logged_in())
		{
			//Set where to redirect to after login
			//$this->session->set_userdata('login_redirect_to', $this->uri->uri_string());
			
			//Not logged in, redirect to login page.
			redirect('/st-admin/users/login');
		}
	}
	
	function index()
	{
		$this->_initialize();
		
		//Otherwise, bring to user management
		$this->management();
	}
	
	function management() {
		$this->_initialize();
		$this->template->add_value('admin_page_title', 'Users &rsaquo; Management');
		$this->load->helper('string');
		
		$this->validation->set_error_delimiters('<div id="error" class="updated fade"><p>', '</p></div>');
		
		//Set validation rules
		//users is actually an array so we can't set rules for each of the elements.
		//In the future, we can loop through the number of users and set rules for
		//each one.
		$rules['users'] = 'required|callback__user_ids_check'; //We don't require this since the page can be empty.
		$rules['action'] = 'trim|required|alphanum|max_length[200]'; //Add page name check here
		$rules['new_role'] = 'trim|alphanum|max_length[100]';
		$this->validation->set_rules($rules);
		
		//Also repopulate the form
		$fields['users'] = 'User ID Selection';
		$fields['action'] = 'Update Selected';
		$fields['new_role'] = 'User Role';
		$this->validation->set_fields($fields);
		
		if($this->validation->run() === TRUE)
		{
			if($this->validation->action == 'delete')
			{
				$user_ids = $this->input->post('users');
				foreach($user_ids as $each_user_id)
				{
					$username = $this->users_model->get_username($each_user_id);
					$this->users_model->username = $username;
					$this->users_model->delete();
					unset($this->users_model->username, $username);
				}
				
				//Now display sucess message
				$this->message->set_delimiters('<div id="message" class="updated fade"><p>', '</p></div>');
				$num_users_deleted = count($user_ids);
				if($num_users_deleted > 1)
				{
					$this->message->set_text($num_users_deleted.' users deleted.');
				}
				else
				{ //One user
					$this->message->set_text($num_users_deleted.' user deleted.');
				}
			}
			else if($this->validation->action == 'promote')
			{
				//We require the new_role field
				if($this->validation->required($this->validation->new_role))
				{
					$user_ids = $this->input->post('users');
					foreach($user_ids as $each_user_id)
					{
						$username = $this->users_model->get_username($each_user_id);
						$this->users_model->username = $username;
						$this->users_model->set_value('role', $this->validation->new_role);
						unset($this->users_model->username, $username);
					}
					
				//Now display sucess message
				$this->message->set_delimiters('<div id="message" class="updated fade"><p>', '</p></div>');
				$num_users_deleted = count($user_ids);
				if($num_users_deleted > 1)
				{
					$this->message->set_text('Roles for '.$num_users_deleted.' users changed.');
				}
				else
				{ //One user
					$this->message->set_text('Role for '.$num_users_deleted.' user changed.');
				}
				}
				else
				{
					$this->validation->_error_array[] = 'No new role selected.';
					$this->validation->run();
				}
			}
			else
			{
				$this->validation->_error_array[] = 'Invalid action selected.';
				$this->validation->run();
			}

		}


		$this->load->view('admin/users-management');
	
	}
	
	function _user_ids_check($in_user_ids) {
		foreach($in_user_ids as $each_user_id)
		{
			$each_user_id = trim($each_user_id);
			//Check if uid exists by getting the username
			$username = $this->users_model->get_username($each_user_id);
			
			if(!$this->validation->numeric($each_user_id) || empty($username))
			{
				//Set error
				$this->validation->set_message('_user_ids_check', 'One or more of the user IDs you have selected are invalid.');
				return false;
			}
		}
		
		return true;
	}
	
	function addnew() {
		$this->_initialize();
		$this->template->add_value('admin_page_title', 'Users &rsaquo; Management');
		$this->load->helper('string');
		
		$this->validation->set_error_delimiters('<div id="error" class="updated fade"><p>', '</p></div>');
		
		//Set validation rules
		$rules['user_login'] = 'required|callback__user_ids_check'; //We don't require this since the page can be empty.
		$rules['email'] = 'trim|required|alphanum|max_length[200]'; //Add page name check here
		$rules['pass1'] = 'trim|alphanum|max_length[100]';
		$rules['pass2'] = '';
		$rules['role'] = '';
		$this->validation->set_rules($rules);
		
		//Also repopulate the form
		$fields['user_login'] = 'User ID Selection';
		$fields['email'] = 'Update Selected';
		$fields['pass1'] = 'User Role';
		$fields['pass1'] = '';
		$fields['role'] = '';
		$this->validation->set_fields($fields);
		
		if($this->validation->run() === TRUE)
		{
		}
		
		$this->load->view('admin/users-management');
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
	
	function logout() {
		$this->load->library('authorization');
		$this->authorization->logout();
		redirect('/st-admin/users');
	}
	
}
?>
