<?php
/************************************************************************
 * @project Gutuma Newsletter Managment
 * @author Rowan Seymour
 * @copyright This source is distributed under the GPL
 * @file Miscellaneous functions
 *
 * Gutama plugin package
 * @version 1.8.7
 * @date	22/11/2017
 * @author	Thomas INGLES
 * @author	Cyril MAGUIRE
*/
/**
 * Checks the start of the specified string
 * @param string $haystack The string to check
 * @param string $needle The start to check for
 * @return bool TRUE if the string starts with the given string, else FALSE
 */
function str_starts($haystack, $needle){
	return strpos($haystack, $needle) === 0;
}
/**
 * Checks the ending of the specified string
 * @param string $haystack The string to check
 * @param string $needle The ending to check for
 * @return bool TRUE if the string ends with the given string, else FALSE
 */
function str_ends($haystack, $needle){
	$ending = substr($haystack, strlen($haystack) - strlen($needle));
	return $ending === $needle;
}
/**
 * Limits the length of a string and appends '...' if over the specified max length
 * @param string $str The string to limit
 * @param int $max The max number of characters in the returned string
 * @return string The limited string
 */
function str_limit($str, $max){
	return (strlen($str) > $max) ? (mb_substr($str, 0, $max, GUTUMA_ENCODING) . '…') : $str;#with substr(), if last char (25th) is accentuated ::: replaced by � :\
}
/**
 * Masks a string such as a password
 * @param string $str The string to mask
 * @param string $mask The masking character
 * @return string The masked string
 */
function str_mask($str, $mask = '*'){
	$masked = '';
	for($i = 0; $i < strlen($str); ++$i)
		$masked .= $mask;
	return $masked;
}
/**
 * Checks if the specified name is a POST variable
 * @param string $name The name to check
 * @return TRUE if the specified id is a posted value, else FALSE
 */
function is_post_var($name){
	return isset($_POST[$name]);
}
/**
 * Gets a POST variable and strips slashes if they've been added by magic_quotes
 * @param string $name The name of the POST variable
 * @return string The POST variable value
 */
function get_post_var($name){
	if (get_magic_quotes_gpc() && isset($_POST[$name]) && !is_array($_POST[$name]))
		return stripslashes($_POST[$name]);
	return $_POST[$name];
}
/**
 * Checks if the specified name is a GET variable
 * @param string $name The name to check
 * @return TRUE if the specified id is a GET value, else FALSE
 */
function is_get_var($name){
	return isset($_GET[$name]);
}
/**
 * Gets a GET variable and decodes any url encoded characters
 * @param string $name The name of the GET variable
 * @return string The GET variable value
 */
function get_get_var($name){
	return urldecode($_GET[$name]);
}
/**
 * Loosely checks the validity of an email address
 * @param string $address The address to check
 * @return bool TRUE is email address is valid, else FALSE
 */
function check_email($address){
// Reject blank addresses and invalid characters
	if ($address == '' || preg_match('[^0-9a-zA-Z_@\.\[\]\-]', $address))
		return FALSE;
	if(function_exists('filter_var'))//Free [PHP.5.1.3] Fix: Call to undefined function filter_var()
		return filter_var($address, FILTER_VALIDATE_EMAIL);
	if(method_exists('plxUtils', 'checkMail'))//PluXml
		return plxUtils::checkMail($address);
// Allow only one @, which can't be the first character
	return ((strpos($address, '@') > 0) && (substr_count($address, '@') == 1));//Gutuma Original
}
/**
 * Recursively deletes the specified directory or file
 * @param string $path The directory or file to delete
 */
function rm_recursive($path){
	if (is_dir($path) && !is_link($path)){
		if ($dh = @opendir($path)){
			while (($sf = readdir($dh)) !== FALSE){
				if ($sf == '.' || $sf == '..')
					continue;
				if (!rm_recursive($path.'/'.$sf))
					return FALSE;
			}
			closedir($dh);
		}
		return @rmdir($path);
	}
	return @unlink($path);
}
/**
 * Converts a path such as 'login.php' to a full URL, or returns the current absolute url if path is not specified
 * @param string $path The path to convert, blank if the current url should be used
 * @return string The absolute URL
 */
function absolute_url($path = ''){
	$isHTTPS = (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on");
	$port = (isset($_SERVER["SERVER_PORT"]) && ((!$isHTTPS && $_SERVER["SERVER_PORT"] != "80") || ($isHTTPS && $_SERVER["SERVER_PORT"] != "443")));
	$port = ($port) ? ':'.$_SERVER["SERVER_PORT"] : '';
	$base = ($isHTTPS ? 'https://' : 'http://').$_SERVER["SERVER_NAME"].$port;
	if ($path != ''){
		$abs_path = str_replace("\\", "/", get_absolute_path(rtrim(dirname($_SERVER['PHP_SELF']), '/\\').'/'.$path));
		return $base.'/'.$abs_path;
	}
	else {
		$query = (isset($_SERVER['QUERY_STRING']) && $_SERVER['QUERY_STRING'] != '') ? ('?'.$_SERVER['QUERY_STRING']) : "";
		return $base.$_SERVER['PHP_SELF'].$query;
	}
}
/**
 * Canonizes a path, even if it doesn't exist. From http://uk2.php.net/realpath
 * @param string $path The path to canonize
 * @return string The canonized path
 */
function get_absolute_path($path){
	$path = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $path);
	$parts = array_filter(explode(DIRECTORY_SEPARATOR, $path), 'strlen');
	$absolutes = array();
	foreach ($parts as $part){
		if ('.' == $part)
			continue;
		if ('..' == $part)
			array_pop($absolutes);
		else
			$absolutes[] = $part;
	}
	return implode(DIRECTORY_SEPARATOR, $absolutes);
}
/**
 * Strips the extension from the given file path
 * @param string $path The path to strip
 * @return string The new extensionless path
 */
function remove_ext($path){
	$ext = strrchr($path, '.');
	if ($ext !== FALSE)
		return substr($path, 0, -strlen($ext));
	return $path;
}
/**
 * Converts HTML to plain text, trying to keep the layout
 */
function html_to_text(&$html){
	// Even tho TinyMCE tends to put newlines into the HTML in the right places,
	// we can't assume that about the formatting of the HTML, so we start by
	// removing all new lines
	$text = str_replace(array("\r\n", "\n"), '', $html);
// table to paragraph
	$text = str_replace('<table', "<div", $text);
	$text = str_replace('</table>', "</div>", $text);
	$text = str_replace('<tr', "<p", $text);
	$text = str_replace('</tr>', "</p>", $text);
	$text = str_replace('<td', " <span", $text);
	$text = str_replace('</td>', "</span> ", $text);
// Start-tag beginnings that deserve a new line
	$text = str_replace('<p', "\n<p", $text);
	$text = str_replace('<h', "\n<h", $text);
	$text = str_replace('<li', "\n<li", $text);
// End-tags that deserve a new line
	$text = str_replace(array('</p>', '</h1>', '</h2>', '</h3>', '</h4>', '</h5>', '</h6>', '</ol>', '</ul>', '<br />'), "\n", $text);
	$text = str_replace("<li>", "* ", $text);
	$text = str_replace("<hr />", "-------------------------------------------\n", $text);
// Convert entities such as &nbsp; to real characters
	$text = html_entity_decode($text, ENT_QUOTES, GUTUMA_ENCODING);
// Strip all but links and images
	$text = strip_tags($text, '<a>');
// Replace <a href="http://...">http://...</a> with http://... (or https)
	$pattern = "/<[aA] .*?[hH][rR][eE][fF]=\"(.*?)\".*?>https?:\/\/(.*?)<\/[aA]>/";
	$text = preg_replace($pattern, "$1", $text);
// Replace <a href="http://...">text</a> with text (http://...)
	$pattern = "/<[aA] .*?[hH][rR][eE][fF]=\"(.*?)\".*?>(.*?)<\/[aA]>/";
	$text = preg_replace($pattern, "$2 ($1)", $text);
// Replace <a ...>text</a> with text
	$pattern = "/<[aA] .*?>(.*?)<\/[aA]>/";
	$text = preg_replace($pattern, "$1", $text);
	$text = trim($text);
	return wordwrap($text, 70);
}