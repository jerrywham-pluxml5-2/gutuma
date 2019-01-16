<?php
/**
 * Classe gutuma
 * @version 2.2.0 * @date	16/01/2019 * @author	Thomas Ingles
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
			$this->setAdminMenu($this->getLang('L_GUTUMA_MENU_NAME'), 0, $this->getLang('L_GUTUMA_TITLE_MENU'));#Position du Menu : remplacer 0 par tout autre chiffre
			$this->addHook('AdminProfilPrepend', 'AdminProfilPrepend');
			$this->addHook('AdminTopBottom', 'AdminTopBottom');
			$this->addHook('plxAdminEditUsersXml', 'plxAdminEditUsersXml');
			$this->addHook('AdminMediasFoot', 'AdminMediasFoot');
		}elseif($this->getParam('subscribe_is_good'))
			$this->addHook('IndexBegin', 'goodGets');
	}
 public function AdminMediasFoot(){//changement des onclic target blank en lien retour de tiny du gestionnaire des médias (popup) (Wymeditor base)
?>
<script type="text/javascript">
if (window.parent.tinyMCE && window.parent.location.pathname.search('news/compose.php') >= 0){//1 || media manager : gutuma (in compose iframe)
	sessionStorage.StickyNotes = 'none';//hide notes
	//stackoverflow.com/a/36108449 : onload chained
	if(window.onload != null){var fgfm = window.onload;}
	window.onload=function(){//restore rules
		try{//hide plxAdminBar (from stickyNotes)
			document.getElementById('plxadminbar').style.display = 'none';//hide bar
			new Array('-nocache','-icon','').forEach(function(e){//plxadminbar links
				var ab_css = document.getElementById('plxadminbar'+e+'-css');//link id
				ab_css.parentNode.removeChild(ab_css);//remove
			});
		}catch(e){if(console)console.log('#<?php echo @L_MEDIAS_TITLE . ' : ' . __CLASS__ ?> : hide plxAdminBar error: '+e,e)}
		if(fgfm!=null){fgfm();}//chained onload
	}
	function gu_GetUrlParam(varName, url){//src : roxyfileMan js util.js :  RoxyUtils.GetUrlParam
		var ret = '';
		if(!url)
			url = self.location.href;
		if(url.indexOf('?') > -1){
				url = url.substr(url.indexOf('?') + 1);
				url = url.split('&');
				for(i = 0; i < url.length; i++){
					var tmp = url[i].split('=');
					if(tmp[0] && tmp[1] && tmp[0] == varName){
						ret = tmp[1];
						break;
					}
				}
		}
		return ret;
	}
	function tinymce4(insertPath,input){//src : roxyfileMan js main.js function getPreselectedFile()
		var win = (window.opener?window.opener:window.parent);
		win.document.getElementById(input).value = insertPath;
		if	(typeof(win.ImageDialog) != "undefined") {
			if (win.ImageDialog.getImageData)
				win.ImageDialog.getImageData();
			if (win.ImageDialog.showPreviewImage)
				win.ImageDialog.showPreviewImage(insertPath);
		}
		win.tinyMCE.activeEditor.windowManager.close();
	}
//Get for Memory url query &type &input (is needed after upload)
	var	tinyType = gu_GetUrlParam('type');
	var	where = gu_GetUrlParam('input');
	try{//Fix lost query &type &input after upload
		if(tinyType){
			sessionStorage.<?php echo __CLASS__ ?>tinyType = tinyType;
		}
		else if (sessionStorage.<?php echo __CLASS__ ?>tinyType){
			tinyType = sessionStorage.<?php echo __CLASS__ ?>tinyType;
		}
		if(where){//input
			sessionStorage.<?php echo __CLASS__ ?>where = where;
		}
		else if (sessionStorage.<?php echo __CLASS__ ?>where){
			where = sessionStorage.<?php echo __CLASS__ ?>where;
		}
	}catch(e){
		if(console)console.log('SessionStorage error: '+e,e);//fallback
		if(typeof(Storage !== "undefined")){
			if(tinyType){
				localStorage.setItem("<?php echo __CLASS__ ?>tinyType", tinyType);
			}
			else if (localStorage.getItem("<?php echo __CLASS__ ?>tinyType")){
				tinyType = localStorage.getItem("<?php echo __CLASS__ ?>tinyType");
			}
			if(where){//input
				localStorage.setItem("<?php echo __CLASS__ ?>where", where)
			}
			else if (localStorage.getItem("<?php echo __CLASS__ ?>where")){
				where = localStorage.getItem("<?php echo __CLASS__ ?>where");
			}
			if(console)console.log('Attempt with Storage');
		}else{
			alert('<?php echo @L_MEDIAS_TITLE . ' : ' . __CLASS__ ?> <?php $this->lang('L_NO_JS_STORAGE') ?>');
		}
	}
	var body = document.getElementsByTagName('body');
	body = body[0];//body.id="medias";
	body.className="mediasManager";
	var tbody = document.getElementsByTagName('tbody');
	var ancres = tbody[0].getElementsByTagName('a');
	for(var i = 0; i < ancres.length; i++){
		if(ancres[i].getAttribute('onclick')){
			var str = ancres[i].getAttribute('onclick');
			var res = str.substr(0, 4);// this OR over[lay]
			if(res == 'this'){
				var type = 'document';
				if ((/\.(gif|jpg|jpeg|png|svg)$/i).test(ancres[i].href))
					type = 'image';
				else if (/\.(tb)\./i.test(ancres[i].href))
					type = 'image';//thumb
				else
					type = 'media';
/*
				if (/\.(mp3|ogg|wav)/i.test(ancres[i].href))
					type = 'media';//audio
				if (/\.(mp4|ogv|webm)/i.test(ancres[i].href))
					type = 'media';//video
*/
				if(tinyType && tinyType != type){
					ancres[i].parentElement.parentElement.style.display="none";//hide tr
				}else{
					ancres[i].setAttribute('onclick','tinymce4(this.href,where);return false;')
					ancres[i].style.color="darkgreen";//gutuma color link
					ancres[i].title = "Ajouter "+ancres[i].title+" a l'infolettre ("+type+").";
				}
			}
		}
	}
}
</script>
<?php
	}
	//IndexBegin hook restore GET's
	public function goodGets(){//Méthode qui retrouve les gets perdu si l'url de la statique est réécrte (static / gerer-mes-lettres.html)
		echo '<?php '; ?>/* gutuma goodGets IndexBegin hook */
		if($plxMotor->mode =='static' AND $plxMotor->aConf['urlrewriting']/* AND isset($plxMotor->plxPlugins->aPlugins['plxMyBetterUrls'])*/){
			$gu_subscribe_url = $plxMotor->plxPlugins->aPlugins['gutuma']->getParam('subscribe_url');
			if($gu_subscribe_url){
				$plxPage = $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
				$gu_sub_page = strstr($plxPage,$gu_subscribe_url);
				if($gu_sub_page){//IndexBegin restore GET's
					/*#://my.site/gerer-mes-infolettres.html?backlink=no&help=no&list=##########&addr=adr@e.ss&action=subscribe&k=########... */
					$gu_sub_p = parse_url($plxMotor->path_url);
					$gu_sub_q = parse_str($gu_sub_p['query'],$_GET);
				}
			}
		}
?><?php
	}//IndexEnd
	public function v(){//Méthode qui crée les variables de version (venant d'info.xml) a transmettre à gutuma.php
		$this->code = $this->getInfo('version');#Friendly v name
		$this->release = str_replace('.','',$this->getInfo('date'));#v release time YYYYMMDDHH
	}
	public function setGutumaUser(&$plxAdmin,$GU_users,$id){//Méthode qui retourne la MAJ : (array) $gu_config['users'] serialisé
		$aProfils = $this->aProfils();# Tableau des profils
		$GU_users = unserialize($GU_users);
		$GU_users[$plxAdmin->aUsers[$id]['name']] = array(
			'id'=>$id,
			'login'=>$plxAdmin->aUsers[$id]['login'],
			'password'=>$plxAdmin->aUsers[$id]['password'],
			'salt'=>$plxAdmin->aUsers[$id]['salt'],
			'profil'=>$aProfils[$plxAdmin->aUsers[$id]['profil']]
		);
		return serialize($GU_users);
	}
	public function aProfils(){//Méthode qui retourne le Tableau des profils PluXml
		return array(
			PROFIL_ADMIN => @L_PROFIL_ADMIN,
			PROFIL_MANAGER => @L_PROFIL_MANAGER,
			PROFIL_MODERATOR => @L_PROFIL_MODERATOR,
			PROFIL_EDITOR => @L_PROFIL_EDITOR,
			PROFIL_WRITER => @L_PROFIL_WRITER
		);
	}
	public function getGutumaConfig(){//Méthode qui retourne gutuma/inc/config.php a éval(ué) (faux si fichier absent)
		if (file_exists($this->listsDir.'/inc/config.php')){//Le fichier de config existe donc le module est installé
//			Récupération de la config de Gutuma
//			echo base64_decode(substr(file_get_contents($this->listsDir.'/inc/config.php'),9,-5));// désencodé & affiché, si besoin est
//			return substr(file_get_contents($this->listsDir.'/inc/config.php'),7,-4);// Version décodée
			return base64_decode(substr(file_get_contents($this->listsDir.'/inc/config.php'),9,-5));
		}
		return FALSE;
	}
	public function setGutumaConfig($gu_config){//Méthode qui enregistre les MAJ ds gutuma/inc/config.php
		$this->v();#populate $this->release (& code)
		$GU_config = "\$gu_config_version = $this->release;\n";
		foreach($gu_config as $key => $value){
			$GU_config .="\$gu_config['$key'] = ".($value===false ? "FALSE" : ($value === true ? "TRUE" : "'$value'")).";\n";
		}
//		Version encodée
		file_put_contents($this->listsDir.'/inc/config.php',"<?php /*\n".base64_encode($GU_config)."\n*/  ?>");
//		Version décodée
/*		file_put_contents($this->listsDir.'/inc/config.php',"<?php \n".$GU_config."\n?>");*/
	}
	public function onUpdate(){//si fichier update présent a la racine du plugin
		//return array('cssCache' => true);#mise a jour du cache des css
	}
	public function AdminTopBottom(){//Méthode qui affiche un message s'il y a un message à afficher * @return	stdio * @author	Stephane F, Cyril MAGUIRE
		echo '<?php '; ?>
		if(empty($plxAdmin->aUsers["001"]["email"])) {
			echo '<p class="warning">Plugin <?php echo $this->getLang('L_ADMIN_MENU_NAME') ?><br /><?php echo $this->getLang('L_ERR_EMAIL') ?></p>';
			plxMsg::Display();
		}
?><?php
	}
	public function AdminProfilPrepend(){//Méthode pour detecter si connecté (ajax test ds compose) * @return	stdio @author	Thomas Ingles
		echo '<?php '; ?>/* gutuma AdminProfilPrepend */
		if(isset($_GET['gu_plx_domain']) AND $_GET['gu_plx_domain']=='ajax'){
			if(isset($_SESSION['domain']) AND isset($_SESSION['user']) AND $_SESSION['user']!=''){
				header('Pragma: no-cache');
				header('Content-Type: application/javascript');
				echo 'gu_plx_domain = "'.$_SESSION['domain'].'";';
			}
			exit;
		}
?><?php
	}

	/**
	 * Méthode qui supprime ou met a jour les utilisateurs de la liste des utilisateurs valides (fichier parameters.xml)
	 * L'utilisateur n'est pas véritablement supprimé de la liste mais son statut passe de activé/désactivé à supprimé
	 * @author Cyril MAGUIRE, Thomas Ingles
	 */
	public function plxAdminEditUsersXml(){
		echo '<?php '; ?>/* gutuma plxAdminEditUsersXml */
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
	public function delParam($parm){
		unset($this->aParams[$parm]);return true;//delParam (pluxml 5.2)
	}
}