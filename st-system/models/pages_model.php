<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Pages_model extends Model {
	var $id = '';
	var $pagename = '';
	var $time = '';
	var $author = '';
	var $body = '';
	
	var $page = array(); //Contains all page information (array). Result from query.
	var $revisions_data;

	
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
		$this->template->add_function('revision_list', array(&$this, 'get_all_revisions_data'));
	}
	
	function _set_where_clauses() {
	
		//And clauses are automatically added
		if(!empty($this->id))
			{ $this->db->where('id', $this->id); } // echo $this->id; 
		else if(!empty($this->pagename))
			{ $this->db->where('tag', $this->pagename); }
		else
			{ return false; } //Nothing can happen since page identifiers aren't set.
		
		return true; //Something was set.
	}
	
	/**
	 * loadPage gets the page information from the database.
	 * 
	 * @access private
	 * @return mixed Page table row containing content, author, etc.
	 */
	function loadPage() {
		
		//Set where clauses
		if($this->_set_where_clauses() == false)
			{ return; } //Nothing can happen since page identifiers aren't set.
		
		//We load the latest page first just so we can load information from the latest page.
		$this->db->select('*');
		$this->db->from(ST_PAGES_TABLE);
		$this->db->orderby('id', 'desc');
		$this->db->limit(1);
		$query = $this->db->get();
		$this->page = $query->row_array();
		
		$query->free_result(); //Cut down on memory consumption
		
		//If the page id is still empty, then the page did not exist!
		//We prompt user to create if no id is set. Otherwise, we error.
		if(empty($this->page))
		{
			$this->page['body'] = '';
			$this->page['time'] = '';
			return false;
		}	

	}
	
	/**
	 * Note: Deletes only from the PAGES_TABLE. Not the archives table.
	 */	 	
	function delete() {
		//Set where clauses
		if($this->_set_where_clauses() == false)
			{ return; } //Nothing can happen since page identifiers aren't set.
		
		//$this->db->from();
		$this->db->delete(ST_PAGES_TABLE);
	}
	
	function insert() {
		//Doing the INSERT
		$data['tag'] = $this->pagename;
		$data['time'] = $this->time;
		$data['user'] = $this->author;
		$data['note'] = $this->note;
		$data['body'] = $this->body;
		
		$this->db->insert(ST_PAGES_TABLE, $data);
	}
	
	function get_all_revisions_data() {
		//Set where clauses
		if($this->_set_where_clauses() == false)
			{ return; } //Nothing can happen since page identifiers aren't set.
		
		//We load the latest page first just so we can load information from the latest page.
		$this->db->select('*');
		$this->db->from(ST_PAGES_TABLE);
		$this->db->orderby('id', 'desc');
		$query = $this->db->get();
		$this->revisions_data = $query->result_array();
		
		return $this->revisions_data;
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
