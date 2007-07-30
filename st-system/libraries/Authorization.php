<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @author Zelaza (http://codeigniter.com/forums/viewthread/54283/#265761) 
 * @author Michael Huynh (mike@mikexstudios.com) 
 */ 
class Authorization {
	var $CI;
	
	function Authorization() {
		$this->CI =& get_instance();
		
		$this->CI->load->model('Users_model', 'users_model');
		
		log_message('debug', "Authorization Class Initialized");
	}
	
	function exists($in_username) {
		$this->CI->users_model->username = $in_username;
		//We just check for the existance of the username-password pair since
		//that is the only necessary entry.
		$temp_password = $this->CI->users_model->get_value('password');
		if(!empty($temp_password))
		{
			return true;
		}
		
		return false; //User doesn't exist
	}
	
	function validate($in_username, $in_password) {
		if(!$this->exists($in_username)) 
		{
			return false;
		}
		
		//Hash the input password
		$this->CI->load->library('encrypt');
		$input_hashed_password = $this->CI->encrypt->sha1($this->CI->config->item('encryption_salt').$in_password);
		
		//Get real hashed password from DB
		$real_hashed_password = $this->CI->users_model->get_value('password');
		
		//Compare
		if($input_hashed_password === $real_hashed_password)
		{
			return true;
		}
		
		return false;
	}

  function set_logged_in($username) {
      $this->CI->session->set_userdata(array('username' => $username, 'logged_in' => true));
  }

  function logout() {
       
      // delete session from session table for a dead kill
      if (isset($this->CI->session->userdata)) 
			{
          if (isset($this->CI->session->userdata['session_id'])) 
					{
              $this->CI->load->model('Sessions_model', 'sessions_model');
              $this->CI->sessions_model->delete_session($this->CI->session->userdata['session_id']);
          }
      }
      
      /*
       * the following operation clears session's userdata and
       * clears the user's cookie data - both are important
       * need to clear session user data in case code logs someone out
       * and then checks login status before doing a GET (e.g. redirect)
       * need to clear user's cookie in case code checks login status
       * after a GET
       */
      
      // clear session object userdata and user cookie value
      $this->CI->session->unset_userdata($this->CI->session->userdata);
      
  }

  function is_logged_in() {
  
      /*
       * user will always have a valid "session" after a GET,
       * we need to check the session userdata to see if the session
       * has been authenticated
       */
  
      /* yes, this is paranoid */
      if (!isset($this->CI->session->userdata['logged_in'])) {
          return false;
      }
      
      /* yes, this is even sicker */
      if (!is_bool($this->CI->session->userdata['logged_in'])) {
          return false;
      }
      
      /* yes, you could probably get away with just this */
      if (!$this->CI->session->userdata['logged_in']) {
          return false;
      }
      
      return true;
      
  }
	
}

?>
