<?php
/************************************************************************
 * @project Gutuma Newsletter Managment
 * @author Rowan Seymour
 * @copyright This source is distributed under the GPL
 * @file included header page : Mix of pluxml/core/admin/theme/top.php v5.3.1
 * @maintainer : Thomas Ingles
 *
 * Gutama plugin package
 * @version 1.9.0
 * @date	19/05/2018
 * @author	Cyril MAGUIRE
*/

$plxMotor = plxMotor::getInstance();
?><?php if(!defined('PLX_ROOT')) exit; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="content-type" content="text/html;charset=<?php echo GUTUMA_ENCODING; ?>" />
<meta name="robots" content="none">
<script type="text/javascript" src="js/misc.min.js"></script>
<script type="text/javascript" src="js/tw-sack.js"></script>
<script type="text/javascript" src="js/md5.js"></script>
<script type="text/javascript" src="js/sha1.js"></script>
<script type="text/javascript" src="<?php echo $plxMotor->urlRewrite(); ?>core/lib/functions.js"></script>
<script type="text/javascript" src="<?php echo $plxMotor->urlRewrite(); ?>core/lib/visual.js"></script>
<link rel="stylesheet" type="text/css" href="<?php echo $plxMotor->urlRewrite(); ?>core/admin/theme/base.css" media="screen" />
<link rel="stylesheet" type="text/css" href="<?php echo $plxMotor->urlRewrite(); ?>core/admin/theme/style.css" media="screen" />
<link rel="stylesheet" type="text/css" href="themes/<?php echo gu_config::get('theme_name');?>/css/style.css" media="screen" />
<link rel="icon" type="image/png" href="themes/<?php echo gu_config::get('theme_name'); ?>/favicon.png" />
<link rel="stylesheet" type="text/css" href="themes/<?php echo gu_config::get('theme_name');?>/css/gutuma.css" media="screen" />
<!--[if IE]>
	<link rel="shortcut icon" type="image/x-icon" href="themes/<?php echo gu_config::get('theme_name'); ?>/favicon.ico" />
<![endif]-->
<title><?php echo GUTUMA_TITLE; ?></title>
<style style="display:none">
/* #send_form,#edit_form___{margin-top: -111px;} */
.menubar{margin-top: 0 !important;}
</style>
</head>

<body<?php echo ($nomenu)?' class="subscribe"':'';?>>
<?php if (gu_is_demo()) { ?>
	<div id="demobanner">DEMO MODE</div>
<?php }
if (!$nomenu) {
	if (gu_session_is_valid()) {
	include ('_menu.5.3.1.php');
	} else {
?>
<div id="sidebar">
<ul>
	<li class="nav">
		<a href="<?php echo $plxMotor->urlRewrite(); ?>" class="homepage" title="<?php echo t('Back to site') ?>"><?php echo t('Back to site');?></a>
		<br/>
		<?php echo gu_config::get('application_name');?>

	</li>
	<li class="user">
		<?php echo plxUtils::strCheck($plxMotor->aUsers[$_SESSION['user']]['name']) ?>

	</li>
	<li class="profil">
<?php
		if($_SESSION['profil']==PROFIL_ADMIN) printf('%s',L_PROFIL_ADMIN);
		elseif($_SESSION['profil']==PROFIL_MANAGER) printf('%s',L_PROFIL_MANAGER);
?>
	</li>
	<li class="pluxml">
		<a class="version" title="<?php echo gu_config::get('application_name').t(' Powered by') .' ' . t('Gutuma') ?>" href="<?php echo GUTUMA_URL ?>"><?php echo t('Gutuma').'&nbsp;'.GUTUMA_VERSION_NAME ?></a> &amp;
		<a title="PluXml" href="http://www.pluxml.org">Pluxml&nbsp;<?php echo $plxMotor->aConf['version'] ?></a>
		<br/>
		<a href="<?php echo GUTUMA_URL; ?>" onclick="window.open(this.href);return false;">Gutuma</a> <?php echo t('is released under the GPL');?> | &copy; Rowan
	</li>
</ul>

</div><!-- sidebar -->
	<?php }} else { ?>
	<div id="sidebar" style="display:none;"></div>
<?php } ?>
<div id="wrapper">

	<div id="container">

		<div id="content">