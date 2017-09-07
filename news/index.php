<?php
/************************************************************************
 * @project Gutuma Newsletter Managment
 * @author Rowan Seymour
 * @copyright This source is distributed under the GPL
 * @file The home page
 * @modifications Cyril Maguire
 *
 * Gutama plugin package
 * @version 1.6
 * @date	01/10/2013
 * @author	Cyril MAGUIRE
*/
include_once 'inc/gutuma.php';
include_once 'inc/newsletter.php';
if ($_SESSION['profil'] != PROFIL_ADMIN){
	header('Location:compose.php');
	exit();
}
gu_init();
gu_theme_start();
// Calculate some stats
$lists = gu_list::get_all();
$total_addresses = 0;
foreach ($lists as $list) {
	$total_addresses += $list->get_size();
}
$mailbox = gu_newsletter::get_mailbox();
include_once 'themes/'.gu_config::get('theme_name').'/_index.php';//Body
gu_theme_end();