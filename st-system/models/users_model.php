<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Users_model extends Model {
	//var $id;
	var $username = '';
	//var $key = '';
	//var $value = '';
	//var $attribute = '';
	
	var $all_users = array(); //Holds all user info
	
	function Users_model() {
		parent::Model();	
	}
	
	function get_all() {
		$this->db->select('*');
		$this->db->from(ST_USERS_TABLE);
		$this->db->orderby('id', 'asc');
		$query = $this->db->get();
		
		foreach($query->result() as $row)
		{
			$this->all_users[$row->username][$row->key] = $row->value;
		}
		
		return $this->all_users;
	}
	
	/**
	 * Mainly used for MU environments. Returns array of username and role.
	 */	 	
	function get_wiki_username_and_roles() {
		$wiki_tag = $this->config->item('wiki_tag');
		
		$this->db->select('*');
		$this->db->from(ST_USERS_TABLE);
		$this->db->where('`key`', $wiki_tag.'_role');
		$query = $this->db->get();
		
		$user_info = array();
		foreach($query->result() as $row)
		{
			$user_info[$row->username]['role'] = $row->value;
		}
		
		return $user_info;
	}
	
	function get_next_uid() {
		$this->db->select('value');
		$this->db->from(ST_USERS_TABLE);
		$this->db->where('`key`', 'uid');
		$this->db->orderby('value', 'desc');
		$this->db->limit(1);
		$query = $this->db->get();
		$largest_uid = intval(element('value', $query->row_array())); //We want a single result
		
		return $largest_uid+1;
	}
	
	function get_username($in_uid) {
		$this->db->select('username');
		$this->db->from(ST_USERS_TABLE);
		$this->db->where('`key`', 'uid'); //key is a MySQL reserved word. We need to quote it.
		$this->db->where('value', $in_uid);
		$this->db->limit(1);
		$query = $this->db->get();
		return element('username', $query->row_array()); //We want a single result
	}
	
	function get_value($in_key) {
		$this->db->select('value');
		$this->db->from(ST_USERS_TABLE);
		$this->db->where('username', $this->username);
		$this->db->where('`key`', $in_key); //key is a MySQL reserved word. We need to quote it.
		$this->db->limit(1); //Currently, we are only using results that return one row
		$query = $this->db->get();
		
		/**
		 * Currently, we are only using results that return one row
		 */		 		
		//Check how many results are returned:
		if($query->num_rows() > 1)
		{
			$result = $query->result_array();
			
			$value_array = array();
			foreach($result as $row)
			{
				$value_array[] = $row['value'];
			}
			
			return $value_array;
		}
		elseif($query->num_rows() == 1)
		{
			$result = $query->row_array(); //We want a single result
			$value = $result['value']; //We can't use element() because it returns false when the value is ''
			if($value == '')
			{
				return '';
			}
			
			return $value;
		}
		else
		{
			//No results
			return false;
		}
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
			$this->db->where('username', $this->username);
			$this->db->where('`key`', $in_key);
			return $this->db->update(ST_USERS_TABLE);
		}
		else
		{
			$this->db->set('username', $this->username); //Notice we use set here instead of where
			$this->db->set('`key`', $in_key);
			return $this->db->insert(ST_USERS_TABLE);
		}
	}
	
	function delete_key($in_key) {
		$this->db->where('username', $this->username);
		$this->db->where('`key`', $in_key);
		return $this->db->delete(ST_USERS_TABLE);
	}
	
	function delete_all() {
		$this->db->where('username', $this->username);
		return $this->db->delete(ST_USERS_TABLE);
	}
	
}

?>
