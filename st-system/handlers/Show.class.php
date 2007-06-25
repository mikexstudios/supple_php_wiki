<?php
/**
 * suppleText Show class
 * 
 * The purpose of this class is to load the page and provide output for templates.  
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
	var $page;
	
	function Show() {
		parent::Handler();
		
		//Add functions to be used by themes.
		//@todo Add a hook here so that plugin files can add theme functions too.
		$this->registerAction('page_content', 'getPageContent');
		$this->registerAction('page_tag',  'getPageTag');
		$this->registerAction('page_time',  'getPageTime');
		$this->registerAction('page_id',  'getPageId');
		
		$this->setPagename($this->Supple->getPagename());
		$this->loadPage();
	}
	
	function run() {
		//Code in run should *only* be for when Show is loaded directly, not if some
		//other class includes Show.
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
		$this->page = $this->Db->get_row('SELECT * 
																FROM '.ST_PAGES_TABLE.'
																WHERE tag = "'.mysql_real_escape_string($this->pagename).'" '.($time ? '
																	AND time = "'.mysql_real_escape_string($time).'"' : '
																	AND latest = "Y"').' 
																LIMIT 1');
	}
	
	function loadTemplate() {
		include get_theme_system_path('show.tpl.php');
	}
	
	function getPage() {
		return $this->page;
	}
	
	function getPageId() {
		return $this->page['id'];
	}
	
	function getPageContent() {
		return $this->page['body'];
	}
	
	function getPageTag() {
		return $this->page['tag'];
	}
	
	function getPageTime() {
		return $this->page['time'];
	}
}

?>