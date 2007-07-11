<?php
/**
 * Initializes and loads necessary script elements
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
 */ 

/**
 * Display PHP errors only or errors and warnings
 * @todo	make this configurable
 */
//error_reporting(E_ALL);
error_reporting (E_ALL ^ E_NOTICE);

/**
 * PHP Version Check. We should just make the version 5.2 and add PDO support, etc.
 */ 
if ( version_compare( '5.0', phpversion(), '>' ) ) { //Wikka has version 4.1.0, but we can make it 4.2 just to be safe since we might be using code from WP.
	die( 'Your server is running PHP version ' . phpversion() . ' but suppleText requires at least 5.0.' );
}

/**
 * Calculate page generation time.
 */
global $tstart;
$tstart = substr(microtime(),11).substr(microtime(),1,9); 

/**
 * Include main library if it exists.
 */
if (file_exists('st-system/Supple.class.php'))
{
	require_once('st-system/Supple.class.php');
}
else
{
	die('Supple class missing.'); #fatalerror
}

/**
 * Load config file
 */
//For now, just include the config file. Soon, we want to revamp the config
//options.
include('st-config.php'); //Maybe should ABSPATH this.
//define('SITEURL', '/suppleText');

//Installer check?

//Add language file here.


//Start session

//Load constants
require_once ABSPATH.'/st-system/includes/constants.php';


//Load database
//require_once(ABSPATH.'st-system/includes/Db.class.php');
require_once(ABSPATH.'/st-system/includes/db.php');
if(!isset($Stdb))
	{ $Stdb = new wpdb(DB_USER, DB_PASSWORD, DB_NAME, DB_HOST); }


?>
