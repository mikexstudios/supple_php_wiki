<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Manages and produces the admin menu system.
 *
 * Meant to be a dynamic menu system where admin files can add and edit
 * the admin menu at will so that new menu functions can be contained totally
 * in a single file minimizing the need to keep editing a menu file to add
 * new menu entries.
 */
class AdminMenu {
	//var $CI;
	
	var $menu = array();
	var $submenu = array();
	
	function AdminMenu() {
		//$this->CI =& get_instance();
	}
	
	/**
	* Adds a new entry to the top level menu.
	*
	* @param string $in_link_to What this menu entry will link to. Try to use relative paths. Usually, this is just the script filename (ie. 'editcomic.php').
	* @param string $in_name The name that will be displayed in the menu.
	* @param int $in_position Denotes position of entry in the menu list.
	*/
	function add_top_level($in_link_to, $in_name, $in_position) {
		$this->menu[] = array('link_to' => $in_link_to,
													'name' => $in_name,
													'position' => $in_position);
	}
	
	function add_sub_level($in_link_to, $in_name, $in_position, $in_associated_page) {
		$unique_id = md5($in_associated_page);
		$this->submenu[$unique_id][] = array('link_to' => $in_link_to,
																				'name' => $in_name,
																				'position' => $in_position, 
																				'associated_page' => $in_associated_page);
	}

	//Add menu removing functions here


	function get_top_level() {
		$this->_sort_top_level();
		return $this->menu;
	}
	
	function get_sub_level($in_associated_page='') {

		if(!empty($in_associated_page))
		{
			$this->_sort_sub_level($in_associated_page);
	
			$unique_id = md5($in_associated_page);
			if(isset($this->submenu[$unique_id]))
			{
				return $this->submenu[$unique_id];
			}
			
			return array(); //We return empty array since there is no submenu for this page
		}
		else
		{
			return $this->submenu;
		}
	}

	/**
	 * Sort with position ascending
	 */	 	
	function _sort_top_level() {
		uasort($this->menu, array(&$this, '_position_sort_callback'));	
	}
	
	function _sort_sub_level($in_associated_page='') {
		if(!empty($in_associated_page))
		{
			$unique_id = md5($in_associated_page);
			if(isset($this->submenu[$unique_id]))
			{
				uasort($this->submenu[$unique_id], array(&$this, '_position_sort_callback'));
			}
			return;
		}
		else
		{
			//We have to sort all of them
			foreach(array_keys($this->submenu) as $key)
			{
				uasort($this->submenu[$key], array(&$this, '_position_sort_callback'));
			}
		}
				
	}

	/**
	 * Used by sort()/usort() to compare two elements. 
	 *	 	
	 * @access private
	 */	 	
	function _position_sort_callback($a, $b) {
		if($a['position'] == $b['position'])
			{ return 0; }
		else if($a['position'] < $b['position'])
			{ return -1; }
		else //a > b
			{ return 1; }			
	}

}


?>
