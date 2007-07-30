<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

//Not sure if there is a better way to load this:
include_once APPPATH.'controllers/show.php';

class Diff extends Show {
	//Make sure these variables have a unique name from any included classes!
	var $pid; //Can't name this $id since clash with pages_model $id.
	var $data; //Both arrays with keys: a, b
	var $added;
	var $deleted;

	function Diff()
	{
		parent::Show(); //Loads pages model
	}
	
	//Inherits _remap(), _set_page_info(...) from Show 
	
	function register_functions() {
		$Supple->registerAction('diff_added', 'getAddedFormatted');
		$Supple->registerAction('diff_deleted', 'getDeletedFormatted');
		$Supple->registerAction('revision_a_time', 'getATime');
		$Supple->registerAction('revision_b_time', 'getBTime');
	}
	
	function display($pagename) {
		$this->_set_page_info($pagename);
		
		$this->pid['a'] = $this->input->get('a');
		$this->pid['b'] = $this->input->get('b');
		
		//Secure input
		if(!$this->validation->numeric($this->pid['a']) || !$this->validation->numeric($this->pid['b'])) 
		{
			show_error("The specified page id's are invalid.");
		}

		//Get page data from both revisions.
		$this->load->model('Pages_model', 'pages_model_diff');
		$this->pages_model_diff->pagename = $pagename;
		
		//Get data for a:
		$this->pages_model_diff->id = $this->pid['a'];
		$this->pages_model_diff->loadPage();
		$this->data['a'] = $this->pages_model_diff->page;
		
		//Check for existance of this revision
		if(empty($this->data['a']['time']))
		{
			show_error('The specified page id is invalid: '.$this->pid['a']);
		}
		
		//Get data for b:
		$this->pages_model_diff->id = $this->pid['b'];
		$this->pages_model_diff->loadPage();
		$this->data['b'] = $this->pages_model_diff->page;
		
		//Check for existance of this revision
		if(empty($this->data['b']['time']))
		{
			show_error('The specified page id is invalid: '.$this->pid['b']);
		}
		
		$this->compute_differences();
		
		//Set template tags
		$this->template->add_value('diff_added', format_text($this->added));
		$this->template->add_value('diff_deleted', format_text($this->deleted));
		$this->template->add_value('diff_a', $this->data['a']);
		$this->template->add_value('diff_b', $this->data['b']);
		
		
		$this->load->view('diff');
	}
	
	/**
	 * Does not return anything.	
	 * @access private
	 */	 	
	function compute_differences() {
		
		// prepare bodies. Below code from Wikkawiki:
		$bodyA = explode("\n", $this->data['a']['body']);
		$bodyB = explode("\n", $this->data['b']['body']);

		$this->added   = array_diff($bodyA, $bodyB);
		$this->deleted = array_diff($bodyB, $bodyA);
		
		//We turn into strings
		$this->added = implode("\n", $this->added);
		$this->deleted = implode("\n", $this->deleted);
	}
	
}
?>
