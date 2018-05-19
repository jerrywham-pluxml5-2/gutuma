<?php 
/************************************************************************
 * @project Gutuma Newsletter Managment
 * @author Rowan Seymour
 * @copyright This source is distributed under the GPL
 * @file Settings functions
 * @modifications Cyril Maguire
 *
 * Gutama plugin package
 * @version 1.6
 * @date	01/10/2013
 * @author	Cyril MAGUIRE
*/

class gu_config{
	private static $version;
	private static $values;
	/**
	 * Gets the version that last stored the config values
	 * @return mixed The version number
	 */
	public static function get_version(){
		self::reload();
		return self::$version;
	}
	/**
	 * Sets the value of the specified setting (only in adhesion plugin)
	 * @param string $key The setting name
	 * @return null
	 */
	public static function set_adhesion($key){
		//$plxAdmin = defined('PLX_ADMIN')?@plxAdmin::getInstance():@plxMotor::getInstance();
		$plxAdmin = $GLOBALS['plxMotor'];
		if (isset($plxAdmin->plxPlugins->aPlugins["adhesion"])){
			$adhesion = $plxAdmin->plxPlugins->aPlugins["adhesion"];
			$admin = $adhesion->getParam('nom_asso');
			$mail = $adhesion->getParam('email');
			if($mail != ''){
				if ($key == 'admin_email'){
					self::$values[$key] = $adhesion->getParam('email');
				}
			}
			if($admin != ''){
				if ($key == 'admin_name'){
					self::$values[$key] = $adhesion->getParam('nom_asso');
				}
			}
		}
		//return self::$values[$key];
	}
	/**
	 * Gets the value of the specified setting
	 * @param string $key The setting name
	 * @return mixed The setting value
	 */
	public static function get($key){
		if ($key == 'admin_email' OR $key == 'admin_name')
			self::set_adhesion($key);
		return self::$values[$key];
	}
	/**
	 * Gets all the settings of users except the admin user
	 * @return array The setting of users
	 */
	public static function getUsers(){
		return unserialize(str_replace('\"','"',self::get('users')));
	}
	/**
	 * Sets the value of the specified setting
	 * @param string $key The setting name
	 * @param mixed $value The setting value
	 */
	public static function set($key, $value){
		self::$values[$key] = $value;
	}
	/**
	 * Sets the value of users except admin user
	 * @param numeric $id The id of user in pluxml
	 * @param string $name The name of user
	 * @param string $login The login of user in pluxml
	 * @param string $password The encoded password of user in pluxml
	 * @param string $salt The salt of user in pluxml
	 * @param string $profil The profile of user in pluxml
	 */
	public static function setUsers($id,$name,$login,$password,$salt,$profil){
		$users = self::getUsers();
		$users[$name] = array(
			'id'=>$id,
			'login'=>$login,
			'password'=>$password,
			'salt'=>substr($salt,1,-2),
			'profil'=>$profil
		);
		self::$values['users'] = serialize($users);
	}
	/**
	 * Delete a user
	 * @param string $name The name of user
	 */
	public static function delUser($name){
		$users = self::getUsers();
		unset($users[$name]);
		self::$values['users'] = serialize($users);
		self::save();
	}
	/**
	 * Reloads settings
	 */
	public static function reload(){
		if (!file_exists(GUTUMA_CONFIG_FILE))// Check if a config exists
			return FALSE;
// Read file values and copy to static members
		$gu_config = array();
//include GUTUMA_CONFIG_FILE;
		eval(base64_decode(substr(file_get_contents(GUTUMA_CONFIG_FILE),9,-5)));// Version encodée (voir ligne 196)
//eval(substr(file_get_contents(GUTUMA_CONFIG_FILE),7,-4));// Version décodée (voir ligne 197)
		self::$version = $gu_config_version;
		foreach (array_keys($gu_config) as $keys)
			self::$values[$keys] = $gu_config[$keys];
		return TRUE;
	}
	/**
	 * Loads settings - default values are overridden by user's config file if it exists
	 */
	public static function load(){
		global $plxMotor;//code is in perpetual movement//$plxMotor = defined('PLX_ADMIN')?plxAdmin::getInstance():plxMotor::getInstance();
		$profil = $plxMotor->aUsers['001'];//default 4 1st install
		if (empty($profil['email']) && strpos($plxMotor->path_url,'news/ajax.php') === FALSE  && strpos($plxMotor->path_url,'news/js/gadgets.js.php') === FALSE && strpos($plxMotor->path_url,'news/subscribe.php') === FALSE){
			header('Location: '.PLX_MORE.'admin/profil.php');
			exit;
		}
// Set defaults
		self::$values	= array();
		self::$values['application_name'] = t('Newsletters');
		self::$values['collective_name'] = t('My Newsletters');
		self::$values['admin_name'] = $profil['name'];
		self::$values['admin_username'] = $profil['login'];
		self::$values['admin_password'] = $profil['password'];
		self::$values['admin_email'] = $profil['email'];
		self::$values['use_smtp'] = TRUE;
		self::$values['use_sendmail'] = FALSE;
		self::$values['use_phpmail'] = TRUE;
		self::$values['smtp_server'] = '';
		self::$values['smtp_port'] = '';
		self::$values['smtp_encryption'] = '';
		self::$values['smtp_username'] = '';
		self::$values['smtp_password'] = '';
		self::$values['batch_max_size'] = 500;
		self::$values['batch_time_limit'] = 30;
		self::$values['msg_prefix_subject'] = TRUE;
		self::$values['msg_coll_name_on_multilist'] = FALSE;
		self::$values['msg_append_signature'] = TRUE;
		self::$values['msg_admin_copy'] = TRUE;
		self::$values['spell_check'] = 'browser';// browser, no
		self::$values['tiny_tools'] = 'tools';//tools, menu, all (tools & menu), no (dont use tiny?)
		self::$values['theme_name'] = 'default';//gutuma (original theme)
		self::$values['list_send_welcome'] = TRUE;
		self::$values['list_send_goodbye'] = TRUE;
		self::$values['list_subscribe_notify'] = TRUE;
		self::$values['list_unsubscribe_notify'] = TRUE;
		self::$values['salt'] = $profil['salt'];
//		self::$values['ROOT'] = RPATH;
		self::$values['users']= serialize (array());
		if (!file_exists(GUTUMA_CONFIG_FILE))// Check if a config exists
			return FALSE;
// Read file values and copy to static members
		$gu_config = array();
//include GUTUMA_CONFIG_FILE;
		eval(base64_decode(substr(file_get_contents(GUTUMA_CONFIG_FILE),9,-5)));//var_dump($gu_config);// Version encodée (voir ligne 105 & 196)
//eval(substr(file_get_contents(GUTUMA_CONFIG_FILE),7,-4));// Version décodée (voir ligne 106 & 197)
		self::$version = $gu_config_version;
		foreach (array_keys($gu_config) as $keys)
			self::$values[$keys] = $gu_config[$keys];
		return TRUE;
	}
	/**
	 * Saves the current settings values by writing them to the config.php file
	 * @return bool TRUE if operation was successful, else FALSE
	 */
	public static function save(){
		if (gu_is_demo())
			return gu_error(t('Settings cannot be changed in demo mode'));
		// Data checks
		if (preg_match('[^A-Za-z0-9]', self::$values['admin_username']))
			return gu_error(t('Username can only contain alphanumeric characters'));
		if (strlen(self::$values['admin_password']) < GUTUMA_PASSWORD_MIN_LEN)
			return gu_error(t('Password must be at least % characters long',array(GUTUMA_PASSWORD_MIN_LEN)));
		if (!check_email(self::$values['admin_email']))
			return gu_error(t('A valid administrator email must be at provided'));
		if (!is_dir(RPATH.'themes/'.self::$values['theme_name']))
			return gu_error(t("Theme <em>%</em> doesn't exists. You need to create it first !",array(self::$values['theme_name']) ) );
		if (!touch(GUTUMA_CONFIG_FILE))//$lh = @fopen(GUTUMA_CONFIG_FILE, 'w'); if ($lh == FALSE)fclose($lh);
			return gu_error(t("Unable to create/open % file for writing",array(GUTUMA_CONFIG_FILE)));
//		$w = (strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN');#iswin more simply # 1st idea 4 detect windwos ((strpos(str_replace(":\\","",self::$values[$key]),":\\")!=false))//check is :\ in path (is windows path ?) /:
		$f = "\$gu_config_version = ".GUTUMA_VERSION_NUM.";\n";#Begin txt 4 config file
		foreach (array_keys(self::$values) as $key){
			if (is_bool(self::$values[$key]))
				$f .= "\$gu_config['".$key."'] = ".(self::$values[$key] ? 'TRUE' : 'FALSE').";\n";
			elseif (is_numeric(self::$values[$key]))
				$f .= "\$gu_config['".$key."'] = ".self::$values[$key].";\n";
			elseif ($key == 'ROOT'/* && $w */)//$gu_config['ROOT'] //removal (before 1.8.6.plx.5.6) # Pass this line if exist in old conf
				$rien = '';#$f .= "\$gu_config['".$key."'] = 'E:\htdocs\PluXml-5.6\myPluXml\plugins\gutuma\news\';\n";#test //~ 2nd idea  $f .= "\$gu_config['".$key."'] = '".str_replace('\\','\\',self::$values[$key])."';\n";
			else
				$f .= "\$gu_config['".$key."'] = '".str_replace(array('\"',"'",'\\'),array('"','’','/'),self::$values[$key])."';\n";#replace last \ by / ::: preg_replace('/^.|.$/','',$string); //rem 1st & last char
		}
		file_put_contents(GUTUMA_CONFIG_FILE,"<?php /*\n".base64_encode($f)."\n*/  ?>");// Version encodée (voir ligne 159)
/*file_put_contents(GUTUMA_CONFIG_FILE,"<?php \n".$f."\n?>");*/ // Version décodée (voir ligne 160)
		return TRUE;
	}
	/**
	 * Méthode qui retourne une chaine de caractères au hasard
	 * @param	taille		nombre de caractère de la chaine à retourner (par défaut sur 10 caractères)
	 * @return	string		chaine de caractères au hasard
	 * @author	Florent MONTHEL et Stephane F
	 **/
	public static function plx_charAleatoire($taille='10'){
		$string = '';
		$chaine = 'abcdefghijklmnpqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
		mt_srand((float)microtime()*1000000);
		for($i=0; $i<$taille; $i++)
			$string .= $chaine[ mt_rand()%strlen($chaine) ];
		return $string;
	}
}