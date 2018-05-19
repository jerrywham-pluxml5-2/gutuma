<?php
/**
 * Classe gutuma
 * @version 1.8 * @date	01/09/2017 * @author	Thomas Ingles
 * @version 1.6 * @date	01/10/2013 * @author	Cyril MAGUIRE
 **/
class gutuma extends plxPlugin {
	public $code;
	public $release;
	public $listsDir;
	public function __construct($default_lang){
		$this->listsDir = PLX_ROOT.'data/'.__CLASS__;# Définition de l'emplacement des listes de diffusion des newsletters
		parent::__construct($default_lang);# appel du constructeur de la classe plxPlugin (obligatoire)
		$this->setAdminProfil(PROFIL_ADMIN, PROFIL_MANAGER);# Autorisation d'accès à l'administration du plugin
		if(defined('PLX_ADMIN')) {#Déclaration des hooks pour la zone d'administration
			$this->setAdminMenu($this->getLang('L_ADMIN_MENU_NAME'), 0, $this->getLang('L_ADMIN_TITLE_MENU'));#Position du Menu : remplcer 0 tout autre chiffre
			$this->addHook('AdminTopBottom', 'AdminTopBottom');
			$this->addHook('plxAdminEditUsersXml', 'plxAdminEditUsersXml');
		}
	}
	public function v(){//Méthode qui crée les constantes de version (info.xml) a transmettre à gutuma.php
		$this->code = $this->getInfo('version');#Friendly v name
		$this->release = str_replace('.','',$this->getInfo('date'));#v release time YYYYMMDDHH
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
	 * @author Cyril MAGUIRE
	 */
	public function plxAdminEditUsersXml(){
		$string =<<<END
		\$Gutumaction = FALSE;
		\$Gutuma = \$this->plxPlugins->aPlugins["gutuma"];
		if(\$user_id!='001'){
			if(!\$user['active'] OR \$user['profil']>PROFIL_MANAGER OR \$user['delete']){
				if(!\$Gutuma->delParam('user_'.\$user_id))//unset(\$Gutuma->aParams['user_'.\$user_id]);//delParam
					\$Gutuma->setParam('user_'.\$user_id, 'desactivé', 'cdata');//au cas ou
				\$Gutumaction = TRUE;
			}
		}
		if(\$Gutumaction)	\$Gutuma->saveParams();
END;
		echo '<?php '.$string.'?>';
	}
}