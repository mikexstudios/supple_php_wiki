<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Page_metadata_model extends Model {
	//var $id;
	var $pagename = '';
	//var $key = '';
	//var $value = '';
	//var $attribute = '';
	
	var $all_pages = array(); //Holds all user info
	
	function Page_metadata_model() {
		parent::Model();	
	}
	
	function get_all() {
		$this->db->select('`key`, value'); //Maybe work in attribute?
		$this->db->from(ST_PAGE_METADATA_TABLE);
		$this->db->where('pagename', $this->pagename);
		$this->db->orderby('id', 'asc');
		$query = $this->db->get();
		
		$page_info = array();
		foreach($query->result() as $row)
		{
			$page_info[$row->key] = $row->value;
		}
		
		return $page_info;
	}
	
	function get_value($in_key) {
		$this->db->select('value');
		$this->db->from(ST_PAGE_METADATA_TABLE);
		$this->db->where('pagename', $this->pagename);
		$this->db->where('`key`', $in_key); //key is a MySQL reserved word. We need to quote it.
		$this->db->limit(1);
		$query = $this->db->get();
		
		return element('value', $query->row_array()); //We want a single result
	}
	
	function set_value($in_key, $in_value, $in_attribute='') {	
		
		//check if the key exists. We want this to come before the other
		//AR SQL statements. Otherwise, we run into conflicts.
		$temp_value = $this->get_value($in_key);
		
		//In both UPDATE and INSERT, we have to set the value and attr.
		$this->db->set('value', $in_value);
		$this->db->set('attribute', $in_attribute);
		$this->db->limit(1);
		
		if(!empty($temp_value)) //empty() can only be used on a variable
		{
			$this->db->where('pagename', $this->pagename);
			$this->db->where('`key`', $in_key);
			return $this->db->update(ST_PAGE_METADATA_TABLE);
		}
		else
		{
			$this->db->set('pagename', $this->pagename); //Notice we use set here instead of where
			$this->db->set('`key`', $in_key);
			return $this->db->insert(ST_PAGE_METADATA_TABLE);
		}
	}
	
	function delete_key($in_key) {
		$this->db->where('pagename', $this->pagename);
		$this->db->where('`key`', $in_key);
		return $this->db->delete(ST_PAGE_METADATA_TABLE);
	}
	
	function delete_all() {
		$this->db->where('pagename', $this->pagename);
		return $this->db->delete(ST_PAGE_METADATA_TABLE);
	}
	
}

?>
