<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
   "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="content-type" content="text/html;charset=<?php echo GUTUMA_ENCODING; ?>" />
<meta name="robots" content="none"> 
<script type="text/javascript" src="js/misc.js"></script>
<script type="text/javascript" src="js/tw-sack.js"></script>
<script type="text/javascript" src="js/md5.js"></script>
<link href="themes/<?php echo gu_config::get('theme_name'); ?>/style.css" rel="stylesheet" type="text/css" />
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
			<div id="headertitle"><h1>Gutuma</h1></div>
			<?php if (gu_session_is_valid()) { ?>
			<div id="headerwelcome"><?php echo gu_config::get('collective_name'); ?> | Welcome <?php echo gu_config::get('admin_name'); ?> | <a href="login.php?action=logout">Logout</a></div>
			<?php } ?>
		</div>	
		<div id="mainmenu">
			<?php if (gu_session_is_valid()) { ?>
			<ul>
				<li><a href="index.php" <?php echo (str_ends($_SERVER['SCRIPT_NAME'], '/index.php') ? 'class="current"' : '') ?>>Home</a></li>
				<li><a href="compose.php" <?php echo (str_ends($_SERVER['SCRIPT_NAME'], '/compose.php') ? 'class="current"' : '') ?>>Newsletters</a></li>
				<li><a href="lists.php" <?php echo (str_ends($_SERVER['SCRIPT_NAME'], '/lists.php') ? 'class="current"' : '') ?>>Lists</a></li>
				<li><a href="integrate.php" <?php echo (str_ends($_SERVER['SCRIPT_NAME'], '/integrate.php') ? 'class="current"' : '') ?>>Gadgets</a></li>				
				<li><a href="settings.php" <?php echo (str_ends($_SERVER['SCRIPT_NAME'], '/settings.php') ? 'class="current"' : '') ?>>Settings</a></li>
			</ul>
			<?php } ?>
		</div>
		<div id="content">