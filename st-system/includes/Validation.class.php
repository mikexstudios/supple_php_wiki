<?php
/**
 * suppleText - removed a big chunk of the class since it depended
 *  on CI. Perhaps manually edit the code to add it back later.
 *  Also added alpha_special() method. 
 * 
 * CodeIgniter
 *
 * An open source application development framework for PHP 4.3.2 or newer
 *
 * @package		CodeIgniter
 * @author		Rick Ellis
 * @copyright	Copyright (c) 2006, EllisLab, Inc.
 * @license		http://www.codeignitor.com/user_guide/license.html
 * @link		http://www.codeigniter.com
 * @since		Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * Validation Class
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Validation
 * @author		Rick Ellis
 * @link		http://www.codeigniter.com/user_guide/libraries/validation.html
 */
class Validation {

	/**
	 * Constructor
	 *
	 */	
	function Validation()
	{	
	}

	// --------------------------------------------------------------------
	
	/**
	 * Minimum Length
	 *
	 * @access	public
	 * @param	string
	 * @return	bool
	 */	
	function min_length($str, $val)
	{
		if ( ! is_numeric($val))
		{
			return FALSE;
		}
	
		return (strlen($str) < $val) ? FALSE : TRUE;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Max Length
	 *
	 * @access	public
	 * @param	string
	 * @return	bool
	 */	
	function max_length($str, $val)
	{
		if ( ! is_numeric($val))
		{
			return FALSE;
		}
	
		return (strlen($str) > $val) ? FALSE : TRUE;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Exact Length
	 *
	 * @access	public
	 * @param	string
	 * @return	bool
	 */	
	function exact_length($str, $val)
	{
		if ( ! is_numeric($val))
		{
			return FALSE;
		}
	
		return (strlen($str) != $val) ? FALSE : TRUE;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Valid Email
	 *
	 * @access	public
	 * @param	string
	 * @return	bool
	 */	
	function valid_email($str)
	{
		return ( ! preg_match("/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix", $str)) ? FALSE : TRUE;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Validate IP Address
	 *
	 * @access	public
	 * @param	string
	 * @return	string
	 */
	function valid_ip($ip)
	{
		return ( ! preg_match( "/^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$/", $ip)) ? FALSE : TRUE;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Alpha
	 *
	 * @access	public
	 * @param	string
	 * @return	bool
	 */		
	function alpha($str)
	{
		return ( ! preg_match("/^([-a-z])+$/i", $str)) ? FALSE : TRUE;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Alpha-numeric
	 *
	 * @access	public
	 * @param	string
	 * @return	bool
	 */	
	function alpha_numeric($str)
	{
		return ( ! preg_match("/^([-a-z0-9])+$/i", $str)) ? FALSE : TRUE;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Alpha-numeric with underscores and dashes
	 *
	 * @access	public
	 * @param	string
	 * @return	bool
	 */	
	function alpha_dash($str)
	{
		return ( ! preg_match("/^([-a-z0-9_-])+$/i", $str)) ? FALSE : TRUE;
	}
	
	/**
	 * Alpha-numeric with underscores, dashes, and colon (and other special
	 * characters).	 
	 *
	 * @access	public
	 * @param	string
	 * @return	bool
	 */	
	function alpha_special($str)
	{
		return ( ! preg_match("/^([-a-z0-9_\'\:-])+$/i", $str)) ? FALSE : TRUE;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Numeric
	 *
	 * @access	public
	 * @param	int
	 * @return	bool
	 */	
	function numeric($str)
	{
		return ( ! ereg("^[0-9\.]+$", $str)) ? FALSE : TRUE;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Is Numeric
	 *
	 * @access	public
	 * @param	string
	 * @return	bool
	 */	
	function is_numeric($str)
	{
		return ( ! is_numeric($str)) ? FALSE : TRUE;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Convert PHP tags to entities
	 *
	 * @access	public
	 * @param	string
	 * @return	string
	 */	
	function encode_php_tags($str)
	{
		$_POST[$this->_current_field] = str_replace(array('<?php', '<?PHP', '<?', '?>'),  array('&lt;?php', '&lt;?PHP', '&lt;?', '?&gt;'), $str);
	}

}
// END Validation Class
?>