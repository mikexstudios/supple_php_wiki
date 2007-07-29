<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @author Zelaza (http://codeigniter.com/forums/viewreply/265762/)
 */ 
class Sessions_model extends Model {

	function Sessions_model() {
	    parent::Model();    
	}
	
	function get_all_sessions() {
	   return $this->db->get(ST_SESSIONS_TABLE); //Uh, this could be very ugly if table is big.
	}
	
	function get_session($id) {
	    return $this->db->getwhere(ST_SESSIONS_TABLE, array('session_id' => $id));
	}
	
	function delete_session($id) {
	    return $this->db->delete(ST_SESSIONS_TABLE, array('session_id' => $id));
	}
	
}

?>
