<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Pages_model extends Model {
	var $pagename;
	var $time;
	var $page;
	var $id;
	
	function Pages_model() {
		parent::Model();	
	}
	
	function register_functions() {
		//Add functions to be used by themes.
		//@todo Add a hook here so that plugin files can add theme functions too.
		$this->template->add_function('page_content', array(&$this, 'get_content'));
		$this->template->add_function('page_tag', array(&$this, 'get_tag'));
		$this->template->add_function('page_time', array(&$this, 'get_time'));
		$this->template->add_function('page_id', array(&$this, 'get_id'));	
	}
	
	/**
	 * loadPage gets the page information from the database.
	 * 
	 * @access private
	 * @return mixed Page table row containing content, author, etc.
	 */
	function loadPage() {
		//Possibly add AND clauses in the future.
		if(!empty($this->id))
			{ $this->db->where('id', $this->id); }
		else if(!empty($this->pagename))
			{ $this->db->where('tag', $this->pagename); }
		else
			{ return; } //Nothing can happen since page identifiers aren't set.
		
		
		//Could probably do this a little better
		//We load the latest page first just so we can load information from the latest page.
		$this->db->select('*');
		$this->db->from(ST_PAGES_TABLE);
		$this->db->limit(1);
		
		$query = $this->db->get();
		$this->page = $query->row_array();
		
		//If the ID that we want isn't the latest page id, we look in the archives table.
		if(!empty($this->id) && $this->page['id'] != $this->id)
		{
			$this->db->from(ST_ARCHIVES_TABLE);
			$query = $this->db->get();
			$this->page = $query->row_array();
		}														
		
		//Then we check if we need to grab an older copy.														
		if(!empty($this->time) && (strcmp($this->page['time'], $this->time)!=0)) //The latest page time and the specified time are not the same.
		{
			$this->db->from(ST_ARCHIVES_TABLE);
			$this->db->where('time', $this->time);
			$this->page = $query->row_array();
		}
		
		$query->free_result(); //Cut down on memory consumption
	}
	
	function get_all() {
		return $this->page;
	}
	
	function get_id() {
		return $this->page['id'];
	}
	
	function get_content() {
		return $this->page['body'];
	}
	
	function get_tag() {
		if(empty($this->page['tag']))
		{
			return $this->pagename; //This fixes cases where the page does not exist yet.
		}
		
		return $this->page['tag'];
	}
	
	function get_author() {
		return $this->page['user'];
	}
	
	function get_time() {
		return $this->page['time'];
	}
}

?>
