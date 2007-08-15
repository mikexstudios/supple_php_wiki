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
			$this->pages_model->page['body'] = format_text($this->pages_model->page['body']);
		}
		$this->load->view('show');
	}
	

	

}
?>
