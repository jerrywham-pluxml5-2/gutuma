<?php
/**
 * Classe gutuma
 *
 * @version 1.6
 * @date	01/10/2013
 * @author	Cyril MAGUIRE
 **/
class gutuma extends plxPlugin {

	public $listsDir;

	public function __construct($default_lang) {

		# appel du constructeur de la classe plxPlugin (obligatoire)
		parent::__construct($default_lang);

		# Définition de l'emplacement des listes de diffusion des newsletters
		$this->listsDir = PLX_ROOT.'data/gutuma';

		# Autorisation d'accès à l'administration du plugin
		$this->setAdminProfil(PROFIL_ADMIN, PROFIL_MANAGER);

		# Déclarations des hooks
		$this->addHook('AdminTopBottom', 'AdminTopBottom');
		$this->addHook('plxAdminEditUsersXml', 'plxAdminEditUsersXml');

	}

	/**
	 * Méthode qui affiche un message s'il y a un message à afficher
	 *
	 * @return	stdio
	 * @author	Stephane F, Cyril MAGUIRE
	 **/
	public function AdminTopBottom() {

			$string = 'if(empty($plxAdmin->aUsers["001"]["email"])) {
				echo "<p class=\"warning\">Plugin Gutuma<br />'.$this->getLang("L_ERR_EMAIL").'</p>";
				plxMsg::Display();
			}';
			echo '<?php '.$string.' ?>';
	}

	/**
	 * Méthode qui supprime les utilisateurs de la liste des utilisateurs valides (fichier parameters.xml)
	 * L'utilisateur n'est pas véritablement supprimé de la liste mais son statut passe de activé/désactivé à supprimé
	 * 
	 * @author Cyril MAGUIRE
	 */
	public function plxAdminEditUsersXml(){
		$string =<<<END
		\$Gutuma = \$this->plxPlugins->aPlugins["gutuma"];
		if(!empty(\$content['selection']) AND \$content['selection']=='delete' AND isset(\$content['idUser'])) {
			foreach(\$content['idUser'] as \$user_id) {
				if(\$content['selection']=='delete' AND \$user_id!='001') {
					\$Gutuma->setParam('user_'.\$user_id, 'supprimé', 'cdata');
				}
			}
			\$Gutuma->saveParams();
		}
END;
		echo '<?php '.$string.'?>';
	}
}
?>
