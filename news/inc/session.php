<?php
/************************************************************************
 * @project Gutuma Newsletter Managment
 * @author Rowan Seymour
 * @copyright This source is distributed under the GPL
 * @file Session functions
 */
 /* Gutama plugin package
 * @version 1.6
 * @date	01/10/2013
 * @author	Cyril MAGUIRE
*/
 
define('GU_SESSION_SITES', 'valid_sites');
 
// In order to maintain separate authentication states for different Gutuma
// installations on the same server we need a unique key for each installation
define('GU_SESSION_SITE_KEY', absolute_url('index.php'));

// Start the PHP session engine
if ( !isset($_SESSION) && !function_exists('\php_error\reportErrors') ) {
	session_start();
}
/**
 * Attempts to authenticate the current user. First checks the current session, then any stored cookies, and finally redirects to the login page
 * @return bool TRUE if session is valid, else causes exit and redirect
 */
function gu_session_authenticate($username = NULL, $password = NULL, $remember = TRUE)
{
	// Check aganist specified credentials
	if (isset($username) && isset($password)) {
		if (gu_session_check_credentials($username, $password)) {
			if ($remember) {
				setcookie('username', $username, time()+60*60*24*7);
				setcookie('password', $password, time()+60*60*24*7);
			}
			gu_session_set_valid(TRUE);
			return TRUE;
		}
		else {
			gu_session_set_valid(FALSE);
			return FALSE;
		}
	}

	// Check the session variable next
	if (gu_session_is_valid())
		return TRUE;
	
	// Then try authenticating with cookie values
	if (isset($_COOKIE['username']) && isset($_COOKIE['password'])) {
		if (gu_session_check_credentials($_COOKIE['username'], $_COOKIE['password'])) {
			gu_session_set_valid(TRUE);
			return TRUE;
		}	
  }
	
	gu_session_set_valid(FALSE);
	return FALSE;
}
 
/**
 * Checks the specified username and password against the stored admin credentials
 * @param string $username The username
 * @param string $password The MD5 encrypted password 
 * @return bool TRUE if username and password match stored admin credentials
 */
function gu_session_check_credentials($username, $password)
{
	return ($username == gu_config::get('admin_username')) && (sha1(gu_config::get('salt').$password) == gu_config::get('admin_password'));
}

/**
 * Attempts to authenticate the current user when parameters come from Pluxml. First checks the current session, then any stored cookies, and finally redirects to the login page
 * @return bool TRUE if session is valid, else causes exit and redirect
 */
function plx_gu_session_authenticate($name = FALSE, $username = NULL, $password = NULL, $remember = TRUE,$user = FALSE)
{
	// Check aganist specified credentials
	if (isset($name) && isset($username) && isset($password)) {
		if (plx_gu_session_check_credentials($name, $username, $password,$user)) {
			if ($remember) {
				setcookie('username', $username, time()+60*60*24*7);
				setcookie('password', $password, time()+60*60*24*7);
			}
			gu_session_set_valid(TRUE);
			return TRUE;
		}
		else {
			gu_session_set_valid(FALSE);
			return FALSE;		
		}
	}

	// Check the session variable next
	if (gu_session_is_valid())
		return TRUE;
	
	// Then try authenticating with cookie values
	if (isset($_COOKIE['username']) && isset($_COOKIE['password'])) {
		if (plx_gu_session_check_credentials($_COOKIE['username'], $_COOKIE['password'],true)) {
			gu_session_set_valid(TRUE);
			return TRUE;
		}	
  }
	
	gu_session_set_valid(FALSE);
	return FALSE;
}

/**
 * Checks the specified username and password against the stored admin credentials
 * @param string $username The username
 * @param string $password The MD5 encrypted password 
 * @return bool TRUE if username and password match stored admin credentials
 */
function plx_gu_session_check_credentials($name, $username, $password,$user = FALSE)
{
	if ($user == FALSE){
		return ($username == gu_config::get('admin_username')) && ($password == gu_config::get('admin_password'));
	}else {
		$users = gu_config::getUsers();
		if (isset($users[$name])) {
			return (($username == $users[$name]['login']) && ($password == $users[$name]['password']));
		} else {
			return FALSE;
		}
	}
	
}


/**
 * Stores or removes the valid session flag for this site
 * @param bool $valid TRUE if session is valid, else FALSE
 */
function gu_session_set_valid($valid)
{

	if (!isset($_SESSION[GU_SESSION_SITES]))
		$_SESSION[GU_SESSION_SITES] = array();
	
	if ($valid) {
		session_regenerate_id();
		$_SESSION[GU_SESSION_SITES][GU_SESSION_SITE_KEY] = TRUE;
	}
	else {
		unset($_SESSION[GU_SESSION_SITES][GU_SESSION_SITE_KEY]);
		
		// Clear the username/password cookies
		setcookie('username', '', 1);
		setcookie('password', '', 1);
	}	
}

/**
 * Checks to see if the current session is valid, i.e, authenticated as admin
 * @return bool TRUE if session is valid, else FALSE
 */
function gu_session_is_valid()
{
	return isset($_SESSION[GU_SESSION_SITES][GU_SESSION_SITE_KEY]);
}
 
?>