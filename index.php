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
require('./st-system/initialize2.php');


//Get input
$wiki = $_GET['wiki'];

/**
 * Remove leading slash.
 */
$wiki = preg_replace("/^\//", "", $wiki);

/**
 * Extract pagename and handler from URL. This will be removed.
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
$Supple = new Supple($stdb);

//Theme helpers
require_once ABSPATH.'/st-system/includes/theme_helpers.php';

/** 
 * Run the engine.
 */
if (!isset($handler))
{
	$handler='show'; //Default to the show handler
}

if(empty($page))
{
	$page = 'HomePage'; //Default to the HomePage
}

$Supple->setPagename($page);
$Supple->callHandler($handler);



?>
