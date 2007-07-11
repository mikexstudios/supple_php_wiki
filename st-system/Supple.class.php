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
 * @author Hendrik Mans <hendrik@mans.de>
 * @author Jason Tourtelotte <wikka-admin@jsnx.com>
 * @author {@link http://wikkawiki.org/JavaWoman Marjolein Katsma}
 * @author {@link http://wikkawiki.org/NilsLindenberg Nils Lindenberg}
 * @author {@link http://wikkawiki.org/DotMG Mahefa Randimbisoa}
 * @author {@link http://wikkawiki.org/DarTar Dario Taraborelli}
 * @author Michael Huynh <mike@mikexstudios.com> 
 * 
 * @copyright Copyright 2002-2003, Hendrik Mans <hendrik@mans.de>
 * @copyright Copyright 2004-2005, Jason Tourtelotte <wikka-admin@jsnx.com>
 * @copyright Copyright 2006, {@link http://wikkawiki.org/CreditsPage Wikka Development Team}
 * @copyright Copyright 2007, suppleText Development Team
 *   
 */

/**
 * Reference to the Supple class. Just declaring it first.
 *
 * Used to set an external reference to the Xcomic class. Allows
 * nested functions and outside functions to access the instance
 * of the Xcomic class.
 *
 * @global reference $xcomic
 */
$Supple = null;

/**
 * The core class of the Supple system. 
 *
 * Pulls together the base classes and loads the plugins system for 
 * the rest of the script to use. This is meant to be small and very extensible.
 */
class Supple {
	var $Db; //Database connection
	var $pagename; //The page name we are operating on.
	var $handlerName; //Holds the current handler name.
	var $actions = array();

	// {{{ Classes objects
	/**#@+
	* @access public
	* @var object
	*/
	var $Input;
	var $SyntaxParser;
	var $UserManagement;
	var $Settings;
	var $Validation;
	/**#@-*/
	// }}}

	/**
	 * Constructor for the Supple class.
	 *
	 * Takes a database reference as the only parameter. Therefore, a database
	 * object must be created first. This constructor also calls some private
	 * methods that sets up Supple's plugins system.
	 *
	 * @param reference &$dbc Database reference
	 */
	function Supple(&$dbc) {
		//Take database reference from initialize and set to class variable.
		$this->Db = &$dbc;

		//Set external $Supple reference so nested functions can make
		//call methods in this class and make changes if needed.
		$this->setExternalReference();
		
		$this->loadAndAssociateCoreClasses();

	}
		
	/**
	 * Sets an external global variable to $this (reference to this class).
	 *
	 * Since the actions functions are being loaded inside the loadFilesInDirectory()
	 * method of this class, they are functions existing inside another function. Therefore
	 * they cannot access the $this reference to this class. To work around this problem,
	 * an external global variable, $Supple, is used to reference $this. This method
	 * sets the external, global variable.
	 *
	 * @access private
	 */
	function setExternalReference() {
		
		//As described by the PHP manual (http://www.php.net/manual/en/language.references.whatdo.php)
		//one cannot just assign a reference inside a function to a global variable like:
		//global $var; $var =& $this;
		//since $var is a reference to $GLOBALS[] array.
		$GLOBALS['Supple'] = &$this;
	}
	
	/**
	 * Returns the database connection object.
	 * 
	 * @access public
	 * @return object The database connection.
	 */	 	 	 	 	 	
	function &getDatabaseConnection() { //Do we need & in from of get... here?
		return $this->Db;
	}

	/**
	 * Loads and constructs core Supple classes.
	 *
	 * These core classes provide all the methods that can be used to access
	 * the database. Perhaps the core classes can be replaced by pure actions
	 * but that would destroy the elegancy of the classes in favor of architecture.
	 *
	 * @access private
	 */
	function loadAndAssociateCoreClasses() {
		//Input class - sanitizes external input
		include_once ABSPATH.'st-system/includes/Input.class.php';
		$this->Input = new Input();
	
		//Handler class
		include_once ABSPATH.'/st-system/includes/Handler.class.php';
		
		//UserManagement class
		include_once ABSPATH.'/st-system/includes/UserManagement.class.php'; //Login/Logout
		$this->UserManagement = new UserManagement();
		$this->UserManagement->isLoggedIn(); //Currently need to run this.
		
		//Syntax parsing class
		include_once ABSPATH.'/st-system/includes/SyntaxParser.class.php';
		$this->SyntaxParser = new SyntaxParser();
		
		//Configuration Information----------------------
		include_once ABSPATH.'/st-system/includes/Settings.class.php';
		$this->Settings = new Settings($db);
		//-----------------------------------------------
		
		//Validation class
		include_once ABSPATH.'st-system/includes/Validation.class.php';
		$this->Validation = new Validation();
	}
	
	/**
	 * Parses URL fragment (the input to the wiki parameter) into individual parts.
	 * 
	 * @access private
	 * @param string $in_url_fragment The URL fragment from the wiki parameter (ie. SandBox/edit)
	 * @return array Hash of each part of the URL fragment.	 	 	 	 
	 *
	 */	 	 	
	function parseUrlFragment($in_url_fragment) {
		/**
		 * Extract pagename and handler from URL. From wikka.php.
		 */
		if (preg_match("#^(.+?)/(.*)$#", $in_url_fragment, $matches))
		{
			list(, $parsed['page'], $parsed['handler']) = $matches;
		}
		else if (preg_match("#^(.*)$#", $in_url_fragment, $matches))
		{
			list(, $parsed['page']) = $matches;
		}
		//Fix lowercase mod_rewrite bug: URL rewriting makes pagename lowercase. #135
		if ((strtolower($parsed['page']) == $parsed['page']) && (isset($_SERVER['REQUEST_URI']))) #38
		{
			$pattern = preg_quote($parsed['page'], '/');
			if (preg_match("/($pattern)/i", urldecode($Supple->Input->server('REQUEST_URI', true)), $match_url))
			{
				$parsed['page'] = $match_url[1];
			}
		}
		
		return $parsed;
	}
	
	/**
	 * Sets the page name we are operating on.
	 * 
	 * @access private
	 * @param string $in_pagename The page name we are concerned with (ie. SandBox).	 	 	 
	 *	 
	 */	 	
	function setPagename($in_pagename) {
		$this->pagename = $in_pagename;
	}
	
	function getPagename() {
		return $this->pagename;
	}
	
	/**
	 * Loads and runs specified handler.
	 *
	 */	 	 	
	function callHandler($in_handler) {
		//$this->handlerName = ucfirst(trim($in_handler));
		$this->handlerName = trim($in_handler); //No longer need the capitalization.
		
		//Ugly, so we should improve:
		include_once ABSPATH.'/st-system/handlers/'.$this->handlerName.'.php';

	}
	
	//The following are part of suppleText's new extensible features. Code based off of
	//Wordpress (http://www.wordpress.org).
	
	/**
	 * Scans the specified directory and includes all files in that directory. 
	 *
	 * This is intended for loading actions with each action file registering 
	 * themselves with registerAction().
	 *
	 * @access private
	 * @param string $inDir Directory of files to be loaded. NOTE: The directory should be input with the trailing slash.
	 * @param string $ext the extention of files to be loaded (defaults to '.php')
	 */
	function loadFilesInDirectory($inDir, $ext = '.php') {
		//Declare global here so that all of the action/included
		//files do not have to do so.
		global $xcomic;

		if ($handle = opendir($inDir)) 
		{
			//Need the !== so that directories called '0' don't break the loop
			while (false !== ($file = readdir($handle)))
			{
			    if (is_dir($inDir.$file))
			    {
                    if ($file != '.' && $file != '..')
                        $xcomic->loadFilesInDirectory($inDir.$file); // Recurse subdirectories
                    continue;
                }
				if (strpos($file, $ext) !== false) // Only php files, for safety.
				{
					include_once($inDir.$file);
				}
			}
			closedir($handle); 
		}
	}

	/**
	 * Registers a function with the core so that it can be called
	 * by a tag later during script execution. Useful for template
	 * purposes.
	 *
	 * @access public
	 * @param string $tag Short variable-like name to associate with the function such as 'getimagetag'.
	 * @param string $functionName Function (that exists) to be registered such as 'getImageTag()'.
	 */
	function registerAction($tag, $functionName) {
		/* For now, we allow overwriting:
		//Check for existing action. Match tags.
		foreach($this->actions as $actionName=>$actionFunction)
		{
			if($tag == $actionName)
			{
				//Error
				echo 'Error (already exists): '.$tag;
				return;
			}
		}
		*/
		
		//Add new action to actions array
		$this->actions[$tag] = $functionName;  

	}
	
	/**
	 * Executes the function associated with the tag.
	 *
	 * @access public
	 * @param string $tag Short variable-like name associated with a function such as 'getimagetag'.
	 * @param mixed $arg1,... Optional arguments that are associated with the tag.
	 * @return mixed Returns whatever the function associated to the tag returns. Could possibly be nothing.
	 */
	function doAction($tag) {
		$args = array_slice(func_get_args(), 1); //Get all arguments after the first one ($tag).

		//Check to see if tag exists
		if ((is_string($this->actions[$tag]) && !function_exists($this->actions[$tag])) ||
            (is_array($this->actions[$tag]) && !method_exists($this->actions[$tag][0], $this->actions[$tag][1])))
		{
		  echo "Error: Invalid action '$tag'";
		  return '';
		}

		//Call associated function
		return call_user_func_array($this->actions[$tag], $args);
		
	}
}

?>
