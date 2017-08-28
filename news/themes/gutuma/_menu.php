<?php 
/************************************************************************
 * @project Gutuma Newsletter Managment
 * @author Rowan Seymour
 * @copyright This source is distributed under the GPL
 * @file included menu page
 * @modifications Cyril Maguire
 */
/* Gutama plugin package
 * @version 1.6
 * @date	01/10/2013
 * @author	Cyril MAGUIRE
*/

$u = gu_config::getUsers();
foreach($u as $k => $v) {
	if ($v['id'] == $_SESSION['user'])
		$u['connect'] = $k;
}

if (gu_session_is_valid()) { ?>
			<div id="headerwelcome"><?php echo gu_config::get('collective_name'); ?> | <?php echo t('Welcome ');?><?php echo isset($u['connect']) ? $u['connect'] : gu_config::get('admin_name'); ?> | <a href="login.php?action=logout"><?php echo t('Logout');?></a></div>
<?php } ?>
		</div>	
		<div id="mainmenu">
<?php if (gu_session_is_valid()) {?>
			<ul>
				<li><a href="../../../core/admin/plugin.php?p=gutuma">Admin PluXml</a></li>
<?php if ($_SESSION['profil'] == PROFIL_ADMIN) :?> 
				<li><a href="index.php" <?php echo (str_ends($_SERVER['SCRIPT_NAME'], '/index.php') ? 'class="current"' : '') ?>><?php echo t('Home');?></a></li>
<?php endif;?>
				<li><a href="compose.php" <?php echo (str_ends($_SERVER['SCRIPT_NAME'], '/compose.php') ? 'class="current"' : '') ?>><?php echo t('Newsletters');?></a></li>
				<li><a href="lists.php" <?php echo (str_ends($_SERVER['SCRIPT_NAME'], '/lists.php') ? 'class="current"' : '') ?>><?php echo t('Lists');?></a></li>
<?php if ($_SESSION['profil'] == PROFIL_ADMIN) :?> 
				<li><a href="integrate.php" <?php echo (str_ends($_SERVER['SCRIPT_NAME'], '/integrate.php') ? 'class="current"' : '') ?>><?php echo t('Gadgets');?></a></li>				
				<li><a href="settings.php" <?php echo (str_ends($_SERVER['SCRIPT_NAME'], '/settings.php') ? 'class="current"' : '') ?>><?php echo t('Settings');?></a></li>
<?php endif;?>
			</ul>
<?php } ?>
		</div>
		<div id="content">