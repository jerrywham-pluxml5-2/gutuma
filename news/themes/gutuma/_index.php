<?php 
/************************************************************************
 * @project Gutuma Newsletter Managment
 * @author Rowan Seymour
 * @copyright This source is distributed under the GPL
 * @file included home page
 * @modifications Cyril Maguire
 */
/* Gutama plugin package
 * @version 1.6
 * @date	01/10/2013
 * @author	Cyril MAGUIRE
*/

include_once '_menu.php';?>

<h2><?php echo t('Home');?></h2>

<?php gu_theme_messages(); ?>

<script type="text/javascript" src="<?php echo GUTUMA_UPDATE_URL; ?>" ></script>
<script type="text/javascript">
/* <![CDATA[ */
var current_ver_num = <?php echo GUTUMA_VERSION_NUM; ?>;
if (gu_latest_version_num > current_ver_num) {
	gu_success("Gutuma <a href=\"" + gu_latest_download_url + "\">" + gu_latest_version_name + "<?php echo t('</a> is now available. Please upgrade.');?>");
}
else if (gu_latest_version_num < current_ver_num) {
	gu_success("<?php echo t('You are using a pre-release or beta version of Gutuma. Please report any bugs or problems you encounter.');?>");
}
if (gu_latest_version_num != current_ver_num)
	gu_messages_display(0);
/* ]]> */
</script>
<p><?php echo t('Welcome to Gutuma - an easy to use, yet feature rich newsletter management tool, geared towards web designers and people out in the field.');?></p>
<div>
	<div style="float: left; width: 50%">
		<h3><?php echo t('Installation checklist');?></h3>
		<ul>
			<li><code><?php echo GUTUMA_LISTS_DIR; ?></code> <?php echo t('directory is writable...');?> <?php echo is_writable(GUTUMA_LISTS_DIR) ? '<span class="yes">'.t('Yes').'</span>' : '<span class="no">'.t('No').'</span>'; ?></li>
			<li><code><?php echo GUTUMA_TEMP_DIR; ?></code> <?php echo t('directory is writable...');?> <?php echo is_writable(GUTUMA_TEMP_DIR) ? '<span class="yes">'.t('Yes').'</span>' : '<span class="no">'.t('No').'</span>'; ?></li>	
			<li><code><?php echo GUTUMA_CONFIG_FILE; ?></code> <?php echo t('is writable...');?> <?php echo is_writable(GUTUMA_CONFIG_FILE) ? '<span class="yes">'.t('Yes').'</span>' : '<span class="no">'.t('No').'</span>'; ?></li>
			<li><?php echo t('Admin email address has been set...');?> <?php echo (gu_config::get('admin_email') != '') ? '<span class="yes">'.t('Yes').'</span>' : '<span class="no">'.t('No').'</span>'; ?></li>	
			<li><?php echo t('Admin password has been changed...');?> <?php echo (gu_config::get('admin_password') != 'admin') ? '<span class="yes">'.t('Yes').'</span>' : '<span class="no">'.t('No').'</span>'; ?></li>
			<li><?php echo t('Install script');?> <code>install.php</code> <?php echo t('deleted...');?> <?php echo (!file_exists('install.php')) ? '<span class="yes">'.t('Yes').'</span>' : '<span class="no">'.t('No').'</span>'; ?></li>	
		</ul>
	</div>
	<div style="float: right; width: 50%">
		<h3><?php echo t('System information');?></h3>
		<ul>
			<li><?php echo t('Gutuma version:');?> <b><?php echo GUTUMA_VERSION_NAME; ?></b></li>
			<li><?php echo t('PHP version:');?> <b><?php echo phpversion(); ?></b></li>
			<li><?php echo t('Currently storing:');?> <?php echo '<b>'.$total_addresses.'</b> '.t('addresse% in',array(($total_addresses<2?'':'s'))).' <b>'.count($lists).'</b> '.t('list%',array((count($lists)<2?'':'s'))); ?></li>
			<li><?php echo t('Mailbox:');?> <b><?php echo count($mailbox['drafts']); ?></b> <?php echo t('draft%',array((count($mailbox['drafts'])<2?'':'s')));?>, <b><?php echo count($mailbox['outbox']); ?></b> <?php echo t('sending',array(count($mailbox['outbox'])<2?'':'s'));?></li>
			<li><?php echo t('SMTP server:');?> <?php echo gu_config::get('use_smtp') ? ('<b>'.(gu_config::get('smtp_server') != '' ? gu_config::get('smtp_server') : 'AUTO').'</b>') : '<span class="no">Not enabled</span>' ?></li>	
		</ul>
	</div>
</div>
<div style="clear: both">
	<h3><?php echo t('Licence');?></h3>
<?php echo $tips;?>
	<p><?php echo t('Gutuma is free open source software released under the GPL version 3 licence. However, if you would like to assist further development of this project please consider making a donation, which will be much appreciated.');?></p>
</div>