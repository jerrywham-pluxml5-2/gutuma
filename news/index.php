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
?>
<script type="text/javascript" src="<?php echo GUTUMA_UPDATE_URL; ?>" ></script>
<script type="text/javascript">
/* <![CDATA[ */
var current_ver_num = <?php echo GUTUMA_VERSION_NUM; ?>;
if (gu_latest_version_num > current_ver_num) {
	gu_success("Gutuma <a href=\"" + gu_latest_download_url + "\"><b>" + gu_latest_version_name + "</b><?php echo t('</a> is now available. Please upgrade.');?>");
	gu_messages_display(0);
	setTimeout("gu_element_set_inner_html('new', '<br />' + document.getElementById('statusmsg').innerHTML);", 1618);
}
else if (gu_latest_version_num < current_ver_num) {
	gu_success("<?php echo t('You are using a pre-release or beta version of Gutuma. Please report any bugs or problems you encounter.');?>");
	gu_messages_display(0);
}
//~ if (gu_latest_version_num != current_ver_num)
	//~ gu_messages_display(0);
/* ]]> */
</script>
<?php
gu_theme_end();