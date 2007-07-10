<?php
/**
 * suppleText Edit class
 * 
 * The purpose of this class is to handle page editing functionality 
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

class Edit extends Handler {
	var $Show;
	var $pagename;
	var $content;
	var $author;
	var $note;
	
	function Edit() {
		parent::Handler();
		
		//Add functions to be used by themes.
		//@todo Add a hook here so that plugin files can add theme functions too.
		
	}

	function setPagename($in_pagename) {
		$this->pagename = $in_pagename;
	}
	
	function setContent($in_content) {
		$this->content = $in_content;
	}
	
	function setAuthor($in_author) {
		$this->author = $in_author;
	}
	
	function setEditnote($in_note) {
		$this->note = $in_note;
	}
	
	function showPreview() {
	
	}
	
	function storeChanges() {
			
			//We send the old version into an archives table.
			$sql = '
				INSERT INTO '.ST_ARCHIVES_TABLE.'
				SELECT *  
				FROM '.ST_PAGES_TABLE.' 
				WHERE tag = :tag';
			$stmt = $this->Db->prepare($sql);
			$stmt->bindParam(':tag', $this->pagename, PDO::PARAM_STR);
			$stmt->execute();

			//Then delete the entry from the pages table. Should add a check to see if
			//the copy was successful before deleting.
			$sql = 
				'DELETE FROM '.ST_PAGES_TABLE.'
				WHERE tag = :tag 
				LIMIT 1';
			$stmt = $this->Db->prepare($sql);
			$stmt->bindParam(':tag', $this->pagename, PDO::PARAM_STR);
			$stmt->execute();
	
			//Now we create the new version.
			$sql = '
				INSERT INTO '.ST_PAGES_TABLE.' 
				SET tag = :tag,
						time = now(),
						owner = "",
						user = :user, 
						note = :note, 
						body = :body';
			$stmt = $this->Db->prepare($sql);
			$stmt->bindParam(':tag', $this->pagename, PDO::PARAM_STR);
			$stmt->bindParam(':user', $this->author, PDO::PARAM_STR);
			$stmt->bindParam(':note', $this->note, PDO::PARAM_STR);
			$stmt->bindParam(':body', $this->content, PDO::PARAM_STR);
			$stmt->execute();

	}

}

?>