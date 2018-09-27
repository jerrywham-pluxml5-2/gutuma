<?php if(!defined('PLX_ROOT')) exit;
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
global $plxMotor;// = @plxMotor::getInstance();
?><!DOCTYPE html>
<html lang="<?php echo $plxMotor->aConf['default_lang'] ?>">
<head>
<title><?php echo GUTUMA_TITLE; ?></title>
<meta http-equiv="content-type" content="text/html;charset=<?php echo GUTUMA_ENCODING; ?>" />
<meta name="viewport" content="width=device-width, user-scalable=yes, initial-scale=1.0">
<meta name="robots" content="none, noindex, nofollow">
<link href="themes/<?php echo gu_config::get('theme_name'); ?>/css/style.css?v=<?php echo GUTUMA_VERSION_NAME ?>" rel="stylesheet" type="text/css" />
<link rel="icon" type="image/png" href="themes/<?php echo gu_config::get('theme_name'); ?>/favicon.png" />
<!--[if IE]>
	<link rel="shortcut icon" type="image/x-icon" href="themes/<?php echo gu_config::get('theme_name'); ?>/favicon.ico" />
<![endif]-->
<script type="text/javascript" src="js/misc.min.js?v=<?php echo GUTUMA_VERSION_NAME ?>"></script>
<script type="text/javascript" src="js/tw-sack.min.js?v=<?php echo GUTUMA_VERSION_NAME ?>"></script>
<script type="text/javascript" src="js/md5.min.js?v=<?php echo GUTUMA_VERSION_NAME ?>"></script>
<script type="text/javascript" src="js/sha1.min.js?v=<?php echo GUTUMA_VERSION_NAME ?>"></script>
</head>
<body>
	<?php if (gu_is_demo()) { ?><div id="demobanner">DEMO MODE</div><?php } ?>
	<div id="page">
		<div id="header">
			<div id="headertitle"><h1><?php echo t('Gutuma');?></h1></div>