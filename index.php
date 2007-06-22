<?php
/**
 * suppleText main script
 * 
 * This file is called each time a request is made from the browser. Its
 * purpose is to initialize the script, call the supple core, and load
 * themes.  
 *  
 * @package suppleText
 * @version $Id: $
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
 
//Initialize needed script elements
require('./st-system/initialize.php');


//Get input
$wiki = $_GET['wiki'];

/**
 * Remove leading slash.
 */
$wiki = preg_replace("/^\//", "", $wiki);

/**
 * Extract pagename and handler from URL
 */
if (preg_match("#^(.+?)/(.*)$#", $wiki, $matches))
{
	list(, $page, $handler) = $matches;
}
else if (preg_match("#^(.*)$#", $wiki, $matches))
{
	list(, $page) = $matches;
}
//Fix lowercase mod_rewrite bug: URL rewriting makes pagename lowercase. #135
if ((strtolower($page) == $page) && (isset($_SERVER['REQUEST_URI']))) #38
{
	$pattern = preg_quote($page, '/');
	if (preg_match("/($pattern)/i", urldecode($_SERVER['REQUEST_URI']), $match_url))
	{
		$page = $match_url[1];
	}
}

/**
 * Create Supple object
 */
//print_r($suppleConfig);
$Supple = new Supple($stdb, $suppleConfig);

/** 
 * Run the engine.
 */
if (!isset($handler))
{
	$handler='';
}

//This is wikka's hack to obtain output from the wakka class. If we modify the
//core wakka class, we won't need this anymore.
ob_start();
$Supple->Run($page, $handler);
$content =  ob_get_contents();
ob_end_clean();

//Call a fixed theme for now unless we can implement a good theme system. 
require('./st-external/themes/default/index.php');  

?>
