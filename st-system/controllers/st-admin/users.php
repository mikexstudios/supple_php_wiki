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
		$rules['action'] = 'required|trim|alpha_dash|max_length[200]'; //Add page name check here
		$rules['new_role'] = 'trim|alpha_dash|max_length[100]';
		$rules['bulkupdate'] = 'required'; //Submit button
		$this->validation->set_rules($rules);
		
		//Also repopulate the form
		$fields['users'] = 'User ID Selection';
		$fields['action'] = 'Update Selected';
		$fields['new_role'] = 'User Role';
		$fields['bulkupdate'] = 'Bulk Update';
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
		$this->template->add_value('admin_page_title', 'Users &rsaquo; Add New User');
		$this->load->helper('string');
		
		$this->validation->set_error_delimiters('<div id="error" class="updated fade"><p>', '</p></div>');
		
		//Set validation rules
		$rules['user_login'] = 'required|trim|min_length[4]|max_length[100]|alpha_dash|callback__user_exist_check'; //We don't require this since the page can be empty.
		$rules['email'] = 'required|trim|max_length[300]|valid_email'; //Add page name check here
		$rules['pass1'] = 'required|trim|max_length[250]|matches[pass2]';
		$rules['pass2'] = 'required|trim|max_length[250]|matches[pass1]';
		$rules['role'] = 'required|trim|max_length[100]|alpha_dash';
		$rules['adduser'] = 'required'; //The submit button
		$this->validation->set_rules($rules);
		
		//Also repopulate the form
		$fields['user_login'] = 'Username';
		$fields['email'] = 'E-mail';
		$fields['pass1'] = 'Password';
		$fields['pass2'] = 'Password Again';
		$fields['role'] = 'Role';
		$fields['adduser'] = 'Add User Submit';
		$this->validation->set_fields($fields);
		
		if($this->validation->run() === TRUE)
		{
			//Now we add the user
			$this->users_model->username = $this->validation->user_login;
			$this->users_model->set_value('uid', $this->users_model->get_next_uid());
				$this->load->library('encrypt');
				$hashed_password = $this->encrypt->sha1($this->config->item('encryption_salt').$this->validation->pass1);			
			$this->users_model->set_value('password', $hashed_password);
			$this->users_model->set_value('email', $this->validation->email);
			$this->users_model->set_value('role', $this->validation->role);
			
			//Now display sucess message
			$this->message->set_delimiters('<div id="message" class="updated fade"><p>', '</p></div>');
			$this->message->set_text('New user created: '.$this->validation->user_login);
			
			//Now we empty some of the form fields
			$this->validation->user_login = '';
			$this->validation->email = '';
		}
		
		$this->load->view('admin/users-addnew');
	}
	
	function _user_exist_check($in_username) {
		$this->users_model->username = $in_username;
		$uid_temp = $this->users_model->get_value('uid');

		if(!empty($uid_temp))
		{
			//Set error
			$this->validation->set_message('_user_exist_check', 'The user you are trying to add already exists.');
			return false; //User exists!
		}
		
		return true;
	}
	
	function profile() {
		$this->_initialize();
		$this->template->add_value('admin_page_title', 'Users &rsaquo; Profile');
		
		$this->validation->set_error_delimiters('<div id="error" class="updated fade"><p>', '</p></div>');
		
		//Set validation rules
		$rules['email'] = 'required|trim|max_length[300]|valid_email'; //Add page name check here
		$rules['pass1'] = 'trim|max_length[250]|matches[pass2]';
		$rules['pass2'] = 'trim|max_length[250]|matches[pass1]';
		$rules['updateprofile'] = 'required'; //The submit button
		$this->validation->set_rules($rules);
		
		//Also repopulate the form
		$fields['email'] = 'E-mail';
		$fields['pass1'] = 'Password';
		$fields['pass2'] = 'Password Again';
		$fields['updateprofile'] = 'Update Profile Submit';
		$this->validation->set_fields($fields);
		
		if($this->validation->run() === TRUE)
		{
			//Now we add the user
			$this->users_model->username = get_logged_in_username();
			//Do we change the password?
			if(!empty($this->validation->pass1))
			{
				$this->load->library('encrypt');
				$hashed_password = $this->encrypt->sha1($this->config->item('encryption_salt').$this->validation->pass1);
				$this->users_model->set_value('password', $hashed_password);
			}			
			
			$this->users_model->set_value('email', $this->validation->email);
			
			//Now display sucess message
			$this->message->set_delimiters('<div id="message" class="updated fade"><p>', '</p></div>');
			$this->message->set_text('Profile updated.');
		}
		
		$this->load->view('admin/users-profile');
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
	
	function register() {
		//We do not do the initialization stuff since this doesn't require admin-level
		//access
		
		$this->load->helper('admin/autoload');
		
		$this->load->library('authorization');
		if($this->authorization->is_logged_in())
		{
			//redirect('/st-admin/users');
		}
		
		$this->load->library('validation');
		$this->validation->set_error_delimiters('<div class="error">', '</div>');
		
		//Set validation rules
		//Note we should also validate that the name does not already exist!
		$rules['user_login'] = 'required|trim|alpha_dash|min_length[4]|max_length[100]|callback__user_exist_check';
		$rules['user_email'] = 'required|trim|max_length[300]|valid_email';
		$this->validation->set_rules($rules);
		
		//Also repopulate the form
		$fields['user_login'] = 'Username'; //These names correspond to what is shown in error message.
		$fields['user_email'] = 'Email';
		$this->validation->set_fields($fields);

		if ($this->validation->run() === TRUE)
		{					
			$this->load->library('email');
			
			//Get base url of site
			if(preg_match('%^\S+://(\S+\.\S+?)/.*$%', base_url(), $matches))
			{
				$site_domain_name = $matches[1];
			}
			else
			{
				$site_domain_name = 'example.com';
			}
			
			//Generate random password
			$this->load->helper('string');
			$random_password = random_string('alnum', 6);
			$this->load->library('encrypt');
			$hashed_password = $this->encrypt->sha1($this->config->item('encryption_salt').$random_password);				
	
			$this->email->from('noreply@'.$site_domain_name, $this->settings->get('site_name'));
			$this->email->to($this->validation->user_email);
			$this->email->subject('Your username and password');
			$message = '
Thanks for registering on '.$this->settings->get('site_name').'. Here is your login info:
Username: '.$this->validation->user_login.'
Password: '.$random_password.'
(You can change your password after you login.)

You can login here: '.construct_admin_url('users/login')."\n\n";
			$this->email->message($message);
			if($this->email->send() === TRUE)
			{
				//Now we add the user
				$this->users_model->username = $this->validation->user_login;
				$this->users_model->set_value('uid', $this->users_model->get_next_uid());		
				$this->users_model->set_value('password', $hashed_password);
				$this->users_model->set_value('email', $this->validation->user_email);
				$this->users_model->set_value('role', 'Registered');
				
				//Now display sucess message
				$this->message->set_delimiters('<div id="message" class="updated fade"><p><strong>', '</strong></p></div>');
				$this->message->set_text('Registration complete. Please check your email.');
			}
			else
			{
				//Now display sucess message
				$this->message->set_delimiters('<div id="error" class="updated fade"><p><strong>', '</strong></p></div>');
				$this->message->set_text('Registration failed. Your registration email was unable to be sent. Please contact the site owner for assistance.');
			}	
		}
		
		$this->load->view('admin/users-register');

	}
	
}
?>
