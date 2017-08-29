<?php
/************************************************************************
 * @project Gutuma Newsletter Managment
 * @author Rowan Seymour
 * @copyright This source is distributed under the GPL
 * @file The settings page
 * @modifications Cyril Maguire
 *
 * Gutama plugin package
 * @version 1.6
 * @date	01/10/2013
 * @author	Cyril MAGUIRE
*/
include_once 'inc/gutuma.php';
if (isset($_GET['token'])) {
	list($name,$username,$password,$salt,$userProfile,$id,$record_to_del) = explode('[::]',unserialize(base64_decode($_GET['token'])));
	list($name,$id) = explode('[::]',unserialize(base64_decode($record_to_del)));
} else {
	header('Location:../index.php');
}
gu_init();
gu_config::delUser($name);
$redirect = str_replace('plugins/gutuma/news','core/admin',absolute_url('plugin.php?p=gutuma&del=done&u='.$id));
header('location:'.$redirect);echo '<meta HTTP-EQUIV="REFRESH" content="0; url='.$redirect.'">';