<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Install extends Controller {

	function Install()
	{
		parent::Controller();	
	}
	
	//We don't need to remap here since we are using traditional
	//URI format.
	
	function index() {
		//We check if st-config.php file exists
		$this->load->helper('checkpoint');
		check_installed();
		
		//Display install page
		$this->load->view('index');	
	}
}
?>
