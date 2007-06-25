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

class Show extends Handler {
	var $pagename;
	var $time;
	
	function Show() {
		parent::Handler();
	}
	
	function run() {
		$this->setPagename($this->Supple->getPagename());
		$page = $this->loadPage();
		$this->loadTemplate();
	}
	
	function setPagename($in_pagename) {
		$this->pagename = $in_pagename;
	}
	
	//We won't bother with the time of the page for now.
	function setTime($in_time) {
		$this->time = $in_time;
	}
	
	/**
	 * loadPage gets the page information from the database.
	 * 
	 * @access private
	 * @return mixed Page table row containing content, author, etc.
	 */
	function loadPage() {
		//Could probably do this a little better
		return $this->Db->get_row('SELECT * 
																FROM '.ST_PAGES_TABLE.'
																WHERE tag = "'.mysql_real_escape_string($this->pagename).'" '.($time ? '
																	AND time = "'.mysql_real_escape_string($time).'"' : '
																	AND latest = "Y"').' 
																LIMIT 1');
	}
	
	function loadTemplate() {
		include ABSPATH.'/st-external/themes/default/show.tpl.php';
	}
}

?>