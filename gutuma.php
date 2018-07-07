<?php
/**
 * Classe gutuma
 * @version 1.9.1 * @date	07/07/2018 * @author	Thomas Ingles
 * @version 1.8 * @date	01/09/2017 * @author	Thomas Ingles
 * @version 1.6 * @date	01/10/2013 * @author	Cyril MAGUIRE
 **/
class gutuma extends plxPlugin {
	public $code;
	public $release;
	public $listsDir;
	public function __construct($default_lang){
		$this->listsDir = PLX_ROOT.'data/'.__CLASS__;# Définition de l'emplacement des listes de diffusion des newsletters : next PLX_ROOT.PLX_CONFIG_PATH.'plugins/'.__CLASS__;#tmp (uploads) & save .eml
		parent::__construct($default_lang);# appel du constructeur de la classe plxPlugin (obligatoire)
		$this->setAdminProfil(PROFIL_ADMIN, PROFIL_MANAGER);# Autorisation d'accès à l'administration du plugin
		if(defined('PLX_ADMIN')) {#Déclaration des hooks pour la zone d'administration
			$this->setAdminMenu($this->getLang('L_ADMIN_MENU_NAME'), 0, $this->getLang('L_ADMIN_TITLE_MENU'));#Position du Menu : remplacer 0 par tout autre chiffre
			$this->addHook('AdminTopBottom', 'AdminTopBottom');
			$this->addHook('plxAdminEditUsersXml', 'plxAdminEditUsersXml');
		}
	}
	public function v(){//Méthode qui crée les constantes de version (info.xml) a transmettre à gutuma.php
		$this->code = $this->getInfo('version');#Friendly v name
		$this->release = str_replace('.','',$this->getInfo('date'));#v release time YYYYMMDDHH
	}
	public function setGutumaUser(&$plxAdmin,$GU_users,$id){//Méthode qui retourne la MAJ : (array) $gu_config['users'] serialisé
#		var_dump(__CLASS__,__FUNCTION__,$GU_users,$id,$plxAdmin->aUsers[$id]);//exit;//dbg
		$aProfils = $this->aProfils();# Tableau des profils
		$GU_users = unserialize($GU_users);//var_dump('gutuma setGutumaUser $GU_users:',$GU_users);exit;
		$GU_users[$plxAdmin->aUsers[$id]['name']] = array(
			'id'=>$id,
			'login'=>$plxAdmin->aUsers[$id]['login'],
			'password'=>$plxAdmin->aUsers[$id]['password'],
			'salt'=>$plxAdmin->aUsers[$id]['salt'],
			'profil'=>$aProfils[$plxAdmin->aUsers[$id]['profil']]
		);
#		var_dump(__CLASS__,__FUNCTION__,$GU_users,$id,$plxAdmin->aUsers[$id]);//exit;//dbg
		return serialize($GU_users);//$gu_config['users']
	}
	public function aProfils(){//Méthode qui retourne le Tableau des profils PluXml
  return array(
   PROFIL_ADMIN => L_PROFIL_ADMIN,
   PROFIL_MANAGER => L_PROFIL_MANAGER,
   PROFIL_MODERATOR => L_PROFIL_MODERATOR,
   PROFIL_EDITOR => L_PROFIL_EDITOR,
   PROFIL_WRITER => L_PROFIL_WRITER
  );
	}
	public function getGutumaConfig(){//Méthode qui retourne gutuma/inc/config.php a éval(ué) (faux si fichier absent)
		//var_dump(__CLASS__,__FUNCTION__/*,$plxAdmin*/);//WIt
		if (file_exists($this->listsDir.'/inc/config.php')){//Le fichier de config existe donc le module est installé
			// Récupération de la config de Gutuma
			//echo base64_decode(substr(file_get_contents($this->listsDir.'/inc/config.php'),9,-5));// désencodé & affiché, si besoin est
			//return substr(file_get_contents($this->listsDir.'/inc/config.php'),7,-4);// Version décodée
			return base64_decode(substr(file_get_contents($this->listsDir.'/inc/config.php'),9,-5));
			//return true;
		}
		return FALSE;
	}
	public function setGutumaConfig($gu_config){//Méthode qui enregistre les MAJ ds gutuma/inc/config.php
		$this->v();#populate $this->release (& code)
		$GU_config = "\$gu_config_version = $this->release;\n";
		foreach($gu_config as $key => $value){
			$GU_config .="\$gu_config['$key'] = ".($value===false ? "FALSE" : ($value === true ? "TRUE" : "'$value'")).";\n";
		}
		// Version encodée
		file_put_contents($this->listsDir.'/inc/config.php',"<?php /*\n".base64_encode($GU_config)."\n*/  ?>");
		// Version décodée
		/*file_put_contents($this->listsDir.'/inc/config.php',"<?php \n".$GU_config."\n?>");*/
	}
	public function onUpdate(){
		//return array('cssCache' => true);#mise a jour du cache des css
	}
	/**
	 * Méthode qui affiche un message s'il y a un message à afficher
	 * @return	stdio
	 * @author	Stephane F, Cyril MAGUIRE
	 **/
	public function AdminTopBottom(){
		$string = 'if(empty($plxAdmin->aUsers["001"]["email"])) {
			echo "<p class=\"warning\">Plugin '.$this->getLang("L_ADMIN_MENU_NAME").'<br />'.$this->getLang("L_ERR_EMAIL").'</p>";
			plxMsg::Display();
		}';
		echo '<?php '.$string.' ?>';
	}
	/**
	 * Méthode qui supprime les utilisateurs de la liste des utilisateurs valides (fichier parameters.xml)
	 * L'utilisateur n'est pas véritablement supprimé de la liste mais son statut passe de activé/désactivé à supprimé
	 * @author Cyril MAGUIRE, Thomas Ingles
	 */
	public function plxAdminEditUsersXml(){
		echo '<?php '; ?>
		$Gutumaction = FALSE;
		if(!isset($Gutuma))
			$Gutuma = $this->plxPlugins->aPlugins['gutuma'];
		if($user_id!='001'){
			if(!$user['active'] OR $user['profil']>PROFIL_MANAGER OR $user['delete']){
				if(!$Gutuma->delParam('user_'.$user_id))//unset($Gutuma->aParams['user_'.$user_id]);//delParam
					$Gutuma->setParam('user_'.$user_id, 'desactivé', 'cdata');//au cas ou
				$Gutumaction = TRUE;
			}
		}
		if($Gutumaction)	$Gutuma->saveParams();
/* update password, login, name by user MANAGER & admin */
		if($user_id!='001' AND $user['active'] AND $user['profil'] <= PROFIL_MANAGER){
			$Gutuma_usr = $Gutuma->getParam('user_'.$user_id);
			if($Gutuma_usr == 'activé'){
				$ok_config = $Gutuma->getGutumaConfig();#On charge la config de gutuma : $gu_config_version & $gu_config[]
				if($ok_config){//Le fichier de config existe donc le module est installé
					eval($ok_config);//var_dump($gu_config_version,$gu_config);exit;
					$ok_config = TRUE;
					$gu_config['users'] = $Gutuma->setGutumaUser($this,$gu_config['users'],$user_id);#On Met A Jour l'utilisateur
					$Gutuma->setGutumaConfig($gu_config);#On sauve la config
				}
			}
		}

?><?php
	}
}