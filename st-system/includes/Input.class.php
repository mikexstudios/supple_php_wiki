<?php
/**
 * suppleText - slight modifications to the below code. Removed any
 * code referencing config and also removed log_message. Hard coded
 * one variable to true. Also change in line 171 to allow the @ char
 * in variable keys. Added session() method.
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
 * Input Class
 *
 * Pre-processes global input data for security
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Input
 * @author		Rick Ellis
 * @link		http://www.codeigniter.com/user_guide/libraries/input.html
 */
class Input {
	var $use_xss_clean	  = FALSE;
	var $ip_address		  	= FALSE;
	var $user_agent		  	= FALSE;
	var $allow_get_array	= true;
	
	/**
	 * Constructor
	 *
	 * Sets whether to globally enable the XSS processing
	 * and whether to allow the $_GET array
	 *
	 * @access	public
	 */	
	function Input()
	{		
		$this->_disable_magic_quotes();
		$this->_sanitize_globals();
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Disable Magic Quotes
	 * 
	 * Workaround for the amazingly annoying magic quotes.
	 * 
	 * @author Wikka Project
	 * @access private	 	 	 	 	 
	 */
	function _disable_magic_quotes() {
		set_magic_quotes_runtime(0);
		if (get_magic_quotes_gpc())
		{
			$this->_magic_quotes_workaround($_POST);
			$this->_magic_quotes_workaround($_GET);
			$this->_magic_quotes_workaround($_COOKIE);
			$this->_magic_quotes_workaround($_SESSION);
		}	 	
	}	 
	 
	function _magic_quotes_workaround(&$a) {
		if (is_array($a))
		{
			foreach ($a as $k => $v)
			{
				if (is_array($v))
				{
					$this->_magic_quotes_workaround($a[$k]);
				}
				else
				{
					$a[$k] = stripslashes($v);
				}
			}
		}
	}

	
	/**
	 * Sanitize Globals
	 *
	 * This function does the following:
	 *
	 * Unsets $_GET data (if query strings are not enabled)
	 *
	 * Unsets all globals if register_globals is enabled
	 *
	 * Standardizes newline characters to \n
	 *
	 * @access	private
	 * @return	void
	 */
	function _sanitize_globals()
	{
		// Unset globals. This is effectively the same as register_globals = off
		foreach (array($_GET, $_POST, $_COOKIE, $_SESSION) as $global)
		{
			if ( ! is_array($global))
			{
				global $global;
				$$global = NULL;
			}
			else
			{
				foreach ($global as $key => $val)
				{
					global $$key;
					$$key = NULL;
				}	
			}
		}

		// Is $_GET data allowed? If not we'll set the $_GET to an empty array
		if ($this->allow_get_array == FALSE)
		{
			$_GET = array();
		}
		else
		{
			if (is_array($_GET) AND count($_GET) > 0)
			{
				foreach($_GET as $key => $val)
				{
					$_GET[$this->_clean_input_keys($key)] = $this->_clean_input_data($val);
				}
			}
		}
		
		// Clean $_POST Data
		if (is_array($_POST) AND count($_POST) > 0)
		{
			foreach($_POST as $key => $val)
			{				
				$_POST[$this->_clean_input_keys($key)] = $this->_clean_input_data($val);
			}			
		}
	
		// Clean $_COOKIE Data
		if (is_array($_COOKIE) AND count($_COOKIE) > 0)
		{
			foreach($_COOKIE as $key => $val)
			{			
				$_COOKIE[$this->_clean_input_keys($key)] = $this->_clean_input_data($val);
			}	
		}
		
		// Clean $_SESSION Data
		if (is_array($_SESSION) AND count($_SESSION) > 0)
		{
			foreach($_SESSION as $key => $val)
			{			
				$_SESSION[$this->_clean_input_keys($key)] = $this->_clean_input_data($val);
			}	
		}
	}	
	
	// --------------------------------------------------------------------
	
	/**
	 * Clean Input Data
	 *
	 * This is a helper function. It escapes data and
	 * standardizes newline characters to \n
	 *
	 * @access	private
	 * @param	string
	 * @return	string
	 */	
	function _clean_input_data($str)
	{
		if (is_array($str))
		{
			$new_array = array();
			foreach ($str as $key => $val)
			{
				$new_array[$this->_clean_input_keys($key)] = $this->_clean_input_data($val);
			}
			return $new_array;
		}
		
		if ($this->use_xss_clean === TRUE)
		{
			$str = $this->xss_clean($str);
		}
		
		// Standardize newlines
		return preg_replace("/\015\012|\015|\012/", "\n", $str);
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Clean Keys
	 *
	 * This is a helper function. To prevent malicious users
	 * from trying to exploit keys we make sure that keys are
	 * only named with alpha-numeric text and a few other items.
	 *
	 * @access	private
	 * @param	string
	 * @return	string
	 */
	function _clean_input_keys($str)
	{	
		 if ( ! preg_match("/^[a-z0-9@:_\/-]+$/i", $str)) //suppleText edit: We allow the @ character for wikka legacy purposes.
		 {
			exit('Disallowed Key Characters.: '.$str);
		 }
	
		if ( ! get_magic_quotes_gpc())
		{
		   return addslashes($str);
		}
		
		return $str;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Fetch an item from the GET array
	 *
	 * @access	public
	 * @param	string
	 * @param	bool
	 * @return	string
	 */
	function get($index = '', $xss_clean = FALSE)
	{		
		if ( ! isset($_GET[$index]))
		{
			return FALSE;
		}

		if ($xss_clean === TRUE)
		{
			if (is_array($_GET[$index]))
			{
				foreach($_GET[$index] as $key => $val)
				{					
					$_GET[$index][$key] = $this->xss_clean($val);
				}
			}
			else
			{
				return $this->xss_clean($_GET[$index]);
			}
		}

		return $_GET[$index];
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Fetch an item from the POST array
	 *
	 * @access	public
	 * @param	string
	 * @param	bool
	 * @return	string
	 */
	function post($index = '', $xss_clean = FALSE)
	{		
		if ( ! isset($_POST[$index]))
		{
			return FALSE;
		}

		if ($xss_clean === TRUE)
		{
			if (is_array($_POST[$index]))
			{
				foreach($_POST[$index] as $key => $val)
				{					
					$_POST[$index][$key] = $this->xss_clean($val);
				}
			}
			else
			{
				return $this->xss_clean($_POST[$index]);
			}
		}

		return $_POST[$index];
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Fetch an item from the COOKIE array
	 *
	 * @access	public
	 * @param	string
	 * @param	bool
	 * @return	string
	 */
	function cookie($index = '', $xss_clean = FALSE)
	{
		if ( ! isset($_COOKIE[$index]))
		{
			return FALSE;
		}

		if ($xss_clean === TRUE)
		{
			if (is_array($_COOKIE[$index]))
			{
				$cookie = array();
				foreach($_COOKIE[$index] as $key => $val)
				{
					$cookie[$key] = $this->xss_clean($val);
				}
		
				return $cookie;
			}
			else
			{
				return $this->xss_clean($_COOKIE[$index]);
			}
		}
		else
		{
			return $_COOKIE[$index];
		}
	}

	// --------------------------------------------------------------------
	
	/**
	 * Fetch an item from the SERVER array
	 *
	 * @access	public
	 * @param	string
	 * @param	bool
	 * @return	string
	 */
	function server($index = '', $xss_clean = FALSE)
	{		
		if ( ! isset($_SERVER[$index]))
		{
			return FALSE;
		}

		if ($xss_clean === TRUE)
		{
			return $this->xss_clean($_SERVER[$index]);
		}
		
		return $_SERVER[$index];
	}


	// --------------------------------------------------------------------
	
	/**
	 * Fetch an item from the SESSION array
	 *
	 * @access	public
	 * @param	string
	 * @param	bool
	 * @return	string
	 */
	function session($index = '', $xss_clean = FALSE)
	{		
		if ( ! isset($_SESSION[$index]))
		{
			return FALSE;
		}

		if ($xss_clean === TRUE)
		{
			if (is_array($_SESSION[$index]))
			{
				foreach($_SESSION[$index] as $key => $val)
				{					
					$_SESSION[$index][$key] = $this->xss_clean($val);
				}
			}
			else
			{
				return $this->xss_clean($_SESSION[$index]);
			}
		}

		return $_SESSION[$index];
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Fetch the IP Address
	 *
	 * @access	public
	 * @return	string
	 */
	function ip_address()
	{
		if ($this->ip_address !== FALSE)
		{
			return $this->ip_address;
		}
		
		if ($this->server('REMOTE_ADDR') AND $this->server('HTTP_CLIENT_IP'))
		{
			 $this->ip_address = $_SERVER['HTTP_CLIENT_IP'];
		}
		elseif ($this->server('REMOTE_ADDR'))
		{
			 $this->ip_address = $_SERVER['REMOTE_ADDR'];
		}
		elseif ($this->server('HTTP_CLIENT_IP'))
		{
			 $this->ip_address = $_SERVER['HTTP_CLIENT_IP'];
		}
		elseif ($this->server('HTTP_X_FORWARDED_FOR'))
		{
			 $this->ip_address = $_SERVER['HTTP_X_FORWARDED_FOR'];
		}
		
		if ($this->ip_address === FALSE)
		{
			$this->ip_address = '0.0.0.0';
			return $this->ip_address;
		}
		
		if (strstr($this->ip_address, ','))
		{
			$x = explode(',', $this->ip_address);
			$this->ip_address = end($x);
		}
		
		if ( ! $this->valid_ip($this->ip_address))
		{
			$this->ip_address = '0.0.0.0';
		}
				
		return $this->ip_address;
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
		if ( ! preg_match( "/^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$/", $ip))
		{
			return FALSE;
		}
		
		$octets = explode('.', $ip);
		
		for ($i = 1; $i <= 4; $i++)
		{
			$octet = intval($octets[($i-1)]);
			if ($i === 1)
			{
				if ($octet > 223 OR $octet < 1)
					return FALSE;
			}
			elseif ($i === 4)
			{
				if ($octet < 1)
					return FALSE;
			}
			else
			{
				if ($octet > 254)
					return FALSE;
			}
		}
		
		return TRUE;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * User Agent
	 *
	 * @access	public
	 * @return	string
	 */
	function user_agent()
	{
		if ($this->user_agent !== FALSE)
		{
			return $this->user_agent;
		}
	
		$this->user_agent = ( ! isset($_SERVER['HTTP_USER_AGENT'])) ? FALSE : $_SERVER['HTTP_USER_AGENT'];
		
		return $this->user_agent;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * XSS Clean
	 *
	 * Sanitizes data so that Cross Site Scripting Hacks can be
	 * prevented.  This function does a fair amount of work but
	 * it is extremely thorough, designed to prevent even the
	 * most obscure XSS attempts.  Nothing is ever 100% foolproof,
	 * of course, but I haven't been able to get anything passed
	 * the filter.
	 *
	 * Note: This function should only be used to deal with data
	 * upon submission.  It's not something that should
	 * be used for general runtime processing.
	 *
	 * This function was based in part on some code and ideas I
	 * got from Bitflux: http://blog.bitflux.ch/wiki/XSS_Prevention
	 *
	 * To help develop this script I used this great list of
	 * vulnerabilities along with a few other hacks I've
	 * harvested from examining vulnerabilities in other programs:
	 * http://ha.ckers.org/xss.html
	 *
	 * @access	public
	 * @param	string
	 * @return	string
	 */
	function xss_clean($str, $charset = 'ISO-8859-1')
	{	
		/*
		 * Remove Null Characters
		 *
		 * This prevents sandwiching null characters
		 * between ascii characters, like Java\0script.
		 *
		 */
		$str = preg_replace('/\0+/', '', $str);
		$str = preg_replace('/(\\\\0)+/', '', $str);

		/*
		 * Validate standard character entities
		 *
		 * Add a semicolon if missing.  We do this to enable
		 * the conversion of entities to ASCII later.
		 *
		 */
		$str = preg_replace('#(&\#*\w+)[\x00-\x20]+;#u',"\\1;",$str);
		
		/*
		 * Validate UTF16 two byte encoding (x00)
		 *
		 * Just as above, adds a semicolon if missing.
		 *
		 */
		$str = preg_replace('#(&\#x*)([0-9A-F]+);*#iu',"\\1\\2;",$str);

		/*
		 * URL Decode
		 *
		 * Just in case stuff like this is submitted:
		 *
		 * <a href="http://%77%77%77%2E%67%6F%6F%67%6C%65%2E%63%6F%6D">Google</a>
		 *
		 * Note: Normally urldecode() would be easier but it removes plus signs
		 *
		 */	
		$str = preg_replace("/%u0([a-z0-9]{3})/i", "&#x\\1;", $str);
		$str = preg_replace("/%([a-z0-9]{2})/i", "&#x\\1;", $str);		
				
		/*
		 * Convert character entities to ASCII
		 *
		 * This permits our tests below to work reliably.
		 * We only convert entities that are within tags since
		 * these are the ones that will pose security problems.
		 *
		 */
		if (preg_match_all("/<(.+?)>/si", $str, $matches))
		{		
			for ($i = 0; $i < count($matches['0']); $i++)
			{
				$str = str_replace($matches['1'][$i],
									$this->_html_entity_decode($matches['1'][$i], $charset),
									$str);
			}
		}
		
		/*
		 * Not Allowed Under Any Conditions
		 */	
		$bad = array(
						'document.cookie'	=> '[removed]',
						'document.write'	=> '[removed]',
						'window.location'	=> '[removed]',
						"javascript\s*:"	=> '[removed]',
						"Redirect\s+302"	=> '[removed]',
						'<!--'				=> '&lt;!--',
						'-->'				=> '--&gt;'
					);
	
		foreach ($bad as $key => $val)
		{
			$str = preg_replace("#".$key."#i", $val, $str);   
		}
	
		/*
		 * Convert all tabs to spaces
		 *
		 * This prevents strings like this: ja	vascript
		 * Note: we deal with spaces between characters later.
		 *
		 */		
		$str = preg_replace("#\t+#", " ", $str);
	
		/*
		 * Makes PHP tags safe
		 *
		 *  Note: XML tags are inadvertently replaced too:
		 *
		 *	<?xml
		 *
		 * But it doesn't seem to pose a problem.
		 *
		 */		
		$str = str_replace(array('<?php', '<?PHP', '<?', '?>'),  array('&lt;?php', '&lt;?PHP', '&lt;?', '?&gt;'), $str);
	
		/*
		 * Compact any exploded words
		 *
		 * This corrects words like:  j a v a s c r i p t
		 * These words are compacted back to their correct state.
		 *
		 */		
		$words = array('javascript', 'vbscript', 'script', 'applet', 'alert', 'document', 'write', 'cookie', 'window');
		foreach ($words as $word)
		{
			$temp = '';
			for ($i = 0; $i < strlen($word); $i++)
			{
				$temp .= substr($word, $i, 1)."\s*";
			}
			
			$temp = substr($temp, 0, -3);
			$str = preg_replace('#'.$temp.'#s', $word, $str);
			$str = preg_replace('#'.ucfirst($temp).'#s', ucfirst($word), $str);
		}
	
		/*
		 * Remove disallowed Javascript in links or img tags
		 */		
		 $str = preg_replace("#<a.+?href=.*?(alert\(|alert&\#40;|javascript\:|window\.|document\.|\.cookie|<script|<xss).*?\>.*?</a>#si", "", $str);
		 $str = preg_replace("#<img.+?src=.*?(alert\(|alert&\#40;|javascript\:|window\.|document\.|\.cookie|<script|<xss).*?\>#si", "", $str);
		 $str = preg_replace("#<(script|xss).*?\>#si", "", $str);

		/*
		 * Remove JavaScript Event Handlers
		 *
		 * Note: This code is a little blunt.  It removes
		 * the event handler and anything up to the closing >,
		 * but it's unlikely to be a problem.
		 *
		 */		
		 $str = preg_replace('#(<[^>]+.*?)(onblur|onchange|onclick|onfocus|onload|onmouseover|onmouseup|onmousedown|onselect|onsubmit|onunload|onkeypress|onkeydown|onkeyup|onresize)[^>]*>#iU',"\\1>",$str);
	
		/*
		 * Sanitize naughty HTML elements
		 *
		 * If a tag containing any of the words in the list
		 * below is found, the tag gets converted to entities.
		 *
		 * So this: <blink>
		 * Becomes: &lt;blink&gt;
		 *
		 */		
		$str = preg_replace('#<(/*\s*)(alert|applet|basefont|base|behavior|bgsound|blink|body|embed|expression|form|frameset|frame|head|html|ilayer|iframe|input|layer|link|meta|object|plaintext|style|script|textarea|title|xml|xss)([^>]*)>#is', "&lt;\\1\\2\\3&gt;", $str);
		
		/*
		 * Sanitize naughty scripting elements
		 *
		 * Similar to above, only instead of looking for
		 * tags it looks for PHP and JavaScript commands
		 * that are disallowed.  Rather than removing the
		 * code, it simply converts the parenthesis to entities
		 * rendering the code un-executable.
		 *
		 * For example:	eval('some code')
		 * Becomes:		eval&#40;'some code'&#41;
		 *
		 */
		$str = preg_replace('#(alert|cmd|passthru|eval|exec|system|fopen|fsockopen|file|file_get_contents|readfile|unlink)(\s*)\((.*?)\)#si', "\\1\\2&#40;\\3&#41;", $str);
						
		/*
		 * Final clean up
		 *
		 * This adds a bit of extra precaution in case
		 * something got through the above filters
		 *
		 */	
		$bad = array(
						'document.cookie'	=> '[removed]',
						'document.write'	=> '[removed]',
						'window.location'	=> '[removed]',
						"javascript\s*:"	=> '[removed]',
						"Redirect\s+302"	=> '[removed]',
						'<!--'				=> '&lt;!--',
						'-->'				=> '--&gt;'
					);
	
		foreach ($bad as $key => $val)
		{
			$str = preg_replace("#".$key."#i", $val, $str);
		}
		
						
		return $str;
	}

	// --------------------------------------------------------------------

	/**
	 * HTML Entities Decode
	 *
	 * This function is a replacement for html_entity_decode()
	 *
	 * In some versions of PHP the native function does not work
	 * when UTF-8 is the specified character set, so this gives us
	 * a work-around.  More info here:
	 * http://bugs.php.net/bug.php?id=25670
	 *
	 * @access	private
	 * @param	string
	 * @param	string
	 * @return	string
	 */
	/* -------------------------------------------------
	/*  Replacement for html_entity_decode()
	/* -------------------------------------------------*/
	
	/*
	NOTE: html_entity_decode() has a bug in some PHP versions when UTF-8 is the
	character set, and the PHP developers said they were not back porting the
	fix to versions other than PHP 5.x.
	*/
	function _html_entity_decode($str, $charset='ISO-8859-1')
	{
		if (stristr($str, '&') === FALSE) return $str;
	
		// The reason we are not using html_entity_decode() by itself is because
		// while it is not technically correct to leave out the semicolon
		// at the end of an entity most browsers will still interpret the entity
		// correctly.  html_entity_decode() does not convert entities without
		// semicolons, so we are left with our own little solution here. Bummer.
	
		if (function_exists('html_entity_decode') && (strtolower($charset) != 'utf-8' OR version_compare(phpversion(), '5.0.0', '>=')))
		{
			$str = html_entity_decode($str, ENT_COMPAT, $charset);
			$str = preg_replace('~&#x([0-9a-f]{2,5})~ei', 'chr(hexdec("\\1"))', $str);
			return preg_replace('~&#([0-9]{2,4})~e', 'chr(\\1)', $str);
		}
		
		// Numeric Entities
		$str = preg_replace('~&#x([0-9a-f]{2,5});{0,1}~ei', 'chr(hexdec("\\1"))', $str);
		$str = preg_replace('~&#([0-9]{2,4});{0,1}~e', 'chr(\\1)', $str);
	
		// Literal Entities - Slightly slow so we do another check
		if (stristr($str, '&') === FALSE)
		{
			$str = strtr($str, array_flip(get_html_translation_table(HTML_ENTITIES)));
		}
		
		return $str;
	}

}
// END Input class
?>