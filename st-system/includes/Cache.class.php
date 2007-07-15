<?php
/**
 * For caching converted Wiki Syntax.
 * 
 * @author Michael Huynh (http://www.mikexstudios.com)
 * @package suppleText
 * @version $Id:$
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * 
 */
 
class Cache extends Show {
	
	var $have_cached_version = false;
	
	function _construct() {
		parent::__construct();
	}
	
	/**
	 * loadPage gets the page information from the database.
	 * but in this case, we want to check the cache database
	 * table first.
	 * 
	 * @access private
	 * @return mixed Page table row containing content, author, etc.
	 */
	function loadPage() {
		//Possibly add AND clauses in the future.
		if(!empty($this->id))
		{
			//$where = 'WHERE id = "'.mysql_real_escape_string($this->id).'"';
			$where = 'WHERE id = :id';
		}
		else if(!empty($this->pagename))
		{
			//$where = 'WHERE tag = "'.mysql_real_escape_string($this->pagename).'"';
			$where = '
				WHERE tag = :tag
				ORDER BY time DESC';
		}
		else
		{
			return; //Nothing can happen since page identifiers aren't set.
		}
		
		//Could probably do this a little better
		//We load the latest page first just so we can load information from the latest page.
		$sql = '
			SELECT * 
			FROM '.ST_CACHE_TABLE.' 
			'.$where.'
			LIMIT 1';
		
		$stmt = $this->Db->prepare($sql);
		#Ugly hack to work around PHP 5.2.1 upgrade which forces all bound Params to
		#exist in the query.
		if(!empty($this->id))
		{
			$stmt->bindParam(':id', $this->id, PDO::PARAM_INT);
		}
		else if(!empty($this->pagename))
		{
			$stmt->bindParam(':tag', $this->pagename, PDO::PARAM_STR);
		}
		$stmt->execute();
		$this->page = $stmt->fetch(PDO::FETCH_ASSOC);
														
		//Check if we got anything
		if(empty($this->page['body']) || !empty($this->time)) //We currently don't cache any old pages.
		{
			//echo 'here';
			$this->have_cached_version = false;
			//Call the non-cached version
			parent::loadPage();
		}
		else
		{
			//Otherwise, we have a cached version.
			$this->have_cached_version = true;
		}
	}
	
	/**
	 * @return boolean true if we have a cached version. false otherwise.
	 */
	function haveCachedVersion() {
		return $this->have_cached_version;
	}
	
	/**
	 * This method expects the page variable to already have been set.
	 */
	function storeCached() {
		//This is very similar to the page storage SQL of Edit.class.php. Perhaps
		//we can abstract it?
		$sql = '
			INSERT INTO '.ST_CACHE_TABLE.' 
			SET id = :id,
				tag = :tag,
				time = :time,
				body = :body,
				owner = "",
				user = :user, 
				note = :note 
				';
		$stmt = $this->Db->prepare($sql);
		$stmt->bindParam(':id', $this->page['id'], PDO::PARAM_INT);
		$stmt->bindParam(':tag', $this->page['tag'], PDO::PARAM_STR);
		$stmt->bindParam(':time', $this->page['time']);
		$stmt->bindParam(':user', $this->page['user'], PDO::PARAM_STR);
		$stmt->bindParam(':note', $this->page['note'], PDO::PARAM_STR);
		$stmt->bindParam(':body', $this->page['body'], PDO::PARAM_STR);
		$stmt->execute();
	}

}
 
?>
