<?php 
/************************************************************************
 * @project Gutuma Newsletter Managment
 * @author Rowan Seymour
 * @copyright This source is distributed under the GPL
 * @file included header page
 * @modifications Cyril Maguire
 */
/* Gutama plugin package
 * @version 1.6
 * @date	01/10/2013
 * @author	Cyril MAGUIRE
*/

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
   "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="content-type" content="text/html;charset=<?php echo GUTUMA_ENCODING; ?>" />
<meta name="robots" content="none"> 
<script type="text/javascript" src="js/misc.js"></script>
<script type="text/javascript" src="js/tw-sack.js"></script>
<script type="text/javascript" src="js/md5.js"></script>
<script type="text/javascript" src="js/sha1.js"></script>
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
			
		
