<?php
/************************************************************************
 * @project Gutuma Newsletter Managment
 * @author Rowan Seymour
 * @copyright This source is distributed under the GPL
 * @file Generates a CSV version of an address list
 */
/* Gutama plugin package
 * @version 1.6
 * @date	01/10/2013
 * @author	Cyril MAGUIRE
*/


include 'inc/gutuma.php';

gu_init();

// Get id of list to edit from querystring
$list_id = isset($_GET['list']) ? (int)$_GET['list'] : 0;

// Load list data
$list = gu_list::get($list_id, TRUE);

header("Content-type: application/octet-stream");
header("Content-Disposition: attachment; filename=\"".$list->get_name().".csv\"");

foreach ($list->get_addresses() as $address) {
	echo $address."\r\n";
}
?>
