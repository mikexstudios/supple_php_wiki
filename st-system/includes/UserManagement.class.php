<?php
/**
suppleText User Management class

Code adapted from Xcomic.

$Id: UserManagement.class.php 104 2005-12-16 20:00:50Z squeeself $
*/

//Start sessions
session_start();

class UserManagement extends Handler {	
    var $username, $id; //password is unencrypted
    var $md5pass;

	function UserManagement($id = null, $inPassword = null) {
		parent::Handler();

		if (!empty($id)) {
			$this->id = $id;
		}
		if (!empty($inPassword)) {
			$this->md5pass($inPassword);
		}

	}
	
	function setUsername($inUsername) {
		$this->username = $inUsername;
	}
	
	function setPassword($passwd) {
	    $this->md5pass($passwd);
	}
	
	function md5pass($passwd) {
		$this->md5pass = md5($passwd);
	}
	
	function setMd5Password($inMd5Password) {
		$this->md5pass = $inMd5Password;	
	}
	
	function registerUser($username, $password, $email) {
		
		$this->setUsername($username);
		
		//Check to see if username has been taken
		if ($this->userExists()) {
			die('Sorry, that username has already been taken. Please select another one.');
		}
		
		$this->setPassword($password);
		
		$sql = '
			INSERT INTO '.ST_USERS_TABLE.' (username, password, email) 
			VALUES (
				:username, 
				:password,
				:email
				)';
		$stmt = $this->Db->prepare($sql);
		$stmt->bindParam(':username', $this->username, PDO::PARAM_STR);
		$stmt->bindParam(':password', $this->md5pass, PDO::PARAM_STR);
		$stmt->bindParam(':email', $email, PDO::PARAM_STR);
		$stmt->execute();
		
		//Add check for query failure.
	}
	
	//Can add more fields
	function editUserInfo($inEmail) {
		
		//Update the email for the username
		$sql = '
		  UPDATE '.ST_USERS_TABLE.' 
			SET email = :email  
			WHERE uid = :uid';
		$stmt = $this->Db->prepare($sql);
		$stmt->bindParam(':email', $inEmail, PDO::PARAM_STR);
		$stmt->bindParam(':uid', $this->id, PDO::PARAM_INT);
		$stmt->execute();	
	}
	
	function changePassword($inNewPassword) {
		
		//Update the password in this class
		$this->setPassword($inNewPassword);
		
		//Make changes to DB
		$sql = '
		  UPDATE '.ST_USERS_TABLE.'
			SET password = :password 
			WHERE uid = :uid';
		$stmt = $this->Db->prepare($sql);
		$stmt->bindParam(':password', $this->md5pass, PDO::PARAM_STR);
		$stmt->bindParam(':uid', $this->id, PDO::PARAM_INT);
		$stmt->execute();	

	}
	
	function deleteUser() {
		
		//Check if user exists
		if (!$this->userExists()) {
			die("Can't delete non-existant user!");
		}
		
		//Delete from DB
		$sql = '
		  DELETE FROM '.ST_USERS_TABLE.'
			WHERE uid = :uid';
		$stmt = $this->Db->prepare($sql);
		$stmt->bindParam(':uid', $this->id, PDO::PARAM_INT);
		$stmt->execute();	

	}
	
	function getUsername() {
		if (isset($this->username)) 
		{
		    return $this->username;
		}

		if(empty($this->id)) //If no username set, then we give the user an identity.
		{
			return 'Anonymous';
		}
		
		$sql = '
		  SELECT username
			FROM '.ST_USERS_TABLE.' 
			WHERE uid = :uid';
		$stmt = $this->Db->prepare($sql);
		$stmt->bindParam(':uid', $this->id, PDO::PARAM_INT);
		$stmt->execute();	
		$result = $stmt->getColumn();

		//Return username
		$this->username = $result;
		return $result;
	}
	
	function getUid()
	{
		
		$sql = '
		  SELECT uid
			FROM '.ST_USERS_TABLE.' 
			WHERE username = :username';
		$stmt = $this->Db->prepare($sql);
		$stmt->bindParam(':username', $this->username, PDO::PARAM_STR);
		$stmt->execute();
	  $result = $stmt->fetchColumn();

		$this->id = $result;
		return $result;  
	}
	
	function userExists() {

		if (!isset($this->username) && !isset($this->id)) {
		    return false;
		}
		
		$sql = '
			SELECT username
			FROM '.ST_USERS_TABLE.' 
			WHERE ';
		    if (isset($this->id)) {
			    $sql .= 'uid = :uid ';
			} elseif (isset($this->username)) {
			    $sql .= 'username = :username';
			} else {
				//Neither id or username is set so the hypothetical user
				//does not exist.
				return false;
			}
		
		$stmt = $this->Db->prepare($sql);
		$stmt->bindParam(':uid', $this->id, PDO::PARAM_INT);
		$stmt->bindParam(':username', $this->username, PDO::PARAM_STR);
		$stmt->execute();
		$result = $stmt->fetchColumn(); //Get only one variable
		
		if (!empty($result)) {
			//User exists
			return true;
		} 
		//User doesn't exist
	    return false;
	}
	
	function authUser()
	{
		
		//Verify that the user exists
		if (!$this->userExists()) {
		
			return false;
			
			//The following could weaken security
			//$message->error('The entered username does not exist.');
		}

		//Grab password from Db
		$sql = '
		  SELECT password 
			FROM '.ST_USERS_TABLE.'
			WHERE username = :username';
		$stmt = $this->Db->prepare($sql);
		$stmt->bindParam(':username', $this->username, PDO::PARAM_STR);
		$stmt->execute();
		$result = $stmt->fetchColumn();

		//Return user id
		if ($this->md5pass == $result) {
			//User authenticated
			return true;
		}
		//Bad authentication. Password failure.
		return false;
	}
	
	function registerSessionVariables()
	{
		//Set session variables
		$_SESSION[SESSION_USERNAME] = $this->username;
		$_SESSION[SESSION_PASSWORD] = $this->md5pass;
	}
	
	function setCookies($func = 'login')
	{
		
		$cookieTime = 60*60*24*100; //Cookie persists for 100 days
		
		//If logging out
		if ($func == 'login') {
			//Set cookies from session variables
			setcookie(COOKIE_USERNAME, $_SESSION[SESSION_USERNAME], time()+$cookieTime, "/");
			setcookie(COOKIE_PASSWORD, $_SESSION[SESSION_PASSWORD], time()+$cookieTime, "/");
		} else {//Logout
			//Minus the time set to logout. (Setting the time in the past)
			setcookie(COOKIE_USERNAME, '', time()-$cookieTime, "/");
			setcookie(COOKIE_PASSWORD, '', time()-$cookieTime, "/");	
		}
	}
	
	function isLoggedIn()
	{
		global $Supple;
		
		//If cookies exists, set session variables with them
		if (!empty($_COOKIE[COOKIE_USERNAME]) && !empty($_COOKIE[COOKIE_PASSWORD])) {
			//Input validation. We let the password be anything.
			$_SESSION[SESSION_USERNAME] = $Supple->Input->cookie(COOKIE_USERNAME, true);
			$_SESSION[SESSION_PASSWORD] = $Supple->Input->cookie(COOKIE_PASSWORD, true);
		}

		//echo $_SESSION[SESSION_USERNAME];
		//echo $_SESSION[SESSION_PASSWORD];

		//Check if session variables have been set
		if (!empty($_SESSION[SESSION_USERNAME]) && !empty($_SESSION[SESSION_PASSWORD])) {
			
			//Set username and password
			$this->setUsername($Supple->Input->session(SESSION_USERNAME, true));
			$this->setMd5Password($Supple->Input->session(SESSION_PASSWORD, true));
			
			//Authenticate user
			if ($this->authUser()) {
				//User logged in
				return true;
			} else {
				//Session variables are incorrect. Unset
				unset($_SESSION[SESSION_USERNAME]);
				unset($_SESSION[SESSION_PASSWORD]);
				
				//User not logged in
				return false;	
			}
		} else {
			//User not logged in
			return false;
		}
		
	}
	
	function processLogin($alsoDo = '')
	{
		//If authentication is correct, set sessions
		if ($this->authUser()) {
			$this->registerSessionVariables();
			
			if ($alsoDo == 'remember') {
				$this->rememberMe();
			}
			
			//Success
			return true;
		}

		//Failure
		return false;
	}
	
	//Set cookies to remember the user
	function rememberMe()
	{
		$this->setCookies();
	}
	
	function logout()
	{
		
		//Clear cookies
		$this->setCookies('logout');
		
		/* Kill session variables */
		unset($_SESSION[SESSION_USERNAME]);
		unset($_SESSION[SESSION_PASSWORD]);
		$_SESSION = array(); // reset session array
		session_destroy();   // destroy session.
	}

}


/*
//Testing
$x = new UserManagement('test', 'test');
$x->registerUser();
$x->editUserInfo('test@test.com');
*/
?>
