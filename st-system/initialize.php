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
 * PHP Version Check
 */ 
if ( version_compare( '4.2', phpversion(), '>' ) ) { //Wikka has version 4.1.0, but we can make it 4.2 just to be safe since we might be using code from WP.
	die( 'Your server is running PHP version ' . phpversion() . ' but suppleText requires at least 4.2.' );
}

/**
 * Calculate page generation time.
 */
function getmicrotime() {
	list($usec, $sec) = explode(' ', microtime());
	return ((float)$usec + (float)$sec);
}

$tstart = substr(microtime(),11).substr(microtime(),1,9); 
 
if (!function_exists('mysql_real_escape_string'))
{
/**
 * Escape special characters in a string for use in a SQL statement.
 * 
 * This function is added for back-compatibility with MySQL 3.23.
 * @param string $string the string to be escaped
 * @return string a string with special characters escaped
 */
	function mysql_real_escape_string($string)
	{
		return mysql_escape_string($string);
	}
}

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
 * Workaround for the amazingly annoying magic quotes.
 */
function magicQuotesWorkaround(&$a)
{
	if (is_array($a))
	{
		foreach ($a as $k => $v)
		{
			if (is_array($v))
			{
				magicQuotesWorkaround($a[$k]);
			}
			else
			{
				$a[$k] = stripslashes($v);
			}
		}
	}
}
set_magic_quotes_runtime(0);
if (get_magic_quotes_gpc())
{
	magicQuotesWorkaround($_POST);
	magicQuotesWorkaround($_GET);
	magicQuotesWorkaround($_COOKIE);
}

/**
 * Load config file
 */
//For now, just include the config file. Soon, we want to revamp the config
//options.
include('st-config.php');
define('SITEURL', '/suppleText');

//Installer check?

//Add language file here.


//Start session

//Temporary dump for constants
//DATABASE
define('ST_PAGES_TABLE', $table_prefix.'pages');
define('ST_USERS_TABLE', $table_prefix.'users');


//Load database
require_once(ABSPATH.'st-system/includes/Db.class.php');
if(!isset($Stdb))
	{ $Stdb = new wpdb(DB_USER, DB_PASSWORD, DB_NAME, DB_HOST); }



?>
