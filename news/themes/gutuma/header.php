<?php 
/************************************************************************
 * @project Gutuma Newsletter Managment
 * @author Rowan Seymour
 * @copyright This source is distributed under the GPL
 * @file included header page
 * @modifications Cyril Maguire
 *
 * Gutama plugin package
 * @version 1.6
 * @date	09/07/2017
 * @author	Cyril MAGUIRE
*/
if(!defined('PLX_ROOT')) exit;
global $plxMotor;// = @plxMotor::getInstance();
?><!DOCTYPE html>
<html lang="<?php echo $plxMotor->aConf['default_lang'] ?>">
<head>
<meta http-equiv="content-type" content="text/html;charset=<?php echo GUTUMA_ENCODING; ?>" />
<meta name="robots" content="none">
<script type="text/javascript" src="js/misc.min.js"></script>
<script type="text/javascript" src="js/tw-sack.min.js"></script>
<script type="text/javascript" src="js/md5.min.js"></script>
<script type="text/javascript" src="js/sha1.min.js"></script>
<link href="themes/<?php echo gu_config::get('theme_name'); ?>/css/style.css" rel="stylesheet" type="text/css" />
<link rel="icon" type="image/png" href="themes/<?php echo gu_config::get('theme_name'); ?>/favicon.png" />
<!--[if IE]>
	<link rel="shortcut icon" type="image/x-icon" href="themes/<?php echo gu_config::get('theme_name'); ?>/favicon.ico" />
<![endif]-->
<title><?php echo GUTUMA_TITLE; ?></title>
</head>
<body>
	<?php if (gu_is_demo()) { ?><div id="demobanner">DEMO MODE</div><?php } ?>
	<div id="page">
		<div id="header">
			<div id="headertitle"><h1><?php echo t('Gutuma');?></h1></div>