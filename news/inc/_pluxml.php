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
 * @version 1.8.4
 * @date	01/09/2017
 * @author	Cyril MAGUIRE, Thomas I.
*/
# Définition des constantes
define('__GDS__', DIRECTORY_SEPARATOR);//Gutuma Dir. Sep. 4 nux|dow
$gdgt='';
if(strstr($_SERVER['PHP_SELF'],'gadgets.js.php')){//4 gadget call
	$gdgt='..'.__GDS__;
	header('Content-Type: application/x-javascript');
}
define('PLX_GROOT', $gdgt.'..'.__GDS__.'..'.__GDS__.'..'.__GDS__);// GROOT 4 SYMBIOLINK
define('PLX_MORE', PLX_GROOT.'core'.__GDS__);
//PLX_ROOT détermine le chemin des params de XMLFILE_PARAMETERS * uncomment this 3 lines (below) if gutuma is symlinked in an other PluXml (I use it 4 my dev Thom@s)
#$gu_sub = explode('plugins',$_SERVER['DOCUMENT_ROOT'].$_SERVER['PHP_SELF']);
#$gu_sub = str_replace($_SERVER['DOCUMENT_ROOT'].__GDS__,'',$gu_sub[0]);//4 found subdir where plx is
#define('PLX_ROOT',$_SERVER['DOCUMENT_ROOT'].__GDS__.$gu_sub);// OR PLX_GROOT
define('PLX_ROOT', PLX_GROOT);# Normal config, gutuma is in plugins folder 4 real * comment this line if gutuma is symlinked & in an other PluXml
define('PLX_CORE', PLX_ROOT.'core'.__GDS__);

include(PLX_ROOT.'config.php');
include(PLX_CORE.'lib'.__GDS__.'config.php');
# On verifie que PluXml est installé
if(!file_exists(path('XMLFILE_PARAMETERS'))) {
	header('Location: '.PLX_ROOT.'install.php');
	exit;
}
# On démarre la session
session_start();

$session_domain = dirname(__FILE__);
#::see bottom of this file:: This modified original code of PluXml is Commented because bad redirect pages & ajax / subscribe / gadget in public mode to plugins/gutuma/news/auth.php?p=plugins/gutuma/news/*pageRequested*.php
#if(!defined('PLX_AUTHPAGE') OR PLX_AUTHPAGE !== true){ # si on est pas sur la page de login
#	# Test sur le domaine et sur l'identification
#	if((isset($_SESSION['domain']) AND $_SESSION['domain']!=$session_domain) OR (!isset($_SESSION['user']) OR $_SESSION['user']=='')){
#		header('Location: '.PLX_MORE.'admin/auth.php?p='.htmlentities($_SERVER['REQUEST_URI']));//add '.PLX_CORE.'admin/ 4 good page url but p query param is'nt recognized after redirected
#		exit;
#	}
#}

# On inclut les librairies nécessaires
include_once(PLX_CORE.'lib/class.plx.date.php');
include_once(PLX_CORE.'lib/class.plx.glob.php');
include_once(PLX_CORE.'lib/class.plx.utils.php');
include_once(PLX_CORE.'lib/class.plx.msg.php');
include_once(PLX_CORE.'lib/class.plx.record.php');
include_once(PLX_CORE.'lib/class.plx.motor.php');
include_once(PLX_CORE.'lib/class.plx.admin.php');
include_once(PLX_CORE.'lib/class.plx.encrypt.php');
include_once(PLX_CORE.'lib/class.plx.medias.php');
include_once(PLX_CORE.'lib/class.plx.plugins.php');
include_once(PLX_CORE.'lib/class.plx.token.php');

include_once(PLX_CORE.'lib/class.plx.capcha.php');
include_once(PLX_CORE.'lib/class.plx.erreur.php');
include_once(PLX_CORE.'lib/class.plx.feed.php');
include_once(PLX_CORE.'lib/class.plx.show.php');
# Creation de l'objet principal et lancement du traitement
$plxMotor = plxMotor::getInstance();
$lang = $glang = $plxMotor->aConf['default_lang'];
# Chargement des fichiers de langue en fonction du profil de l'utilisateur connecté
if(isset($_SESSION['user'])) $lang = $glang = $plxMotor->aUsers[$_SESSION['user']]['lang'];
$_SESSION['lang'] = $_SESSION['glang'] = $lang;
# Chargement des fichiers de langue
loadLang(PLX_CORE.'lang/'.$lang.'/core.php');
loadLang(PLX_CORE.'lang/'.$lang.'/admin.php');
$plxMotor->mode='gutuma';//4 future ::: & solved bug header 404 in demarrage & prechauffage funct() but in reality is'nt util here (more perf ;-)
#$plxMotor->prechauffage();#origin //bug header 404 when ? is in uri (admin) el motor go to mode error, sin mode home ;-) [non blocant] semble être inutile
#$plxMotor->demarrage();#origin semble inutile de l'appeler maintenant (maybe not: intest)
# Creation de l'objet d'affichage
$plxShow = plxShow::getInstance();//var_dump('_pluxml: ',path('XMLFILE_PARAMETERS'),$plxMotor,$plxShow);exit;
if(isset($_SESSION['user']) AND !empty($_SESSION['user'])) {
	$_profil = $plxMotor->aUsers[$_SESSION['user']];// _profil ONLY CALLED IN INSTALL.PHP
	$plxMotor->mode='gutumadmin';
}//grant access 4 public php files subscript mode ::: other redirect (if not connected in PluXml backend)
elseif(strpos($plxMotor->path_url,'news/ajax.php') === FALSE  && strpos($plxMotor->path_url,'news/js/gadgets.js.php') === FALSE && strpos($plxMotor->path_url,'news/subscribe.php') === FALSE){//gestion des abonnements (publics)
	header('Location:'.PLX_MORE.'admin/auth.php?p=plugin.php?p=gutuma');
	exit();
}