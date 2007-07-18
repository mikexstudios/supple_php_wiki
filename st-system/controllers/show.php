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
		$this->pages_model->pagename = $this->config->item('default_page');
		//$this->pages_model->setPagename($this->config->item('default_page'));
		$this->pages_model->loadPage();
		$data['page_content'] = $this->pages_model->get_content();
		$this->load->view('show', $data);
	}
	
	function display($pagename) {
		$data['x'] = $pagename;
		$this->load->view('test_success_message', $data);
	}
	

	

}
?>
