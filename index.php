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


define('ABSPATH', dirname(__FILE__).'/'); 
 
//Initialize needed script elements
require('./st-system/initialize.php');

/**
 * Create Supple object
 */
//print_r($suppleConfig);
$Supple = new Supple($Stdb);

//Get input. Since Supple calls Input, we know
//that any input variables are already sanitized.
$wiki = $Supple->Input->get('wiki', true); //We want XSS clean

/**
 * Remove leading slash.
 */
$wiki = preg_replace('/^\//', '', $wiki);

/**
 * Extract pagename and handler from URL. This will be removed.
 */
 
$parsed_url = $Supple->parseUrlFragment($wiki); 
$page = $parsed_url['page'];
$handler = $parsed_url['handler'];

//Theme helpers
require_once ABSPATH.'/st-system/includes/theme_helpers.php';

/** 
 * Run the engine.
 */
if (empty($handler))
{
	$handler='show'; //Default to the show handler
}

if(empty($page))
{
	$page = $Supple->Settings->getSetting('root_page'); //Default to the what is set in the database config for default page
}

//Perform some verification on $page and $handler:
//200 characters length is reasonable, right? We allow alpha-numerics, dashes, 
//underscores and colons (and other special chars).
if(!$Supple->Validation->max_length($page, 200) || !$Supple->Validation->alpha_special($page)) 
{
	die('The specified page does not exist.'); //TODO: Make error messages pretty.
}
//70 characters length is reasonable, right? Handlers must be alphanumeric.
if(!$Supple->Validation->max_length($handler, 70) || !$Supple->Validation->alpha_numeric($handler)) 
{
	die('The specified handler does not exist.');
}

$Supple->setPagename($page);
$Supple->callHandler($handler);

?>
