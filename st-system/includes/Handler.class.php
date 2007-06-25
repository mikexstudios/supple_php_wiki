<?php
/**
 * suppleText main script
 * 
 * The purpose of this class is to load the core.  
 *
 * @package suppleText
 * @version $Id:$
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * 
 */
 
class Handler {
	var $Db;
	var $Supple;
	
	function Handler() {
		global $Supple;
		
		$this->Supple = &$Supple;
		
		//Set classes that these Handler's can access
		$this->Db = &$this->Supple->getDatabaseConnection();
	}
	

}
 
 
?>  