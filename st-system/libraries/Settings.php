<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Settings {
	var $CI;	
	var $configInfo; //Holds all configuration info records

	function Settings() {
		$this->CI =& get_instance();
	
		//Grab settings from database
		$this->get_config_info();
		
		log_message('debug', "Template Class Initialized");
	}
	
	function get_config_info()
	{
		$this->CI->db->select('*');
		$this->CI->db->from(ST_CONFIG_TABLE);
		$this->CI->db->orderby('`order`', 'ASC'); //Need order to be in ` `
	
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
	
	function get($inKey)
	{
		return $this->configInfo[$inKey]['value']; //Returns blank if nothing
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
	
	//Leave the SQL not working for now.
	function add_new_setting($inKey, $inValue, $inVarType, $inDisplayCode, $inName, $inDescription)
	{
		global $message;
		
		$order = $this->Db->get_var("SELECT MAX(`order`)+1 FROM ".ST_CONFIG_TABLE);

		$sql = '
		    INSERT INTO '.ST_CONFIG_TABLE." (`order`, `option`, `value`, `vartype`, `displaycode`, `name`, `description`)
			VALUES ('$order', '$inKey', '$inValue', '$inVarType', '$inDisplayCode', '$inName', '$inDescription')";
		$result = $this->Db->query($sql);
		if ($result == 0) {
			#$message->error("Could not add new setting!");
			die('Could not add new setting!');
		}	
	}
	
	function change_setting_value($inKey, $inValue)
	{
		global $message;

        switch ($this->getVarType($inKey))
        {
            case 'number':
                $inValue = (int)$inValue;
                break;
            case 'boolean':
                $inValue = (int)(bool)$inValue;
                break;
            default:
                break;
        }

		$sql = '
		    UPDATE '.ST_CONFIG_TABLE."
			SET `value` = '$inValue'
			WHERE `option` = '$inKey'"; //OPTION is a sql reserved word
		$result = $this->Db->query($sql);
		if ($result==0) {
			#$message->error('Could not change value for setting! SQL: '.$sql);
			die('Could not change value for setting! SQL: '.$sql);
		}
	}
	
	function change_setting_description($inKey, $inDescription)
	{
		global $message;
		
		$sql = '
		    UPDATE '.ST_CONFIG_TABLE."
			SET `description` = '$inDescription'
			WHERE `option` = '$inKey'";
		$result = $this->Db->query($sql);
		if ($result==0) {
			#$message->error("Could not change description for setting!");
			die('Could not change description for setting!');
		}
	}
	
	function does_setting_exist($inKey)
	{
		if (array_key_exists($inKey, $this->configInfo) && !empty($this->configInfo[$inKey]['option'])) {
			//Option exists
			return true;
		}	
		
		//Option does not exist
		return false;
	}
	
}

/*
//Testing Settings
$x = new Settings();
*/
?>
