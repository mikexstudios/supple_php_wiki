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
		if (!file_exists(ABSPATH.'st-external/st-config.php')) 
		{
			//Display install page
			$this->load->view('index');	
		}
		else
		{
			$data['error_title'] = 'Already Installed!';
			$data['error_content'] = '<p>The file \'st-external/st-config.php\' already exists. suppleText is probably already installed. Now <a href="../../">go use the script</a>!</p>';
			
			$this->load->view('error', $data);
			//show_error('The file \'st-external/st-config.php\' already exists. suppleText is probably already installed. Now <a href="../../">go use the script</a>!');
		}
	}
}
?>
