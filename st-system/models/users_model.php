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
		$query = $this->db->get();
		
		foreach($query->result() as $row)
		{
			$this->all_users[$row->username][$row->key] = $row->value;
		}
		
		return $this->all_users;
	}
	
	function get_value($in_key) {
		$this->db->select('value');
		$this->db->from(ST_USERS_TABLE);
		$this->db->where('username', $this->username);
		$this->db->where('`key`', $in_key); //key is a MySQL reserved word. We need to quote it.
		$this->db->limit(1);
		$query = $this->db->get();
		return element('value', $query->row_array()); //We want a single result
		
		$query->free_result();
	}
	
	function set_value($in_key, $in_value, $in_attribute='') {	
		
		//check if the key exists. We want this to come before the other
		//AR SQL statements. Otherwise, we run into conflicts.
		$temp_value = $this->get_value($in_key);
		
		//In both UPDATE and INSERT, we have to set the value and attr.
		$this->db->set('value', $in_value);
		$this->db->set('attribute', $in_attribute);
		$this->db->where('username', $this->username);
		$this->db->where('`key`', $in_key);
		$this->db->limit(1);
		
		if(!empty($temp_value)) //empty() can only be used on a variable
		{
			$this->db->update(ST_USERS_TABLE);
		}
		else
		{
			$this->db->insert(ST_USERS_TABLE);
		}
	}
	
}

?>
