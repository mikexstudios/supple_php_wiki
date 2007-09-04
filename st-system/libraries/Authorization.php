<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @author Zelaza (http://codeigniter.com/forums/viewthread/54283/#265761) 
 * @author Michael Huynh (mike@mikexstudios.com) 
 */ 
class Authorization {
	var $CI;
	
	var $prefix = ''; //We set this to the config value
	
	function Authorization() {
		$this->CI =& get_instance();
		
		$this->CI->load->model('Users_model', 'users_model');
		
		$wiki_tag = $this->CI->config->item('wiki_tag');
		if(!empty($wiki_tag))
		{
			$this->prefix = $wiki_tag.'_';
		} 
		
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
      $this->CI->session->set_userdata('username', $username);
      $user_wikis = get_user_wikis($username);
      if($user_wikis !== false)
      {
	      foreach($user_wikis as $each_wiki)
	      {
					$this->CI->session->set_userdata($each_wiki.'_logged_in', true);
				}
			}
			else
			{
				$this->CI->session->set_userdata('logged_in', true);
			}
  }

  function logout() {
      
      //Seems like the OBSession library only requires this:
      $this->CI->session->sess_destroy();
    
  }

  function is_logged_in() {
  
      /*
       * user will always have a valid "session" after a GET,
       * we need to check the session userdata to see if the session
       * has been authenticated
       */
  
      /* yes, this is paranoid */
      if (!isset($this->CI->session->userdata[$this->prefix.'logged_in'])) {
          return false;
      }
      
      /* yes, this is even sicker */
      if (!is_bool($this->CI->session->userdata[$this->prefix.'logged_in'])) {
          return false;
      }
      
      /* yes, you could probably get away with just this */
      if (!$this->CI->session->userdata[$this->prefix.'logged_in']) {
          return false;
      }
      
      return true;
      
  }
	
}

?>
