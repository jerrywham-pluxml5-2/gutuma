<?php 
/************************************************************************
 * @project Gutuma Newsletter Managment
 * @author Rowan Seymour
 * @copyright This source is distributed under the GPL
 * @file Settings functions
 * @modifications Cyril Maguire
 */
 /*
/* Gutama plugin package
 * @version 1.6
 * @date	01/10/2013
 * @author	Cyril MAGUIRE
*/

class gu_config
{
	private static $version;
	private static $values;
	
	/**
	 * Gets the version that last stored the config values
	 * @return mixed The version number
	 */
	public static function get_version()
	{
		self::reload();
		return self::$version;
	}
	
	/**
	 * Gets the value of the specified setting
	 * @param string $key The setting name 
	 * @return mixed The setting value
	 */
	public static function get($key)
	{
		$plxAdmin = plxAdmin::getInstance();
		if (isset($plxAdmin->plxPlugins->aPlugins["adhesion"])) {
			$adhesion = $plxAdmin->plxPlugins->aPlugins["adhesion"];
			$admin = $adhesion->getParam('nom_asso');
			$mail = $adhesion->getParam('email');
			if($mail != '') {
				if ($key == 'admin_email') {
					self::$values[$key] = $adhesion->getParam('email');
				}
			}
			if($admin != '') {
				if ($key == 'admin_name') {
					self::$values[$key] = $adhesion->getParam('nom_asso');
				}
			}
		}
		return self::$values[$key];
	}
	
	/**
	 * Gets all the settings of users except the admin user
	 * @return array The setting of users
	 */
	public static function getUsers()
	{
		return unserialize(str_replace('\"','"',self::get('users')));
	}
	
	/**
	 * Sets the value of the specified setting
	 * @param string $key The setting name 
	 * @param mixed $value The setting value
	 */
	public static function set($key, $value)
	{
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
	public static function setUsers($id,$name,$login,$password,$salt,$profil)
	{
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
	public static function delUser($name)
	{
		$users = self::getUsers();
		unset($users[$name]);
		self::$values['users'] = serialize($users);
		self::save();
	}
	
	/**
	 * Reloads settings
	 */
	public static function reload() {
		// Check if a config exists
		if (!file_exists(GUTUMA_CONFIG_FILE))
			return FALSE;
			
		// Read file values and copy to static members
		$gu_config = array();
		//include GUTUMA_CONFIG_FILE;
		
		// Version encodée (voir ligne 232)
		eval(base64_decode(substr(file_get_contents(GUTUMA_CONFIG_FILE),9,-5)));
		// Version décodée (voir ligne 234)
		//eval(substr(file_get_contents(GUTUMA_CONFIG_FILE),7,-4));
		
		self::$version = $gu_config_version;
		foreach (array_keys($gu_config) as $keys)
			self::$values[$keys] = $gu_config[$keys];
			
		return TRUE;
	}
	
	/**
	 * Loads settings - default values are overridden by user's config file if it exists
	 */
	public static function load()
	{
		$plxAdmin = plxAdmin::getInstance();
		$profil = $plxAdmin->aUsers[$_SESSION['user']];
		if (empty($profil['email']) && strpos($plxAdmin->path_url,'news/ajax.php') === FALSE  && strpos($plxAdmin->path_url,'news/js/gadgets.js.php') === FALSE && strpos($plxAdmin->path_url,'news/subscribe.php') === FALSE){
			header('Location: '.$plxAdmin->urlRewrite().'core/admin/profil.php');
			exit;
		}
		// Set defaults
		self::$values	= array();
		self::$values['application_name'] = 'Newsletters';
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
		self::$values['theme_name'] = 'gutuma';//default
		self::$values['list_send_welcome'] = TRUE;
		self::$values['list_send_goodbye'] = TRUE;
		self::$values['list_subscribe_notify'] = TRUE;
		self::$values['list_unsubscribe_notify'] = TRUE;
		self::$values['salt'] = $profil['salt'];
		self::$values['ROOT']= RPATH;
		self::$values['users']= serialize (array());
		
		
		// Check if a config exists
		if (!file_exists(GUTUMA_CONFIG_FILE))
			return FALSE;
			
		// Read file values and copy to static members
		$gu_config = array();
		//include GUTUMA_CONFIG_FILE;
		
		// Version encodée (voir ligne 232)
		eval(base64_decode(substr(file_get_contents(GUTUMA_CONFIG_FILE),9,-5)));
		// Version décodée (voir ligne 234)
		//eval(substr(file_get_contents(GUTUMA_CONFIG_FILE),7,-4));
		
		self::$version = $gu_config_version;
		foreach (array_keys($gu_config) as $keys)
			self::$values[$keys] = $gu_config[$keys];
		
		return TRUE;
	}
	
	/**
	 * Saves the current settings values by writing them to the config.php file
	 * @return bool TRUE if operation was successful, else FALSE
	 */
	public static function save()
	{
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
		
		$lh = @fopen(GUTUMA_CONFIG_FILE, 'w');
		if ($lh == FALSE)
			return gu_error(t("Unable to create/open % file for writing",array(GUTUMA_CONFIG_FILE)));
		
		fwrite($lh, "\$gu_config_version = ".GUTUMA_VERSION_NUM.";\n");
		foreach (array_keys(self::$values) as $key) {
			if (is_bool(self::$values[$key]))
				fwrite($lh, "\$gu_config['".$key."'] = ".(self::$values[$key] ? 'TRUE' : 'FALSE').";\n");		
			elseif (is_numeric(self::$values[$key]))
				fwrite($lh, "\$gu_config['".$key."'] = ".self::$values[$key].";\n");
			else
				fwrite($lh, "\$gu_config['".$key."'] = '".str_replace(array('\"',"'"),array('"',"\'"),self::$values[$key])."';\n");
		}
		fclose($lh);
		
		$f = file_get_contents(GUTUMA_CONFIG_FILE);
		// Version encodée (voir ligne 185)
		file_put_contents(GUTUMA_CONFIG_FILE,"<?php /*\n".base64_encode($f)."\n*/  ?>");
		// Version décodée (voir ligne 187)
		/* file_put_contents(GUTUMA_CONFIG_FILE,"<?php \n".$f."\n?>");*/
		return TRUE;
	}
	
	/**
	 * Méthode qui retourne une chaine de caractères au hasard
	 *
	 * @param	taille		nombre de caractère de la chaine à retourner (par défaut sur 10 caractères)
	 * @return	string		chaine de caractères au hasard
	 * @author	Florent MONTHEL et Stephane F
	 **/
	public static function plx_charAleatoire($taille='10') {
	
		$string = '';
		$chaine = 'abcdefghijklmnpqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
		mt_srand((float)microtime()*1000000);
		for($i=0; $i<$taille; $i++)
			$string .= $chaine[ mt_rand()%strlen($chaine) ];
		return $string;
	}
}

?>