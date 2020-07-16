<?php
# ------------------ BEGIN LICENSE BLOCK ------------------
#
# This file is part of PluXml : http://www.pluxml.org
#
# Copyright © 2010-2011 Stephane Ferrari and contributors
# Copyright © 2008-2009 Florent MONTHEL and contributors
# Copyright © 2006-2008 Anthony GUERIN
# Licensed under the GPL license.
# See http://www.gnu.org/licenses/gpl.html
#
# ------------------- END LICENSE BLOCK -------------------
/* Gutama plugin package (from core/admin/prepend.php)
 * @version 2.2.1
 * @date	16/07/2020
 * @author	Thomas INGLES.
*/
# Définition des constantes
define('__GRN__', "\r\n");//Gutuma EOL
define('__GDS__', DIRECTORY_SEPARATOR);//Gutuma Dir. Sep. 4 nux|dow
$gdgt='';
if(strstr($_SERVER['PHP_SELF'],'gadgets.js.php')){//4 gadget call
	$gdgt='..'.__GDS__;
	header('Content-Type: application/x-javascript');
}
define('PLX_GROOT', $gdgt.'..'.__GDS__.'..'.__GDS__.'..'.__GDS__);// GROOT 4 SYMBIOLINK
define('PLX_MORE', PLX_GROOT.'core'.__GDS__);
if(defined('PLX_ROOT')){;#Test for include (fix error of Twice PluXml)
 $plxMotor = $this->plxMotor;
 $lang = $glang = $plxMotor->aConf['default_lang'];
 $gu_is_included = TRUE;
 return;
}
# PLX_ROOT détermine le chemin des params de XMLFILE_PARAMETERS * uncomment this 3 lines (below) if gutuma is symlinked in an other PluXml (I use it 4 my dev Thom@s)
#$gu_sub = explode('plugins',$_SERVER['DOCUMENT_ROOT'].$_SERVER['PHP_SELF']);#if gutuma is symlinked
#$gu_sub = str_replace($_SERVER['DOCUMENT_ROOT'].__GDS__,'',$gu_sub[0]);#4 found subdir where plx is
#define('PLX_ROOT',$_SERVER['DOCUMENT_ROOT'].__GDS__.$gu_sub);// OR PLX_GROOT **AND UNCOMMENT THIS
define('PLX_ROOT', PLX_GROOT);# Normal config, gutuma is in plugins folder 4 real * comment this line if gutuma is symlinked & in an other PluXml**

# On démarre la session
session_start();
#ready for next gen ? ;) like 5.8.4, 5.9, 6.0 ...
$glx_version = '5.3.1';#5.8.3
if(isset($_SESSION['GUTUMA_PLX_VERSION'])){#created in admin.php plugin access page
	$glx_version = $_SESSION['GUTUMA_PLX_VERSION'];
}
#Solve # FIX PLX_CONFIG_PATH & hide error of Multiple Versions on same server by #captbuffer
ob_start();
if(version_compare($glx_version,'5.8.4','<')){# 5.8.3 & Olds
	define('PLX_CORE', PLX_ROOT.'core'.__GDS__);#fix : PLX_CORE already defined in PluXml/core/lib/config.php 5.9 & 6.0
	include(PLX_ROOT.'config.php');# FIX PLX_CONFIG_PATH
}
include(PLX_ROOT.'core'.__GDS__.'lib'.__GDS__.'config.php');//hide error
ob_end_clean();#clear buffer to hide errors if need redirect

#PLX_VERSION #created in 5.5
$_SESSION['GUTUMA_PLX_VERSION'] = defined('PLX_VERSION')? PLX_VERSION : '5.3.1';//solve inter version in same server (same php session)

# On verifie quel PluXml est installé
if($_SESSION['GUTUMA_PLX_VERSION'] != $glx_version) {#reload if needed
	$glx_version = $_SESSION['GUTUMA_PLX_VERSION'];
  $_SESSION = array(); //destroy all of the session variables  //~ session_destroy();
  $_SESSION['GUTUMA_PLX_VERSION'] = $glx_version;//solve inter version in same server (same php session)
	//~ header('Location: ' . PLX_MORE . 'admin' . __GDS__ . 'plugin.php?p=gutuma');#si moteur different : on recharge ;)
	header('Location: ');#si moteur different : on recharge la page & retour admin/auth ;)
	exit;
}
#END ready for next gen maybe? ;)

# On démarre la session
#session_start();

$session_domain = dirname(__FILE__);
#::see bottom at this file:: This modified original code of PluXml is Commented because bad redirect pages & ajax / subscribe / gadget in public mode to plugins/gutuma/news/auth.php?p=plugins/gutuma/news/*pageRequested*.php
#if(!defined('PLX_AUTHPAGE') OR PLX_AUTHPAGE !== true){ # si on est pas sur la page de login
#	# Test sur le domaine et sur l'identification
#	if((isset($_SESSION['domain']) AND $_SESSION['domain']!=$session_domain) OR (!isset($_SESSION['user']) OR $_SESSION['user']=='')){
#		header('Location: '.PLX_MORE.'admin/auth.php?p='.htmlentities($_SERVER['REQUEST_URI']));//add '.PLX_CORE.'admin/ 4 good page url but p query param is'nt recognized after redirected
#		exit;
#	}
#}

# On inclut les librairies nécessaires
foreach(explode('·','date·glob·utils·msg·record·motor·admin·encrypt·medias·plugins·token·capcha·erreur·feed·show') as $glx_class)
 include_once(PLX_CORE.'lib/class.plx.'.$glx_class.'.php');

# Creation de l'objet principal et lancement du traitement
//$plxShow = plxShow::getInstance();//call plxMotor::getInstance() && FIX* myMultiLingue CONSTANT already defined
$plxMotor = $plxAdmin = @plxAdmin::getInstance();//Fatal error: Uncaught Error: Call to undefined method plxMotor::checkProfil() plugins/gutuma/news/themes/default/_menu.php on line <i>124 :::old: $plxShow->plxMotor; # @ fix Notice: Constant PLX_SITE_LANG already defined in pluxml.5.8.3/core/lib/class.plx.motor.php on line 76

if (!isset($plxMotor->plxPlugins->aPlugins['gutuma'])){//if deactivated goto erreur 4 all news system
	header('Location:'.PLX_GROOT.'erreur');
	exit;
}
$lang = $glang = $plxMotor->aConf['default_lang'];
# Chargement des fichiers de langue en fonction du profil de l'utilisateur connecté
if(isset($_SESSION['user'])) $lang = $glang = $plxMotor->aUsers[$_SESSION['user']]['lang'];
$_SERVER['gu_lang'] = $_SERVER['gu_langs'] = $lang;
# Chargement des fichiers de langue
loadLang(PLX_CORE.'lang/'.$lang.'/core.php');
loadLang(PLX_CORE.'lang/'.$lang.'/admin.php');
$plxMotor->mode='gutuma';//4 future ::: & solved bug header 404 in demarrage & prechauffage funct() but in reality is'nt util here (more perf ;-)
#$plxMotor->prechauffage();#origin //bug header 404 when ? is in uri (admin) el motor go to mode error, sin mode home ;-) [non blocant] semble être inutile
#$plxMotor->demarrage();#origin :: inutile de l'appeler maintenant
# Creation de l'objet d'affichage*
#$plxShow = plxShow::getInstance();# origin :: FIXED* myMultiLingue ::: MML CALL PLX_MY_MULTILINGUE TWICE ::: $plxShow NOT IN global

# Pages publiques
switch(true){
 case strpos($plxMotor->path_url,'news/ajax.php') !== FALSE:
 case strpos($plxMotor->path_url,'news/js/gadgets.js.php') !== FALSE:
 case strpos($plxMotor->path_url,'news/subscribe.php') !== FALSE:
  $gu_front = TRUE;# grant access 4 public php files subscript mode
 break;
 default:
  $gu_front = FALSE;# other redirect (if not connected in PluXml backend)
}

if(!$gu_front) {# Back office (admin)
	$access = TRUE;
	if(isset($_SESSION['user']) AND !empty($_SESSION['user'])) {#Si connecté
		$_profil = $plxMotor->aUsers[$_SESSION['user']];// $_profil is called in install.php
		if(!$_profil['active'] OR $_profil['profil']>PROFIL_MANAGER OR $_profil['delete']){//déconnecte l'utilisteur si n'est plus autorisé
			$access = FALSE;
		}
	}else{#déconnecté, session expirée
		$access = FALSE;
	}
	if(!$access){#refused access
		gu_session_set_valid(FALSE);
		header('Location:'.PLX_MORE.'admin/plugin.php?p=gutuma');#on redirige a l'admin de PluXml, qui va rediriger vers l'espace de connexion
		exit;
	}
	$plxMotor->mode='gutumadmin';
}
#echo '//hello000'.PHP_EOL;exit;
if(!defined('PLX_VERSION')) define('PLX_VERSION',$plxMotor->aConf['version']);//$plxMotor->version (<=5.4), PLX_VERSION (>=5.5) ::: aConf['version'] tous ;)
define('THEMEVERS', 532 < str_pad(str_replace('.','',PLX_VERSION),  3, '0', STR_PAD_RIGHT)?'':'.5.3.1');//if PluXml < to 5.3.1 use retail old header, menu & footer (.5.3.1.php)

#var_dump(THEMEVERS,PLX_VERSION);EXIT;# Hook Plugins
if(!$gu_front) eval($plxAdmin->plxPlugins->callHook('AdminPrepend'));# protect plnkikan is called twice in plugins/gutuma/news/js/gadgets.js.php
