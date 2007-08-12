<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Step1 extends Controller {

	function Step1()
	{
		parent::Controller();	

		$this->load->library('validation');
	}
	
	function _initialize() {
		//Unfortunately, we can't put the below code in the constructor since
		//$this isn't fully initialized yet in the constructor (for some reason).
		$this->load->helper('checkpoint');
	
		//We check if st-config.php file exists
		check_installed();
		
		//Check if config file directory is writable
		check_config_writable();
	}
	
	//We don't need to remap here since we are using traditional
	//URI format.
	
	function index() {
		$this->_initialize();
					
		//Display install page
		$this->_prep_form();
		$this->load->view('step1');	
	}
	
	function check_config_writable() {
		if(!is_writable(ABSPATH.'st-external/'))
		{
			error_directory_writable();
		}
	}
	
	function _prep_form() {
		$this->validation->set_error_delimiters('<div class="error">', '</div>');
		
		//Set validation rules
		//Note we should also validate that the name does not already exist!
		$rules['dbms'] = 'required|trim|max_length[30]'; //We don't require this since the page can be empty.
		$rules['dbhost'] = 'required|trim|max_length[200]'; //We can't do alpha_dash since we allow spaces!
		$rules['dbname'] = 'required|trim|alpha_dash|max_length[200]';
		$rules['dbuser'] = 'required|trim|max_length[200]';
		$rules['dbpass'] = 'trim';
		$rules['tblprefix'] = 'trim|alpha_dash|maxlength[50]';
		$fules['submit'] = 'required';
		$this->validation->set_rules($rules);
		
		//Also repopulate the form
		$fields['dbms'] = 'Database Type';
		$fields['dbhost'] = 'Database Host'; //These names correspond to what is shown in error message.
		$fields['dbname'] = 'Database Name';
		$fields['dbuser'] = 'Database Username';
		$fields['dbpass'] = 'Database Password';
		$fields['tblprefix'] = 'Table Prefix';
		$fields['submit'] = 'Continue Button'; //We need to set some name so that validation stores the value
		$this->validation->set_fields($fields);
	}
	
	function check() {
		$this->_initialize();
		$this->_prep_form();
		
		if ($this->validation->run() == FALSE)
		{
			$this->load->view('step1');
		}
		else
		{
			//Check database connection
			$db_config['hostname'] = $this->validation->dbhost;
			$db_config['username'] = $this->validation->dbuser;
			$db_config['password'] = $this->validation->dbpass;
			$db_config['database'] = $this->validation->dbname;
			$db_config['dbdriver'] = $this->validation->dbms;
			$db_config['dbprefix'] = $this->validation->tblprefix;
			$db_config['pconnect'] = FALSE;
			$db_config['db_debug'] = FALSE; //We use our own error messages
			$db_config['active_r'] = TRUE;
			
			$db_load_result = $this->load->database($db_config, TRUE); //TRUE = return database object
			
			if($db_load_result->conn_id === FALSE)
			{
				error_db_connect();
			}
			
			//Write a preliminary config file
			$this->_generate_preliminary_config_file($db_config);
			
			//Set step1 completed
			$this->load->library('session');
			$this->session->set_userdata('step1_completed', true);
			
			//Everything is good, so we move on to step 2:
			header('Location: index.php?step2'); //We have to use this method of redirecting.
			
		}
	}
	
	function _generate_preliminary_config_file($db_config) {
	    
			$config_sample_file = @file_get_contents(ABSPATH.'st-external/st-config.php.sample');
			if($config_sample_file === FALSE)
			{
				error_sample_config_file();
			}
			
			//Set search and replaces
			$search[] = 'st_'; $replace[] = $db_config['dbprefix']; //Do this before the others since this has the most potential to conflict.
			$search[] = 'localhost'; $replace[] = $db_config['hostname'];
			$search[] = 'putyourdbnamehere'; $replace[] = $db_config['database'];
			$search[] = 'usernamehere'; $replace[] = $db_config['username'];
			$search[] = 'yourpasswordhere'; $replace[] = $db_config['password'];
	
			$config_sample_file = str_replace($search, $replace, $config_sample_file);
			
			if(!file_put_contents(ABSPATH.'st-external/st-config.temp.php', $config_sample_file))
			{
				error_directory_writable();
			}
	}

}

?>
