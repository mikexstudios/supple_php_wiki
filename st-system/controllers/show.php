<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Show extends Controller {

	function Show()
	{
		parent::Controller();	
		$this->load->model('pages_model');
	}
	
	/**
	 * We remap the second part of the URL (what should be the method)
	 * to the pagename.
	 */	 	 	
	function _remap($method) {
		if($method === 'index')
		{
			$this->index();
		}
		else
		{
			$this->display($method);
		}
	}
	
	function index()
	{
		$this->display($this->settings->get('root_page'));
	}
	
	function _set_page_info($pagename) {
		$this->pages_model->register_functions();
		
		//Check to see if id param is set in URL:
		//ie. HomePage/show/45
		$in_id = $this->uri->segment(3);
		if(!empty($in_id) && $this->validation->numeric($in_id)) //Verify that it is an integer
		{
			$this->pages_model->id = $in_id;
		}
		
		$this->pages_model->pagename = $pagename;
		$this->pages_model->loadPage();
		
	}
	
	function display($pagename) {
		
		$this->_set_page_info($pagename);
		
		//Syntax formatting. 
		if(does_current_page_exist())
		{
			//Check to see if user has permission to read this page
			$page_read_roles = get_page_read_roles($pagename);
			$user_role = get_user_role();
			if(does_user_have_permission($user_role, $page_read_roles))
			{
				$this->pages_model->page['body'] = format_text($this->pages_model->page['body']);
				
				//We also load page metadata
				load_page_metadata($this->pages_model->pagename);
			}
			else
			{
				$this->pages_model->page['body'] = '<p>You do not have the permission to view this page.</p>';
			}
		}
		$this->load->view('show');
	}
	

	

}
?>
