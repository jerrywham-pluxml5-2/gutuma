<?php
/************************************************************************
 * @project Gutuma Newsletter Managment
 * @author Rowan Seymour
 * @copyright This source is distributed under the GPL
 * @file The home page
 * @modifications Cyril Maguire
 *
 * Gutama plugin package
 * @version 1.9
 * @date	20/05/2018
 * @author	Cyril MAGUIRE, Thomas Ingles
*/
include_once 'inc/gutuma.php';
include_once 'inc/newsletter.php';
if ($_SESSION['profil'] != PROFIL_ADMIN){
	header('Location:compose.php');
	exit();
}
gu_init();
// Calculate some stats
$lists = gu_list::get_all();
$total_addresses = 0;
foreach ($lists as $list) {
	$total_addresses += $list->get_size();
}
$mailbox = gu_newsletter::get_mailbox();
ob_start();include'inc/index.tips.inc.php';$tips = ob_get_clean();//pourboires des auteurs
gu_theme_start();
include_once 'themes/'.gu_config::get('theme_name').'/_index.php';//Body
gu_theme_end();