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
	var $id;
	
	function Show() {
		parent::Handler();
		
		//Add functions to be used by themes.
		//@todo Add a hook here so that plugin files can add theme functions too.
		$this->registerAction('page_content', 'getPageContent');
		$this->registerAction('page_tag', 'getPageTag');
		$this->registerAction('page_time', 'getPageTime');
		$this->registerAction('page_id', 'getPageId');
		
	}
	
	function setPageId($in_id) {
		$this->id = $in_id;
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
		//Possibly add AND clauses in the future.
		if(!empty($this->id))
		{
			$where = 'WHERE id = "'.mysql_real_escape_string($this->id).'"';
		}
		else if(!empty($this->pagename))
		{
			$where = 'WHERE tag = "'.mysql_real_escape_string($this->pagename).'"';
		}
		else
		{
			return; //Nothing can happen since page identifiers aren't set.
		}
		
		//Could probably do this a little better
		//We load the latest page first just so we can load information from the latest page.
		$this->page = $this->Db->get_row('SELECT * 
																FROM '.ST_PAGES_TABLE.' 
																'.$where.'
																LIMIT 1');
		
		if(!empty($this->id) && $this->page['id'] != $this->id)
		{
			$this->page = $this->Db->get_row('SELECT * 
														FROM '.ST_ARCHIVES_TABLE.' 
														'.$where.'
														LIMIT 1');
		}														
		
		//Then we check if we need to grab an older copy.														
		if(!empty($this->time) && (strcmp($this->page['time'], $this->time)!=0)) //The latest page time and the specified time are not the same.
		{
			$this->page = $this->Db->get_row('SELECT * 
																	FROM '.ST_ARCHIVES_TABLE.' 
																	'.$where.'
																	AND time = "'.mysql_real_escape_string($this->time).'" 
																	LIMIT 1');
		}
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
		if(empty($this->page['tag']))
		{
			return $this->pagename; //This fixes cases where the page does not exist yet.
		}
		
		return $this->page['tag'];
	}
	
	function getPageTime() {
		return $this->page['time'];
	}
}

?>