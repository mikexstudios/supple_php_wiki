<?php
/**
 * suppleText main script
 * 
 * This file is called each time a request is made from the browser. Its
 * purpose is to initialize the script, call the supple core, and load
 * themes.  
 *  
 * @package suppleText
 * @version $Id: $
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

/**
 * The Supple core.
 *
 * This class contains all the core methods used to run Supple.
 * @name Supple
 * @package Supple
 *
 */
class Supple
{
	/**
	 * Hold the wikka config.
	 * @access private
	 */
	var $config = array();
	/**
	 * Hold the connection-link to the database.
	 * @access private
	 */
	var $dblink;
	var $page;
	/**
	 * Hold the name of the current page.
	 *
	 * @access	private
	 */
	var $tag;
	var $queryLog = array();
	/**
	 * Hold the interWiki List.
	 */
	var $interWiki = array();
	/**
	 * Hold the Wikka version.
	 */
	var $VERSION;
	var $cookies_sent = false;
	/**
	 * $pageCache. 
	 * This array stores cached pages. Keys are page names (tag) or page id (prepended with /#) and values are the 
	 * page structure. See {@link Wakka::CachePage()}
	 * @var array
	 * @access public
	 */
	var $pageCache;
	/**
	 * $do_not_send_anticaching_headers. 
	 * If this value is set to true, Anti-caching HTTP headers won't be added.
	 * @var boolean
	 * @access public
	 */
	var $do_not_send_anticaching_headers = false;
	/**
	 * $additional_headers.
	 * Array one may use to add customized tags inside <head>, like additional stylesheet, customized javascript, ...
	 * Handlers and/or actions implementing this variable are responsible for sanitizing values passed to it.
	 * Use {@link Wakka::AddCustomHeader()} to populate this array.
	 * @var array
	 * @access public
	 */
	var $additional_headers = array();
	/**
	 * Title of the page to insert in the <title> tag.
	 * 
	 * @var string
	 * @access public
	 */
	var $page_title = '';
	
	/**
	 * Holds the database connection.
	 * @access private	 
	 */	 	
	var $db;

	/**
	 * Constructor
	 */
	function Supple(&$dbc, $config)
	{
		$this->db = &$dbc;
		$this->config = $config;
	}

	/**
	 * Misc methods
	 */
	/**
	 * Buffer the output from an included file.
	 *
	 * @param	string $filename mandatory: name of the file to be included
	 * @param	string $notfoundText optional: optional text to be returned if the file was not found. default: ""
	 * @param	string $vars optional: vars to be passed to the file. default: ""
	 * @param	string $path optional: path to the file. default: ""
	 * @return	string in case the file has some output or there was a notfoundText, boolean FALSE otherwise
	 * @todo	make the function return only one type of variable
	 */
	function IncludeBuffered($filename, $not_found_text = '', $vars = '', $path = '')
	{
		//echo $filename;
		if ($path)
		{
			$dirs = explode(':', $path);
		}
		else
		{
			$dirs = array("");
		}
		//print_r($dirs);
		foreach($dirs as $dir)
		{
			if ($dir)
			{
				$dir .= "/";
			}
			$fullfilename = $dir.$filename;
			if (file_exists($fullfilename))
			{ 
				if (is_array($vars))
				{
					extract($vars);
				}
				ob_start();
				include($fullfilename);
				$output = ob_get_contents();
				ob_end_clean();
				return $output;
			}
		}
		if ($not_found_text)
		{
			return $not_found_text;
		}
		else
		{
			return false;
		}
	}

	/**
	 * Variable-related methods
	 * 
	 * @todo decide if we need these methods
	 */
	/**
	 * Get the handler used on the page.
	 *
	 * @return string name of the method.
	 */
	function GetHandler()
	{
		return $this->handler;
	}
	/**
	 * Get the value of a given value from the wikka config.
	 *
	 * @param	$name mandatory: name of a key in the config array
	 */
	function GetConfigValue($name)
	{
		return (isset($this->config[$name])) ? $this->config[$name] : null;
	}

	/**
	 * Page-related methods
	 */
	/**
	 * LoadPage loads the page whose name is $tag.
	 * 
	 * If parameter $time is provided, LoadPage returns the page as it was at that exact time.
	 * If parameter $time is not provided, it returns the page as its latest state.
	 * LoadPage and LoadPageById remember the page tag or page id they've queried by caching them,
	 * so, these methods try first to retrieve data from cache if available.
	 * @uses	Wakka:LoadSingle()
	 * @uses	Wakka:CachePage()
	 * @uses	Wakka:CacheNonExistentPage()
	 * @uses	Wakka:GetCachedPage()
	 * @param string $tag 
	 * @param string $time 
	 * @param int $cache 
	 * @access public
	 * @return mixed $page
	 */
	function LoadPage($tag, $time = '', $cache = 1)
	{
		$page = null;
		// load page
		if (!$page)
		{
			//$page = $this->LoadSingle('SELECT * FROM '.$this->config['table_prefix'].'pages WHERE tag = "'.mysql_real_escape_string($tag).'" '.($time ? 'AND time = "'.mysql_real_escape_string($time).'"' : 'AND latest = "Y"').' LIMIT 1');
			
			$page = $this->db->get_row('SELECT * 
																	FROM '.ST_PAGES_TABLE.'
																	WHERE tag = "'.mysql_real_escape_string($tag).'" '.($time ? '
																		AND time = "'.mysql_real_escape_string($time).'"' : '
																		AND latest = "Y"').' 
																	LIMIT 1');
			/*
			echo 'SELECT * 
																	FROM '.$this->config['table_prefix'].'pages 
																	WHERE tag = "'.mysql_real_escape_string($tag).'" '.($time ? '
																		AND time = "'.mysql_real_escape_string($time).'"' : '
																		AND latest = "Y"').' 
																	LIMIT 1';
			*/
			//echo $page;
		}
		return $page;
	}

	function SetPage($page)
	{
		$this->page = $page;
		if ($this->page['tag'])
		{
			$this->tag = $this->page['tag'];
		}
	}
	/**
	 * LoadPageById loads a page whose id is $id.
	 * 
	 * If the parameter $cache is true, it first tries to retrieve it from cache.
	 * If the page id was not retrieved from cache, then use sql and cache the page.
	 * @param int $id Id of the page to load.
	 * @param boolean $cache if true, an attempt to retrieve from cache will be made first.
	 * @access public
	 * @return mixed a page identified by $id
	 */
	function LoadPageById($id, $cache = true) 
	{ 
		// It first tries to retrieve from cache.
		if ($cache)
		{
			$page = $this->GetCachedPageById($id);
			if ((is_string($page)) && ($page == 'cached_nonexistent_page'))
			{
				return null;
			}
			if (is_array($page))
			{
				return ($page);
			}
		}
		// If the page id was not retrieved from cache, then use sql and cache the page.
		$page = $this->LoadSingle('SELECT * FROM '.$this->config['table_prefix'].'pages WHERE id = "'.mysql_real_escape_string($id).'" LIMIT 1'); 
		if ($page)
		{
			$this->CachePage($page);
		}
		else
		{
			$this->CacheNonExistentPage('/#'.$id);
		}
		return $page;
	}


	/**
	 * Save a page.
	 *
	 * @uses	Wakka::GetPingParams()
	 * @uses	Wakka::GetUser()
	 * @uses	Wakka::GetUserName()
	 * @uses	Wakka::HasAccess()
	 * @uses	Wakka::LoadPage()
	 * @uses	Wakka::Query()
	 * @uses	Wakka::WikiPing()
	 */
	function SavePage($tag, $body, $note)
	{
		// get current user
		$user = $this->GetUserName();

		// TODO: check write privilege	??? is this still a TODO??
		//if ($this->HasAccess('write', $tag))
		if(true)
		{
			// is page new?
			if (!$oldPage = $this->LoadPage($tag))
			{
				// current user is owner if user is logged in, otherwise, no owner.
				if ($this->GetUser())
				{
					$owner = $user;
				}
			}
			else
			{
				// aha! page isn't new. keep owner!
				$owner = $oldPage['owner'];
			}

			// set all other revisions to old
			$this->Query('UPDATE '.$this->config['table_prefix'].'pages SET latest = "N" WHERE tag = "'.mysql_real_escape_string($tag).'"');

			// add new revision
			$this->Query('INSERT INTO '.$this->config['table_prefix'].'pages SET '.
				'tag = "'.mysql_real_escape_string($tag).'", '.
				'time = now(), '.
				'owner = "'.mysql_real_escape_string($owner).'", '.
				'user = "'.mysql_real_escape_string($user).'", '.
				'note = "'.mysql_real_escape_string($note).'", '.
				'latest = "Y", '.
				'body = "'.mysql_real_escape_string($body).'"');
	
			/*
			if ($pingdata = $this->GetPingParams($this->config['wikiping_server'], $tag, $user, $note))
			{
				$this->WikiPing($pingdata);
			}
			*/
		}
	}

	/**
	 * Cookie related functions.
	 */
	/**
	 * Set a temporary Cookie.
	 */
	function SetSessionCookie($name, $value)
	{
		SetCookie($name.$this->config['wiki_suffix'], $value, 0, '/');
		$_COOKIE[$name.$this->config['wiki_suffix']] = $value;
		$this->cookies_sent = TRUE;
	}
	/**
	 * Set a Cookie.
	 */
	function SetPersistentCookie($name, $value)
	{
		SetCookie($name.$this->config['wiki_suffix'], $value, time() + 90 * 24 * 60 * 60, '/');
		$_COOKIE[$name.$this->config['wiki_suffix']] = $value;
		$this->cookies_sent = TRUE;
	}
	/**
	 * Delete a Cookie.
	 */
	function DeleteCookie($name) {
		SetCookie($name.$this->config['wiki_suffix'], '', 1, '/');
		$_COOKIE[$name.$this->config['wiki_suffix']] = '';
		$this->cookies_sent = TRUE;
		}
	/**
	 * Get the value of a Cookie.
	 */
	function GetCookie($name)
	{
		if (isset($_COOKIE[$name.$this->config['wiki_suffix']]))
		{
			return $_COOKIE[$name.$this->config['wiki_suffix']];
		}
		else
		{
			return FALSE;
		}
	}

	/**
	 * HTTP/REQUEST/LINK RELATED
	 */
	/**
	 * Store a message in the session to be displayed after redirection.
	 *
	 * @param	string $message text to be stored
	 */
	function SetRedirectMessage($message)
	{
		$_SESSION['redirectmessage'] = $message;
	}
	/**
	 * Get a message, if one was stored before redirection.
	 *
	 * @return string either the text of the message or an empty string.
	 */
	function GetRedirectMessage()
	{
		$message = '';
		if (isset($_SESSION['redirectmessage']))
		{
			$message = $_SESSION['redirectmessage'];
			$_SESSION['redirectmessage'] = '';
		}
		return $message;
	}
	/**
	 * Perform a redirection to another page.
	 *
	 * On IIS server, and if the page has sent any cookies, the redirection must not be performed
	 * by using the 'Location:' header. We use meta http-equiv OR javascript OR link (Credits MarceloArmonas).
	 * @author {@link http://wikkawiki.org/DotMG Mahefa Randimbisoa} (added IIS support)
	 * @access	public
	 * @since	Wikka 1.1.6.2
	 *
	 * @param	string	$url optional: destination URL; if not specified redirect to the same page.
	 * @param	string	$message optional: message that will show as alert in the destination URL
	 */
	function Redirect($url='', $message='')
	{
		if ($message != '')
		{
			$_SESSION['redirectmessage'] = $message;
		}
		$url = ($url == '' ) ? $this->config['base_url'].$this->tag : $url;
		if ((eregi('IIS', $_SERVER['SERVER_SOFTWARE'])) && ($this->cookies_sent))
		{
			@ob_end_clean(); 
			$redirlink = '<a href="'.$this->Href($url).'">'.REDIR_LINK_DESC.'</a>';
			die('<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en"><head><title>'.sprintf(REDIR_DOCTITLE,$this->Href($url)).'</title>'.
'<meta http-equiv="refresh" content="0; url=\''.$url.'\'" /></head><body><div><script type="text/javascript">window.location.href="'.$url.'";</script>'.
'</div><noscript>'.sprintf(REDIR_MANUAL_CAPTION,$redirlink).'</noscript></body></html>');
		}
		else
		{
			session_write_close(); # Always use session_write_close() before any header('Location: ...')
			header('Location: '.$url);
		}
		exit;
	}

	/**
	 * Return the full URL to a page/handler.
	 *
	 * @uses	Wakka::MiniHref()
	 */
	function Href($handler = '', $tag = '', $params = '')
	{
		$href = $this->config['base_url'];
		//Currently just returning the href which is wrong, but lets the script function.
		//We will remove this whole function soon.
		return $href;
	}

	/**
	 * ACTIONS / PLUGINS
	 */
	/**
	 * Handle the call to an action.
	 *
	 * @uses	Wakka::IncludeBuffered()
	 * @uses	Wakka::StartLinkTracking()
	 * @uses	Wakka::StopLinkTracking()
	 * 
	 * @todo	move regex to central regex library
	 */
	function Action($action, $forceLinkTracking = 0)
	{
		$action = trim($action);
		$vars=array();

		// search for parameters separated by spaces or newlines - #371
		if (preg_match('/\s/', $action))
		{
			// parse input for action name and parameters
			preg_match('/^([A-Za-z0-9]*)\s+(.*)$/s', $action, $matches);
			// extract $action and $vars_temp ("raw" attributes)
			list(, $action, $vars_temp) = $matches;

			if ($action)
			{
				// match all attributes (key and value)
				preg_match_all('/([A-Za-z0-9]*)=("|\')(.*)\\2/U', $vars_temp, $matches);

				// prepare an array for extract() to work with (in $this->IncludeBuffered())
				if (is_array($matches))
				{
					for ($a = 0; $a < count($matches[0]); $a++)
					{
						$vars[$matches[1][$a]] = $matches[3][$a];
					}
				}
				$vars['wikka_vars'] = trim($vars_temp); // <<< add the buffered parameter-string to the array
			}
			else
			{
				return '<em class="error">'.ACTION_UNKNOWN_SPECCHARS.'</em>'; // <<< the pattern ([A-Za-z0-9])\s+ didn't match!
			}
		}
		if (!preg_match('/^[a-zA-Z0-9]+$/', $action))
		{
			return '<em class="error">'.ACTION_UNKNOWN_SPECCHARS.'</em>';
		}
		if (!$forceLinkTracking)
		{
			/**
			 * @var boolean holds previous state of LinkTracking before we StopLinkTracking(). It will then be used to test if we should StartLinkTracking() or not. 
			 */
			$link_tracking_state = isset($_SESSION['linktracking']) ? $_SESSION['linktracking'] : 0; #38
			$this->StopLinkTracking();
		}
		$result = $this->IncludeBuffered(strtolower($action).'/'.strtolower($action).'.php', '<em class="error">'.sprintf(ACTION_UNKNOWN,$action).'</em>', $vars, $this->config['action_path']);
		if ($link_tracking_state)
		{
			$this->StartLinkTracking();
		}
		return $result;
	}
	/**
	 * Use a handler on the current page.
	 *
	 * @uses	Wakka::IncludeBuffered()   
	 * @todo	 use templating class;
	 * @todo	 use handler config files; #446
	 */
	function Handler($handler)
	{
		if (strstr($handler, '/'))
		{
			$handler = substr($handler, strrpos($handler, '/')+1);
		}
		//if (!$handler = $this->page['handler']) $handler = 'page';
		$handler_location = $handler.'/'.$handler.'.php';
		$handler_location_disp = '<tt>'.$handler_location.'</tt>';
		//$handler_location = 'st-system/actions/'.$hander_location;
		return $this->IncludeBuffered($handler_location, '<div class="page"><em class="error">'.sprintf(HANDLER_UNKNOWN,$handler_location_disp).'</em></div>', '', $this->config['handler_path']);
	}

	//REMOVED ADDITIONAL HEADER INSERT FUNCTION. SHOULD BE IN TEMPLATE.
	
	/**
	 * Render a string using a given formatter or the standard Wakka by default.
	 *
	 * @uses	Config::$wikka_formatter_path
	 * @uses	Wakka::IncludeBuffered()
	 * @param	string $text the source text to format
	 * @param string $formatter the name of the formatter. This name is linked to a file with the same name, located in the folder
		*  specified by {@link Config::$wikka_formatter_path}, and with extension .php; which is called to process the text $text
	 * @param string $format_option a comma separated list of string options, in the form of 'option1;option2;option3'
	 */
	function Format($text, $formatter='wakka', $format_option='')
	{
		return $this->IncludeBuffered($formatter.'.php', '<em class="error">'.sprintf(FORMATTER_UNKNOWN,$formatter).'</em>', compact('text', 'format_option'), $this->GetConfigValue('wikka_formatter_path'));
	}

	/**
	 * USERS
	 */
	/**
	 * Load a given user.
	 *
	 * <p>If a second parameter $password is supplied, this method checks if this password is valid, thus a false return value would mean
	 * nonexistent user or invalid password. Note that this parameter is the <strong>hashed value</strong> of the password usually typed in 
	 * by user, and not the password itself.</p>
	 * <p>If this parameter is not supplied, it checks only for existence of the username, and returns an array containing all information
	 * about the given user if it exists, or a false value. In this latter case, result is cached in $this->specialCache in order to 
	 * improve performance.</p>
	 *
	 * @uses	Wakka::LoadSingle()
	 * @param	string $name mandatory: name of the user
	 * @param	string $password optional: password of the user. default: 0 (=none)
	 * @return	array the data of the user, or false if non-existing user or invalid password supplied.
	 */
	function LoadUser($name, $password = 0) 
	{
		if (($password === 0) && (isset($this->specialCache['user'][strtolower($name)])))
		{
			return ($this->specialCache['user'][strtolower($name)]);
		}
		//$user = $this->LoadSingle("select * from ".$this->config['table_prefix']."users where name = '".mysql_real_escape_string($name)."' limit 1");
		$user = $this->db->get_var("SELECT * 
																FROM ".ST_USERS_TABLE."
																WHERE name = '".mysql_real_escape_string($name)."'
																LIMIT 1");
		if ($password !== 0)
		{
			$pwd = md5($user['challenge'].$user['password']);
			if ($password != $pwd)
			{
				return (null);
			}
		}
		else
		{
			$this->specialCache['user'][strtolower($name)] = $user;
		}
		return ($user);
	}
	/**
	 * Load all users registered at the wiki.
	 *
	 * @uses	Wakka::LoadAll()
	 * @return	array contains all users data
	 */
	function LoadUsers()
	{
		return $this->LoadAll('SELECT * FROM '.$this->config['table_prefix'].'users ORDER BY name');
	}
	/**
	 * Get the name or address of the current user.
	 *
	 * If the user is not logged-in, the host name is only looked up if enabled
	 * in the config (since it can lead to long page generation times).
	 * Set 'enable_user_host_lookup' in wikka.config.php to 1 to do the look-up.
	 * Otherwise the ip-address is used.
	 *
	 * @uses	Wakka::GetUser()
	 * @return	string name/ip-adress/host-name of the current user
	 */
	function GetUserName()
	{
		if ($user = $this->GetUser())
		{
			$name = $user['name'];
		}
		else
		{
			$ip = $_SERVER['REMOTE_ADDR'];
			if ($this->config['enable_user_host_lookup'] == 1)
			{
				$name = gethostbyaddr($ip) ? gethostbyaddr($ip) : $ip;
			}
			else
			{
				$name = $ip;
			}
		}
		return $name;
	}
	/**
	 * Get the name of the current user if he is logged in.
	 *
	 * @return string/NULL either a string with the user name or NULL
	 */
	function GetUser()
	{
		return (isset($_SESSION['user'])) ? $_SESSION['user'] : NULL;
	}
	/**
	 * Log-in a given user.
	 *
	 * User data are stored in the session, whereas name and password are stored in a cookie.
	 * 
	 * @uses	Wakka::SetPersistentCookie()
	 * @uses	Wakka::Query()
	 * @param	array $user mandatory: must contain the userdata
	 * @todo	name should be made made consistent with opposite function LogoutUser()
	 */
	function SetUser($user)
	{
		$_SESSION['user'] = $user;
		$this->SetPersistentCookie('user_name', $user['name']);
		$user['challenge'] = dechex(crc32(rand()));
		$this->Query('UPDATE '.$this->config['table_prefix'].'users set `challenge` = "'.$user['challenge'].'" WHERE name = "'.mysql_real_escape_string($user['name']).'"');
		$this->SetPersistentCookie('pass', md5($user['challenge'].$user['password']));
	}
	/**
	 * Log-out the current user.
	 *
	 * User data are removed from the session and name and password cookies are deleted.
	 * 
	 * @uses	Wakka::DeleteCookie()
	 * @uses	Wakka::GetUserName()
	 * @uses	Wakka::Query()
	 * @todo	name should be made made consistent with opposite function SetUser()
	 */
	function LogoutUser()
	{
		// Choosing an arbitrary challenge that the DB server only knows.
		$user['challenge'] = dechex(crc32(rand()));
		$this->Query('UPDATE '.$this->config['table_prefix'].'users set `challenge` = "'.$user['challenge'].'" WHERE name = "'.mysql_real_escape_string($this->GetUserName()).'"');
		$_SESSION['user'] = '';
		unset($_SESSION['show_comments']);
		$this->DeleteCookie('user_name');
		$this->DeleteCookie('pass');
	}

	//REMOVED USER COMMENT FUNC



	//REMOVED COMMENTS FUNCTIONS

	/**
	 * Updates modified table fields in bulk. 
	 * 
	 * WARNING: Do not add, delete, or reorder records or fields in
	 * 	queries prior to calling this function!!
	 * @uses    Query()
	 * @param	string $tablename mandatory: Table to modify
	 * @param	string $keyfield mandatory: Field name of primary key
	 * @param	resource $old_res mandatory: Old (original) resource
	 *			as generated by mysql_query
	 * @param	resource $new_res mandatory: New (modified) resource
	 *			originally created as a copy of $old_res
	 * @todo    Does not currently handle deletions or insertions of
	 *			records or fields.
	 */
	 function Update($tablename, $keyfield, $old_res, $new_res)
	 {
		 // security checks!
		 if(count($old_res) != count($new_res))
		 {
		 	return;
		 }
		 if(!$tablename || !$keyfield)
		 {
		 	return;
		 }
		 // Reference:
		 // http://www.php.net/manual/en/function.mysql-query.php,
		 // annotation by babba@nurfuerspam.de
		 for($i=0; $i<count($old_res); $i++)
		 {
			 // security check
			 if($old_res[0][$keyfield] != $new_res[0][$keyfield])
			 {
			 	return;
			 }
			 $changedvals = "";
			 foreach($old_res[$i] as $key=>$oldval)
			 {
				 $newval = $new_res[$i][$key];
				 if($oldval != $newval)
				 {
					 if($changedvals != '')
					 {
						 $changedvals .= ', ';
					 }
					 $changedvals .= '`'.$key.'`=';
					 if(!is_numeric($newval))
					 {
						 $changedvals .= '"'.$newval.'"';
					 }
					 else
					 {
						 $changedvals .= $newval;
					 }
				 }
			 }
			 if($changedvals == '')
			 {
			 	return;
			 }
			 $this->Query('UPDATE '.$tablename.' SET '.$changedvals.' WHERE '.$keyfield.'='.$old_res[$i][$keyfield]);
		 }
	}

	/**
	 * THE BIG EVIL NASTY ONE!
	 *
	 * @uses	Wakka::Footer()
	 * @uses	Wakka::GetCookie()
	 * @uses	Wakka::GetHandler()
	 * @uses	Wakka::GetMicrotime()
	 * @uses	Wakka::GetUser()
	 * @uses	Wakka::Header()
	 * @uses	Wakka::Href()
	 * @uses	Wakka::LoadAllACLs()
	 * @uses	Wakka::LoadUser()
	 * @uses	Wakka::LogReferrer()
	 * @uses	Wakka::Maintenance()
	 * @uses	Wakka::Handler()
	 * @uses	Wakka::ReadInterWikiConfig()
	 * @uses	Wakka::Redirect()
	 * @uses	Wakka::SetCookie()
	 * @uses	Wakka::SetPage()
	 * @uses	Wakka::SetUser()
	 *
	 * @param	string $tag mandatory: name of the single page/image/file etc. to be used
	 * @param	string $method optional: the method which should be used. default: "show"
	 * 
	 * @todo	rewrite the handler call routine and move handler specific settings to handler config files #446 #452
	 * @todo	comment each step to make it understandable to contributors
	 */
	function Run($tag, $handler = '')
	{
		// do our stuff!
		if (!$this->handler = trim($handler))
		{
			$this->handler = 'show';
		}
		if (!$this->tag = trim($tag))
		{
			//Temporary remove infinite redirect loop when no page selected.
			//$this->Redirect($this->Href('', $this->config['root_page']));
		}
		if ($user = $this->LoadUser($this->GetCookie('user_name'), $this->GetCookie('pass')))
		{
			$this->SetUser($user);
		}
		if (isset($_COOKIE['wikka_user_name']) && (isset($_COOKIE['wikka_pass'])))
		{
		 //Old cookies : delete them
			$this->DeleteCookie('wikka_pass');
			$this->DeleteCookie('wikka_user_name');
		}
		#$this->SetPage($this->LoadPage($tag, (isset($_REQUEST["time"]) ? $_REQUEST["time"] :'')));
		$this->SetPage($this->LoadPage($tag, (isset($_GET['time']) ? $_GET['time'] :''))); #312

		//$this->LogReferrer();
		//$this->ACLs = $this->LoadAllACLs($this->tag);
		//$this->ReadInterWikiConfig();
		//if(!($this->GetMicroTime()%3)) $this->Maintenance();
		//HTTP headers (to be moved to handler config files - #452)
		if (preg_match('/\.(xml|mm)$/', $this->handler))
		{
			header('Content-type: text/xml');
			print($this->Handler($this->handler));
		}
		// raw page handler
		elseif ($this->handler == 'raw')
		{
			header('Content-type: text/plain');
			print($this->Handler($this->handler));
		}
		// grabcode page handler
		elseif (($this->GetHandler() == 'grabcode') || ($this->GetHandler() == 'mindmap_fullscreen'))
		{
			print($this->Handler($this->handler));
		}
		elseif (preg_match('/\.(gif|jpg|png)$/', $this->handler))
		{
			header('Location: images/' . $this->handler);
		}
		elseif (preg_match('/\.css$/', $this->handler))
		{
			header('Location: css/' . $this->handler);
		}
		else
		{
			$content_body = $this->Handler($this->handler);
			//print($this->Header().$content_body.$this->Footer());
			print($content_body);
		}
	}
}
?>
