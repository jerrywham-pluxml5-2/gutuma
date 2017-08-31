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
 * @version 1.4
 * @date	23/09/2013
 * @author	Cyril MAGUIRE
*/

# Définition des constantes
$output = array();exec('pwd', $output);define('__LINK__', $output[0].substr(__FILE__, strpos(__FILE__, DIRECTORY_SEPARATOR)));//in gadget.js.php
$gdgt='';
if(strstr($_SERVER['PHP_SELF'],'gadgets.js.php')){//4 gadget call
	$gdgt='../';
	header('Content-Type: application/x-javascript');
}
define('PLX_ROOT',$gdgt.'../../../');
define('PLX_CORE', PLX_ROOT.'core/');
define('PLX_GROOT', str_replace($_SERVER['PHP_SELF'],'',$_SERVER['SCRIPT_FILENAME']).'/');//gu_call_path :) duckduckgo.com/?t=lm&q=PHP+SOLVE+FOLDER+SYMLINK&ia=qa ::: 4 memory : $gu_real_path = str_replace('plugins/gutuma/news/inc/_pluxml.php','',__FILE__);
define('PLX_MORE', PLX_GROOT.'core/');
include(PLX_GROOT.'config.php');
include(PLX_MORE.'lib/config.php');
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
#		header('Location: '.PLX_CORE.'admin/auth.php?p='.htmlentities($_SERVER['REQUEST_URI']));//add '.PLX_CORE.'admin/ 4 good page url but p query param is'nt recognized after redirected
#		exit;
#	}
#}

# On inclut les librairies nécessaires
include_once(PLX_MORE.'lib/class.plx.date.php');
include_once(PLX_MORE.'lib/class.plx.glob.php');
include_once(PLX_MORE.'lib/class.plx.utils.php');
include_once(PLX_MORE.'lib/class.plx.msg.php');
include_once(PLX_MORE.'lib/class.plx.record.php');
include_once(PLX_MORE.'lib/class.plx.motor.php');
include_once(PLX_MORE.'lib/class.plx.admin.php');
include_once(PLX_MORE.'lib/class.plx.encrypt.php');
include_once(PLX_MORE.'lib/class.plx.medias.php');
include_once(PLX_MORE.'lib/class.plx.plugins.php');
include_once(PLX_MORE.'lib/class.plx.token.php');

include_once(PLX_MORE.'lib/class.plx.capcha.php');
include_once(PLX_MORE.'lib/class.plx.erreur.php');
include_once(PLX_MORE.'lib/class.plx.feed.php');
include_once(PLX_MORE.'lib/class.plx.show.php');
# Creation de l'objet principal et lancement du traitement
$plxMotor = plxMotor::getInstance();
$lang = $glang = $plxMotor->aConf['default_lang'];
# Chargement des fichiers de langue en fonction du profil de l'utilisateur connecté
if(isset($_SESSION['user'])) $lang = $glang = $plxMotor->aUsers[$_SESSION['user']]['lang'];
$_SESSION['lang'] = $_SESSION['glang'] = $lang;
# Chargement des fichiers de langue
loadLang(PLX_MORE.'lang/'.$lang.'/core.php');
loadLang(PLX_MORE.'lang/'.$lang.'/admin.php');
$plxMotor->mode='gutuma';//4 future ::: & solved bug header 404 in demarrage & prechauffage funct() but in reality is'nt util here (more perf ;-)
#$plxMotor->prechauffage();#origin //bug header 404 when ? is in uri (admin) el motor go to mode error, sin mode home ;-) [non blocant] semble être inutile
#$plxMotor->demarrage();#origin semble inutile de l'appeler maintenant (maybe not: intest)
# Creation de l'objet d'affichage
$plxShow = plxShow::getInstance();
if(isset($_SESSION['user']) AND !empty($_SESSION['user'])) {
	$_profil = $plxMotor->aUsers[$_SESSION['user']];// _profil ONLY CALLED IN INSTALL.PHP
	$plxMotor->mode='gutumadmin';
}//grant access 4 public php files subscript mode ::: other redirect (if not connected in PluXml backend)
elseif(strpos($plxMotor->path_url,'news/ajax.php') === FALSE  && strpos($plxMotor->path_url,'news/js/gadgets.js.php') === FALSE && strpos($plxMotor->path_url,'news/subscribe.php') === FALSE){//gestion des abonnements (publics)
	header('Location:'.PLX_CORE.'admin/auth.php?p=plugin.php?p=gutuma');
	exit();
}