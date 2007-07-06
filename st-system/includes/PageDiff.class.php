<?php
/**
 * suppleText PageDiff class
 * 
 * The purpose of this class is to provide differences between two different
 * versions of page content. 
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

class PageDiff extends Handler {
	var $Show;
	var $pagename;
	var $revision_a;
	var $revision_b;
	var $added;
	var $deleted;
	
	function PageDiff() {
		parent::Handler();
		
		include_once ABSPATH.'/st-system/includes/Show.class.php';
		$this->Show = new Show();
		
		//Add functions to be used by themes.
		//@todo Add a hook here so that plugin files can add theme functions too.

	}
	
	function setPageName($in_pagename) {
		$this->pagename = $in_pagename;
		$this->Show->setPagename($this->pagename);
	}
	
	function setRevisionA($in_revision) {
		$this->revision_a = $in_revision;
	}
	
	function setRevisionB($in_revision) {
		$this->revision_b = $in_revision;
	}
	
	/**
	 * Does not return anything.	
	 * @access private
	 */	 	
	function computeDifferences() {
		//Get page data from both revisions.
		$this->Show->setPageId($this->revision_a);
		$this->Show->loadPage();
		$revision_a_data = $this->Show->getPage();
		
		$this->Show->setPageId($this->revision_b);
		$this->Show->loadPage();
		$revision_b_data = $this->Show->getPage();
		
		// prepare bodies. Below code from Wikkawiki:
		$bodyA = explode("\n", $revision_a_data['body']);
		$bodyB = explode("\n", $revision_b_data['body']);

		$this->added   = array_diff($bodyA, $bodyB);
		$this->deleted = array_diff($bodyB, $bodyA);
	}
	
	function getAdded() {
		return implode("\n", $this->added);
	}
	
	function getDeleted() {
		return implode("\n", $this->deleted);
	}
	

}

?>