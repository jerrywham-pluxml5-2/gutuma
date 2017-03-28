<?php 
/************************************************************************
 * @project Gutuma Newsletter Managment
 * @author Rowan Seymour
 * @copyright This source is distributed under the GPL
 * @file Main Gutuma application
 * @modifications Cyril Maguire
 */
/* Gutama plugin package
 * @version 1.6
 * @date	01/10/2013
 * @author	Cyril MAGUIRE
*/

// Inclusion des librairies de plxuml
include_once ('_pluxml.php');

define('GU_CONFIG_LANG', $glang);

// Check for PHP5+
if (version_compare(phpversion(), '5', '<'))
	die(t('Sorry - Gutuma requires at least PHP5. Please contact your hosting provider and ask them to upgrade.'));

include_once 'setting.php';
include_once 'misc.php';
include_once 'session.php';
include_once 'list.php';
include_once 'theme.php';


// Constants 
define('GUTUMA_VERSION_NUM', 1060001); // Version number w.x.y.z -> wwxxyyzz
define('GUTUMA_VERSION_NAME', '1.6'); // Version friendly name
define('GUTUMA_DEMO_MODE', FALSE); // Demonstration mode
define('GUTUMA_TITLE', t('Gutuma Newsletter Management')); // Application title of Gutuma
define('GUTUMA_URL', 'https://web.archive.org/web/20081228162738/http://ijuru.com/gutuma'); // Homepage of Gutuma
define('GUTUMA_UPDATE_URL', 'http://gutuma.sourceforge.net/update.js.php?ver='.GUTUMA_VERSION_NUM);
define('GUTUMA_ENCODING', 'UTF-8'); // Content encoding
define('GUTUMA_CONFIG_FILE', $plxMotor->plxPlugins->aPlugins['gutuma']->listsDir.'/inc/config.php'); // Configuration file
define('GUTUMA_PASSWORD_MIN_LEN', 6); // Minimum password length
define('GUTUMA_EMAIL', 'rowanseymour@users.sourceforge.net'); // Author email address
define('GUTUMA_LISTS_DIR', $plxMotor->plxPlugins->aPlugins['gutuma']->listsDir); // Directory where lists are stored
define('GUTUMA_TEMP_DIR', sys_get_temp_dir()); // Directory where temp message files are stored
define('GUTUMA_TEMP_EXPIRY_AGE', 3*60*60); // The number of seconds from last access before subfolders/files are deleted from the temp directory
define('GUTUMA_PAGE_SIZE', 10); // The number of items per page in lists of addresses
define('GUTUMA_MAX_ADDRESS_LEN', 320); // The max allowable length in characters of an email address
define('GUTUMA_TINYMCE_COMPRESSION', FALSE); // Enables gzip compression of the TinyMCE scripts

if(!defined('RPATH')){//semble inutilisé
	if (class_exists('gu_config')){
		gu_config::reload();
		define('RPATH',str_replace('inc'.DIRECTORY_SEPARATOR.'gutuma.php','',__FILE__));
	}else{
		header('Location: '.absolute_url('install.php'));
		exit();
	}
}

// Demo mode restrictions
define('GUTUMA_DEMO_MAX_LIST_SIZE', 100); // Maximum number of addresses per list
define('GUTUMA_DEMO_MAX_NUM_LISTS', 10); // Maximum number of lists

// Apparently IIS5 servers don't populate PHP_SELF
$_SERVER['PHP_SELF'] = (isset($_SERVER['PHP_SELF'])) ? $_SERVER['PHP_SELF'] : $_SERVER['SCRIPT_NAME']; 

$htaccess = "Allow from none\n";
$htaccess .= "Deny from all\n";
$htaccess .= "<Files *.php>\n";
$htaccess .= "order allow,deny\n";
$htaccess .= "deny from all\n";
$htaccess .= "</Files>\n";
$htaccess .= "Options -Indexes\n";
// Make lists directory
if (!is_dir(GUTUMA_LISTS_DIR)) {
	mkdir(GUTUMA_LISTS_DIR);
	touch(GUTUMA_LISTS_DIR.'index.html');
	touch(GUTUMA_LISTS_DIR.'/.htaccess');
	file_put_contents(GUTUMA_LISTS_DIR.'/.htaccess', $htaccess);
}
// Make config directory
if (!is_dir(GUTUMA_LISTS_DIR.'/inc')) {
	mkdir(GUTUMA_LISTS_DIR.'/inc');
	touch(GUTUMA_LISTS_DIR.'/inc/index.html');
	touch(GUTUMA_LISTS_DIR.'/inc/.htaccess');
	file_put_contents(GUTUMA_LISTS_DIR.'/inc/.htaccess', $htaccess);
}

/**
 * Initializes the Gutuma application
 * @param bool $validate_session TRUE if session should be checked for a valid login, else FALSE
 * @param bool $install_redirect TRUE if we should redirect when install/update required
 */
function gu_init($validate_session = TRUE, $install_redirect = TRUE)
{
	// If settings couldn't be loaded we need to run the install script or if settings could be
	// loaded but version number is less than the built version number, we need to update
	if (!gu_config::load() || gu_config::get_version() < GUTUMA_VERSION_NUM) {
		if ($install_redirect) {
			header('Location: '.absolute_url('install.php'));
			exit;
		}
	}

	if ($validate_session) {
		if (!gu_session_authenticate()) {
			// If we don't have a stored valid session, redirect to the login page
			header('Location: '.absolute_url('login.php').'?ref='.urlencode(absolute_url()));
			exit;
		}
	}
}

/**
 * Checks to see if Gutuma is running in demo mode
 * @return bool TRUE if Gutuma is running in demo mode, else FALSE
 */
function gu_is_demo()
{
	return GUTUMA_DEMO_MODE;
}

/**
 * Checks to see if Gutuma is running in debugging mode
 * @return bool TRUE if Gutuma is running in demo mode, else FALSE
 */
function gu_is_debugging()
{
	return is_get_var('DEBUG') && gu_session_is_valid();
}

/**
 * Simple function to set the global error message holder
 * @param string $msg The error message
 * @return bool Always FALSE so that you can write if (...) return gu_error("...")
 */
function gu_error($msg, $extra = NULL)
{
	$_SERVER['GU_ERROR_MSG'] = $msg;
	$_SERVER['GU_ERROR_EXTRA'] = $extra;
	return FALSE;
}

/**
 * Simple function to store a debug message
 * @param string $msg The debug message
 */
function gu_debug($msg)
{
	if (!gu_is_debugging())
		return;
	if (!isset($_SERVER['GU_DEBUG_MSGS']))
		$_SERVER['GU_DEBUG_MSGS'] = array();
	$_SERVER['GU_DEBUG_MSGS'][] = $msg;
}

/**
 * Simple function to set the global success message holder
 * @param string $msg The success message
 * @return bool Always TRUE so that you can write if (...) return gu_success("...")
 */
function gu_success($msg)
{
	$_SERVER['GU_STATUS_MSG'] = $msg;
	return TRUE;
}




/**
 * Pass an empty string to $_SESSION['glang'] if you want to add new translations
 * Otherwise, new translation will not be taken into account until session die
 */
// $_SESSION['glang'] = '';

/**
 * Convertis une cle de traduction en langage traduit
 * @param string $key : cle de traduction
 * @param [OPTIONNAL] array $parameters, parametres dynamiques à inclure dans la traduction (remplace les signes % dans $key et [%%] dans la valeur correspondante par les valeurs du tableau de parametres)
 * @param [OPTIONNAL] string $glang
 * @author Idleman, Cyril MAGUIRE
 * @echo String $traduction
 */
function t($key,$parameters=null,$langage=GU_CONFIG_LANG){
	if (!defined('RPATH')) {
		define('RPATH',str_replace('inc'.DIRECTORY_SEPARATOR.'gutuma.php','',__FILE__));
	}
	$return = '';
	if (isset($_SESSION['glang']) && is_array($_SESSION['glang']) && array_key_exists($key,$_SESSION['glang'])){
		$glang = $_SESSION['glang'];
	} else {
		$glang = $_SESSION['glang'] = getLang($langage);
	}

	if ($glang != 'en') {
		$return = (isset($glang[$key])?$glang[$key]:'');
		if ($return == '') {
			file_put_contents(RPATH.'lang/'.$langage,$key.'[::->]<span style="color:red;font-weight:bold">TRADUCTION MISS : "'.$key.'" for langage ['.$langage.']</span>'."\n",FILE_APPEND|LOCK_EX);
			$return = '<span style="color:red;font-weight:bold">TRADUCTION MISS : "'.$key.'" for langage ['.$langage.']</span>';
		}elseif(isset($parameters)){
			$return = distribParams($return,$key,$parameters,$langage);
		}
	} else {
		if(isset($parameters)){
			$return = distribParams($return,$key,$parameters,$langage);
		}else {
			$return = $key;
		}
	}
	return $return;
}
/**
 * Convertis une cle de traduction en langage traduit
 * @param string $return : la traduction
 * @param string $key : cle de traduction
 * @param array $parameters, parametres dynamiques à inclure dans la traduction (remplace les signes % dans $key et [%%] dans la valeur correspondante par les valeurs du tableau de parametres)
 * @param string $glang : la langue de l'interface
 * @author Idleman, Cyril MAGUIRE
 * @echo String $traduction
 */
function distribParams($return,$key,$parameters,$glang){
	if ($parameters != null) {
		if ($glang != 'en'){
			$parametersVars = explode('[%%]',$return);
		} else {
			$parametersVars = explode('%',$key);
		}
		$return = '';
		$i=0;
		for($o = 0,$c=count($parametersVars);$o<$c;$o++){
			if($o!=0){
				$return .= (isset($parameters[$i])?$parameters[$i]:'').$parametersVars[$o];
				$i++;
			}else{
				$return .= $parametersVars[$o];
			}
		}
	}
	
	return $return;
}


/**
 * Parse le fichiers des langues et retourne les traductions sous forme d'un tableau
 * @param [OPTIONNAL] string $glang
 * @author Idleman, Cyril MAGUIRE
 * @return array<String> $traductions
 */
function getLang($glang=GU_CONFIG_LANG){

	if($glang != 'en'){
		$path = RPATH.'lang'.DIRECTORY_SEPARATOR.$glang;
		if (!is_file($path)) {
			file_put_contents($path,'');
		}
		$langLines = file($path);

		$traductions = array();
		foreach($langLines as $langLine){
			if(strstr($langLine,'[::->]')){
				list($key,$traduction) = explode('[::->]',$langLine);
				$traductions[$key] = (substr($traduction,-1) == "\n")? substr($traduction,0,-1) : $traduction;
			}
		}
	}else{
		$traductions = 'en';
	}

	return $traductions;
}
?>