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
	var $note;
	
	function Edit() {
		parent::Handler();
		
		//Edit always needs the Show class.
		include_once ABSPATH.'/st-system/handlers/Show.class.php';
		$this->Show = new Show();
		
		//Add functions to be used by themes.
		//@todo Add a hook here so that plugin files can add theme functions too.
		
		
		$this->setPagename($this->Supple->getPagename());
	}
	
	function run() {
		//Get info from POST. We should secure this:
		//Also see edit.php (Wikka's) for more info how to better do this.
		if($_POST['submit'] == 'Store')
		{
			// strip CRLF line endings down to LF to achieve consistency ... plus it saves database space.
			// Note: these codes must remain enclosed in double-quotes to work!
			$body = str_replace("\r\n", "\n", $_POST['body']);
			$body = preg_replace("/\n[ ]{4}/", "\n\t", $body);	// @@@ FIXME: misses first line and multiple sets of four spaces
		
			// we don't need to escape here, we do that just before display (i.e., treat note just like body!)
			$note = trim($_POST['note']);
			
			//Should check for overwriting.
		
			//DO THIS LATER: only save if new body differs from old body
			
			$this->setContent($body);
			$this->setEditnote($note);
			
			$this->storeChanges();
			redirect_page($this->pagename);
		}
		$this->loadTemplate();
	}
	
	function loadTemplate() {
		include get_theme_system_path('edit.tpl.php');
	}

	function setPagename($in_pagename) {
		$this->pagename = $in_pagename;
	}
	
	function setContent($in_content) {
		$this->content = $in_content;
	}
	
	function setEditnote($in_note) {
		$this->note = $in_note;
	}
	
	function showPreview() {
	
	}
	
	function storeChanges() {
			// set all other revisions to old
			//$this->Query('UPDATE '.$this->config['table_prefix'].'pages SET latest = "N" WHERE tag = "'.mysql_real_escape_string($tag).'"');
			
			//We send the old version into an archives table.
			$this->Db->query('INSERT INTO '.ST_ARCHIVES_TABLE.'
												SELECT *  
												FROM '.ST_PAGES_TABLE.' 
												WHERE tag = "'.mysql_real_escape_string($this->pagename).'"');
			//Then delete the entry from the pages table. Should add a check to see if
			//the copy was successful before deleting.
			$this->Db->query('DELETE FROM '.ST_PAGES_TABLE.'
												WHERE tag = "'.mysql_real_escape_string($this->pagename).'"
												LIMIT 1');
			
			// set all other revisions to old. This could be slow if we have tons of pages.
			//$this->Db->query('UPDATE '.ST_PAGES_TABLE.' 
			//									SET latest = "N" 
			//									WHERE tag = "'.mysql_real_escape_string($this->pagename).'"');

			$this->Db->query('INSERT INTO '.ST_PAGES_TABLE.' 
												SET tag = "'.mysql_real_escape_string($this->pagename).'", '.
																		'time = now(), '.
																		'owner = "", '.
																		'user = "", '.
																		'note = "'.mysql_real_escape_string($this->note).'", '.
																		'latest = "Y", '.
																		'body = "'.mysql_real_escape_string($this->content).'"');

	}

}

?>