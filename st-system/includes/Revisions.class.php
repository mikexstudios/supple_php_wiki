<?php
/**
 * suppleText Revisions class
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

class Revisions extends Handler {
	var $pagename;
	var $revisions_data;
	
	function Revisions() {
		parent::Handler();
		
		//Add functions to be used by themes.
		//@todo Add a hook here so that plugin files can add theme functions too.
		$this->registerAction('revision_list', 'getRevisionList');
		
		//$this->setPagename($this->Supple->getPagename());
	}
	
	function setPagename($in_pagename) {
		$this->pagename = $in_pagename;
	}
	
	function getRevisionList() {
	
		$this->revisions_data[] = $this->Db->get_row('SELECT * 
																FROM '.ST_PAGES_TABLE.'
																WHERE tag = "'.mysql_real_escape_string($this->pagename).'"  
																LIMIT 1');

		$results = $this->Db->get_results('SELECT * 
																	FROM '.ST_ARCHIVES_TABLE.'
																	WHERE tag = "'.mysql_real_escape_string($this->pagename).'"
																	ORDER BY time DESC 
																	');
		if(!empty($results))
		{
			foreach($results as $each_row)
			{
				array_push($this->revisions_data, $each_row);
			}
		}
		
		return $this->revisions_data;
	}	

}

?>