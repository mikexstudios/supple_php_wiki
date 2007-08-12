<?php if (!defined('BASEPATH')) exit('No direct script access allowed');class Step3 extends Controller {	function Step3()	{		parent::Controller();					$this->load->library('validation');	}		function _initialize() {		//Unfortunately, we can't put the below code in the constructor since		//$this isn't fully initialized yet in the constructor (for some reason).		$this->load->helper('checkpoint');			//We check if st-config.php file exists		check_installed();				//Check if config file directory is writable		check_config_writable();				//Check if previous steps completed		check_step1_completed();		check_step2_completed();	}		//We don't need to remap here since we are using traditional	//URI format.		function index() {		$this->_initialize();		$this->_prep_form();		$this->load->view('step3');		}	function _prep_form() {		$this->validation->set_error_delimiters('<div class="error">', '</div>');				//Set validation rules		//Note we should also validate that the name does not already exist!		$rules['adminuser'] = 'required|trim|max_length[100]|alpha_dash';		$rules['adminpassword'] = 'required|trim|max_length[250]'; 		$rules['adminpassword2'] = 'required|trim|max_length[250]|matches[adminpassword]';		$rules['adminemail'] = 'required|trim|max_length[300]|valid_email';		$this->validation->set_rules($rules);				//Also repopulate the form		$fields['adminuser'] = 'Username';		$fields['adminpassword'] = 'Password';		$fields['adminpassword2'] = 'Password Again';		$fields['adminemail'] = 'Email Address';		$fields['submit'] = 'Continue Button'; 		$this->validation->set_fields($fields);	}	function check() {		$this->_initialize();		$this->_prep_form();				if($this->validation->run() == FALSE)		{			$this->load->view('step3');		}		else		{				//Now we create admin user			//Load the database first from temp file			$this->load->helper('loaddb');			$db_info = load_db(); //Includes database prefix, but we don't need it if we are using AR.						/*			//Generate random password			$this->load->helper('string');			$admin_username = 'admin';			$admin_password = random_string('alnum', 6);			*/						//Write to database			$is_error = false;						$data['username'] = $this->validation->adminuser;			$data['`key`'] = 'uid'; //KEY is a reserved MySQL word so we have to `` it.			$data['value'] = '1';			$result = $this->db->insert(ST_USERS_TABLE, $data);			if($result===FALSE) //Error in query			{				$is_error = true;				}						$data['username'] = $this->validation->adminuser;			$data['`key`'] = 'password';			$data['value'] = $this->validation->adminpassword;			$result = $this->db->insert(ST_USERS_TABLE, $data);			if($result===FALSE) //Error in query			{				$is_error = true;				}						$data['username'] = $this->validation->adminuser;			$data['`key`'] = 'email';			$data['value'] = $this->validation->adminemail;			$result = $this->db->insert(ST_USERS_TABLE, $data);			if($result===FALSE) //Error in query			{				$is_error = true;				}						//Check if an error occured			if($is_error)			{				error_creating_user();			}						//Set step3 completed			$this->session->set_userdata('step3_completed', true);						//Everything is good, so we move on to step 4:			header('Location: index.php?step4'); //We have to use this method of redirecting.		}	}}?>