<?php

class Welcome extends Controller {

	function Welcome()
	{
		parent::Controller();	
	}
	
	function index()
	{
		$this->load->view('welcome_message');
	}
	
	function success($x) {
		$data['x'] = $x;
		$this->load->view('welcome_success_message', $data);
	}
}
?>
