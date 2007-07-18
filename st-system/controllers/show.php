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
		$this->display($this->config->item('default_page'));
	}
	
	function display($pagename) {
		$this->pages_model->register_functions();
		
		$this->pages_model->pagename = $pagename;
		$this->pages_model->loadPage();
		$this->load->view('show');
	}
	

	

}
?>
