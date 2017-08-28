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
 *  @version 1.4
 * @date	23/09/2013
 * @author	Cyril MAGUIRE
*/

# Définition des constantes
$gu_real_path = str_replace('plugins/gutuma/news/inc/_pluxml.php','',__FILE__);
$gu_call_path = str_replace($_SERVER['PHP_SELF'],'',$_SERVER['SCRIPT_FILENAME']);
//duckduckgo.com/?t=lm&q=PHP+SOLVE+FOLDER+SYMLINK&ia=qa
$output = array();
exec('pwd', $output);
define('__LINK__', $output[0].substr(__FILE__, strpos(__FILE__, DIRECTORY_SEPARATOR)));
$gdgt='';
if(strstr($_SERVER['PHP_SELF'],'gadgets.js.php')){//4 gadget call
	$gdgt='../';
	header('Content-Type: application/x-javascript');
}
define('PLX_ROOT',$gdgt.'../../../');
define('PLX_CORE', PLX_ROOT.'core/');
define('PLX_GROOT', $gu_call_path.'/');
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
/*
if(!defined('PLX_AUTHPAGE') OR PLX_AUTHPAGE !== true){ # si on est pas sur la page de login
	# Test sur le domaine et sur l'identification
	if((isset($_SESSION['domain']) AND $_SESSION['domain']!=$session_domain) OR (!isset($_SESSION['user']) OR $_SESSION['user']=='')){
		header('Location: auth.php?p='.htmlentities($_SERVER['REQUEST_URI']));
		exit;
	}
}
*/
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
# Chargement des fichiers de langue en fonction du profil de l'utilisateur connecté
$lang = $glang = $plxMotor->aConf['default_lang'];
$_SESSION['lang'] = $_SESSION['glang'] = $lang;
# Chargement des fichiers de langue
loadLang(PLX_MORE.'lang/'.$lang.'/core.php');
loadLang(PLX_MORE.'lang/'.$lang.'/admin.php');
$plxMotor->mode='gutuma';
//$plxMotor->prechauffage();##origin //bug header 404 when ? is in uri (admin) el motor go to mode error, sino mode home ;-) [non blocant] semble être inutile
$plxMotor->demarrage();#origin semble inutile ici
# Creation de l'objet d'affichage
$plxShow = plxShow::getInstance();
if(isset($_SESSION['user']) AND !empty($_SESSION['user'])) {
	$_profil = $plxMotor->aUsers[$_SESSION['user']];// _profil ONLY CALLED IN INSTALL.PHP
	$plxMotor->mode='gutumadmin';

}elseif(strpos($plxMotor->path_url,'news/ajax.php') === FALSE  && strpos($plxMotor->path_url,'news/js/gadgets.js.php') === FALSE && strpos($plxMotor->path_url,'news/subscribe.php') === FALSE){//gestion des abonnements (publics)
	header('Location:'.PLX_CORE.'admin/auth.php?p=plugin.php?p=gutuma');
	exit();
}