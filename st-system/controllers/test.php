<?php

class Test extends Controller {

	function Test()
	{
		parent::Controller();	
	}
	
	function index()
	{
		$this->load->view('test_message');
	}
	
	function success($x='') {
		$data['x'] = $x;
		$this->load->view('test_success_message', $data);
	}
}
?>
