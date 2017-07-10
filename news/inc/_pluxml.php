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
/* Gutama plugin package
 *  @version 1.4
 * @date	23/09/2013
 * @author	Cyril MAGUIRE
*/
//var_dump('gutuma _pluxml',$_SERVER);
# Définition des constantes
//~ define('PLX_ROOT', $_SERVER['DOCUMENT_ROOT'].substr($_SERVER['SCRIPT_NAME'],0,strpos($_SERVER['SCRIPT_NAME'], 'plugins')));#4symlink::bug if plug use PLX_ROOT IN ADmin (favicon)
//~ define('PLX_ROOT', '../../');#Normal?bug: in plugins/gutuma/news/js/gadgets.js.php:::Warning: include(../../config.php): failed to open stream: No such file or directory in /var/www/0.src.blogs/plx_plugs_git/gutuma/news/inc/_pluxml.php on line 24
//~ define('PLX_ROOT', '../../../../');#New [in test]:dac 4 'PHP_SELF' => string '/plugins/gutuma/news/js/gadgets.js.php' (length=38)
$gdgt=strstr($_SERVER['PHP_SELF'],'gadgets.js.php')?'../':'';
define('PLX_ROOT', $gdgt.'../../../');
define('PLX_CORE', PLX_ROOT.'core/');
include(PLX_ROOT.'config.php');
include(PLX_CORE.'lib/config.php');
define('PLX_CONF', PLX_ROOT.'data/configuration/parametres.xml');

# On verifie que PluXml est installé
if(!file_exists(PLX_CONF)) {
	header('Location: '.PLX_ROOT.'install.php');
	exit;
}

# On démarre la session
session_start();

# On inclut les librairies nécessaires
include(PLX_CORE.'lib/class.plx.date.php');
include(PLX_CORE.'lib/class.plx.glob.php');
include(PLX_CORE.'lib/class.plx.utils.php');
include(PLX_CORE.'lib/class.plx.capcha.php');
include(PLX_CORE.'lib/class.plx.erreur.php');
include(PLX_CORE.'lib/class.plx.record.php');
include(PLX_CORE.'lib/class.plx.motor.php');
include(PLX_CORE.'lib/class.plx.feed.php');
include(PLX_CORE.'lib/class.plx.show.php');
include(PLX_CORE.'lib/class.plx.encrypt.php');
include(PLX_CORE.'lib/class.plx.plugins.php');

include(PLX_CORE.'lib/class.plx.admin.php');

# Creation de l'objet principal et lancement du traitement
$plxMotor = plxMotor::getInstance();

# Chargement des fichiers de langue en fonction du profil de l'utilisateur connecté
$lang = $glang = $plxMotor->aConf['default_lang'];
$_SESSION['lang'] = $_SESSION['glang'] = $lang;
# Chargement des fichiers de langue
loadLang(PLX_CORE.'lang/'.$lang.'/core.php');
loadLang(PLX_CORE.'lang/'.$lang.'/admin.php');
$plxMotor->mode='gutuma';
//$plxMotor->prechauffage();##origin //bug header 404 when ? is in uri (admin) el motor go to mode error, sino mode home ;-) [non blocant] semble être inutile
$plxMotor->demarrage();#origin semble inutile ici

# Creation de l'objet d'affichage
$plxShow = plxShow::getInstance();

if(isset($_SESSION['user']) AND !empty($_SESSION['user'])) { 
	$_profil = $plxMotor->aUsers[$_SESSION['user']];// _profil ONLY CALLED IN INSTALL.PHP
}elseif(strpos($plxMotor->path_url,'news/ajax.php') === FALSE  && strpos($plxMotor->path_url,'news/js/gadgets.js.php') === FALSE && strpos($plxMotor->path_url,'news/subscribe.php') === FALSE){//gestion des abonnements (publics)
	header('Location:'.PLX_CORE.'admin/auth.php?p=plugin.php?p=gutuma');
	exit();
}