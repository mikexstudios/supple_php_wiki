<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Settings {
	var $CI;	
	var $configInfo; //Holds all configuration info records

	function Settings() {
		$this->CI =& get_instance();
		
		$this->CI->load->helper('array'); //Settings is called before autoload
		
		//Grab settings from database
		//$this->get_config_info();
		
		log_message('debug', "Template Class Initialized");
	}
	
	function get_config_info()
	{
		$this->CI->db->select('*');
		$this->CI->db->from(ST_CONFIG_TABLE);
		$this->CI->db->orderby('`id`', 'ASC'); //Need order to be in ` `
	
		$query = $this->CI->db->get();
		$result = $query->result_array();

		if(empty($result))
		{
			die('Could not query config information: '.$e->getMessage() );
		}
		
		
		foreach ($result as $row) {
			//Place configuration information in array
			$this->configInfo[$row['option']]['order'] = $row['order'];
			$this->configInfo[$row['option']]['option'] = $row['option'];
			$this->configInfo[$row['option']]['vartype'] = $row['vartype'];
			$this->configInfo[$row['option']]['displaycode'] = $row['displaycode'];
			$this->configInfo[$row['option']]['name'] = $row['name'];
			$this->configInfo[$row['option']]['description'] = $row['description'];
			switch ($row['displaycode'])
			{
                case 'number':
                    $this->configInfo[$row['option']]['value'] = (int)$row['value'];
                    break;
                case 'boolean':
                    $this->configInfo[$row['option']]['value'] = (bool)$row['value'];
                    break;
                default:
                    $this->configInfo[$row['option']]['value'] = $row['value'];
                    break;
            }
		}

	}

	//Kind of useless, but used by doesSettingExist to check for existence
	function get_order($inKey)
	{
		return $this->configInfo[$inKey]['order'];
	}
	
	function get_var_type($inKey)
	{
		return $this->configInfo[$inKey]['vartype'];
	}
	
	function get_display_code($inKey)
	{
		return $this->configInfo[$inKey]['displaycode'];
	}

	function get_option($inKey)
	{
		return $this->configInfo[$inKey]['option'];
	}
	
	function get($in_key)
	{
		$this->CI->db->select('value');
		$this->CI->db->from(ST_CONFIG_TABLE);
		$this->CI->db->where('`key`', $in_key); //key is a MySQL reserved word. We need to quote it.
		$this->CI->db->limit(1);
		$query = $this->CI->db->get();
		
		return element('value', $query->row_array()); //We want a single result
	}
	
	function get_name($inKey)
	{
		return $this->configInfo[$inKey]['name'];	
	}

	function get_description($inKey)
	{
		return $this->configInfo[$inKey]['description'];
	}

	function get_configinfo_array()
	{
		return $this->configInfo;	
	}
	
	function set($in_key, $in_value)
	{
		//check if the key exists. Needs to come before other AR SQL statements.
		$temp_value = $this->get($in_key);
		
		$this->CI->db->set('value', $in_value);
		$this->CI->db->where('`key`', $in_key);
		$this->CI->db->limit(1);
		
		if(!empty($temp_value)) //empty() can only be used on a variable
		{
			return $this->CI->db->update(ST_CONFIG_TABLE);
		}
		else
		{
			return $this->CI->db->insert(ST_CONFIG_TABLE);
		}
	}
	
}

/*
//Testing Settings
$x = new Settings();
*/
?>
