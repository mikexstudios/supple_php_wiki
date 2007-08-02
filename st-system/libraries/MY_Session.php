<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * We want to remove database prefixes...
 */ 
class MY_Session extends CI_Session {

	var $table_prefix = '';

	function MY_Session()
	{
		$this->CI =& get_instance();

		//Store DB Prefix.
		$this->CI->load->database();
		$this->table_prefix = $this->CI->db->dbprefix;
		
		log_message('debug', "Session Class Initialized");
		$this->sess_run();
	}
	

	function sess_run()
	{
		$this->CI->db->dbprefix = '';
		parent::sess_run();
		$this->CI->db->dbprefix = $this->table_prefix;
	}
	
	function sess_read()
	{	
		$this->CI->db->dbprefix = '';
		$return_value = parent::sess_read();
		$this->CI->db->dbprefix = $this->table_prefix;
		return $return_value; //TRUE/FALSE, determines if new session is created
	}
	

	function sess_write()
	{								
		$this->CI->db->dbprefix = '';
		parent::sess_write();
		$this->CI->db->dbprefix = $this->table_prefix;
	}
	
	function sess_create()
	{	
		$this->CI->db->dbprefix = '';
		parent::sess_create();
		$this->CI->db->dbprefix = $this->table_prefix;
	}

	function sess_update()
	{	
		$this->CI->db->dbprefix = '';
		parent::sess_update();
		$this->CI->db->dbprefix = $this->table_prefix;
	}

	function sess_gc()
	{
		$this->CI->db->dbprefix = '';
		parent::sess_gc();
		$this->CI->db->dbprefix = $this->table_prefix;
	}
	
}
// END Session Class
?>
