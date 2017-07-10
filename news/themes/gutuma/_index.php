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
	gu_messages_display(0);
}
else if (gu_latest_version_num < current_ver_num) {
	gu_success("<?php echo t('You are using a pre-release or beta version of Gutuma. Please report any bugs or problems you encounter.');?>");
	gu_messages_display(0);
}
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
	<div style="float: right; margin-left: 10px"><form action="https://www.paypal.com/cgi-bin/webscr" method="post">
	<input type="hidden" name="cmd" value="_s-xclick">
	<input type="image" src="https://www.paypal.com/en_GB/i/btn/x-click-but04.gif" border="0" name="submit" alt="<?php echo t('PayPal - The safer, easier way to pay online.');?>">
	<img alt="" border="0" src="https://www.paypal.com/en_GB/i/scr/pixel.gif" width="1" height="1">
	<input type="hidden" name="encrypted" value="-----BEGIN PKCS7-----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-----END PKCS7-----
	">
	</form></div>
	<p><?php echo t('Gutuma is free open source software released under the GPL version 3 licence. However, if you would like to assist further development of this project please consider making a donation, which will be much appreciated.');?></p>
</div>