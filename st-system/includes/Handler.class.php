<?php
/**
 * Parent class to Handlers.
 * 
 * The purpose of this class is to provide some base functions for all Handlers.
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
	
	/**
	 * Registers a plugin action with the core as a tag that can be
	 * used later during script execution for template purposes.
	 *
	 * @access protected
	 * @param string $tag Short variable-like name to associate with the function such as 'getimagetag'.
	 * @param string $methodName Method (that exists on this object) to be registered such as 'getImageTag()'.
	 */
	function registerAction($tag, $methodName)
	{
	    global $Supple;
	
	    $Supple->registerAction($tag, array(&$this, $methodName));
	}
	

}
?>