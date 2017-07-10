<?php 
/************************************************************************
 * @project Gutuma Newsletter Managment
 * @author Rowan Seymour
 * @copyright This source is distributed under the GPL
 * @file included settings page
 * @modifications Cyril Maguire
 */
include_once '_menu.php';?>

<div id="sectionheader">
	<div style="float: left;"><h2><?php echo $section_titles[$section]; ?></h2></div>
	<div style="float: right;">
		<ul id="sectionmenu">
			<li><a href="settings.php" <?php echo ($section == 'general') ? 'class="current"' : ''; ?>><?php echo t('General');?></a></li>
			<li><a href="settings.php?section=transport" <?php echo ($section == 'transport') ? 'class="current"' : ''; ?>><?php echo t('Transport');?></a></li>
			<li><a href="settings.php?section=messages" <?php echo ($section == 'messages') ? 'class="current"' : ''; ?>><?php echo t('Messages');?></a></li>
		</ul>
	</div>
</div>

<?php gu_theme_messages(); ?>

<form id="edit_form" name="edit_form" method="post" action="">
	<div class="menubar">
<?php if ($section == 'transport') { ?>	
		<input name="test_settings" type="submit" id="test_settings" value="<?php echo t('Test');?>" />
<?php } ?>	
		<input name="save_settings" type="submit" id="save_settings" value="<?php echo t('Save');?>" />
	</div>
	<div class="formfieldset">
	
<?php if ($section == 'general') { ?>

		<div class="formfield">
			<div class="formfieldcomment"><?php echo t('The following is the name of the application ("Newsletters" by default)');?></div>
			<div class="formfieldlabel"><?php echo t('Application name');?></div>
			<div class="formfieldcontrols"><?php gu_theme_text_control('application_name'); ?></div>
		</div>
		<div class="formfield">
			<div class="formfieldcomment"><?php echo t('The following is usually the name of your organization or company. It is used in messages not specific to one list');?></div>
			<div class="formfieldlabel"><?php echo t('Collective name');?></div>
			<div class="formfieldcontrols"><?php gu_theme_text_control('collective_name'); ?></div>
		</div>
		<div class="formfield">
			<div class="formfieldcomment"><?php echo t('The following user details are used as the <em>From</em> field of all sent messages. To minimize the chances of your messages being misidentified as spam, ensure that the email address is valid');?></div>
			<div class="formfieldlabel"><?php echo t('Administrator name');?></div>
			<div class="formfieldcontrols"><?php gu_theme_text_control('admin_name'); ?></div>
			<div class="formfielddivider"></div>
			<div class="formfieldlabel"><?php echo t('Administrator email');?></div>
			<div class="formfieldcontrols"><?php gu_theme_text_control('admin_email'); ?></div>
		</div>
		<!--<div class="formfield">
			<div class="formfieldcomment"><?php echo t('Your login details');?></div>
			<div class="formfieldlabel"><?php echo t('Administrator username');?></div>
			<div class="formfieldcontrols"><?php gu_theme_text_control('admin_username'); ?></div>
			<div class="formfielddivider"></div>
			<div class="formfieldlabel"><?php echo t('Change administrator password');?></div>
			<div class="formfieldcontrols"><input type="password" id="admin_password" name="admin_password" class="textfield" size="12" /> <?php echo t('Retype');?> <input type="password" id="admin_password_retype" name="admin_password_retype" class="textfield" size="12" /></div>
		</div>-->
		<div class="formfield">
			<div class="formfieldcomment"><?php echo t('The following is the name of the folder which contains all the Theme\'s files (Becarefull to respect casse)');?></div>
			<div class="formfieldlabel"><?php echo t('Name of the theme');?></div>
			<div class="formfieldcontrols"><?php
				$thms = array();
				$dir = dirname(__FILE__).'/../';
				$thm_dir = array_diff(scandir($dir), array('..', '.'));
				foreach($thm_dir AS $thm)
					$thms[] = array($thm,$thm);
				gu_theme_list_control('theme_name', $thms); ?></div>
		</div>

<?php } elseif ($section == 'transport') { ?>

		<div class="formfield">
			<div class="formfieldcomment"><?php echo t('Gutuma will first try to use an SMTP server to send messages. If that fails, <code>Sendmail</code> may be tried followed by PHP <code>mail()</code>');?></div>
			<div class="formfieldlabel"><?php echo t('Use SMTP');?></div>
			<div class="formfieldcontrols"><?php gu_theme_bool_control('use_smtp'); ?></div>
			<div class="formfielddivider"></div>
			<div class="formfieldcomment"><?php echo t('If server and port are left blank, Gutuma will attempt to auto-detect them');?><code></code></div>
			<div class="formfieldlabel"><?php echo t('SMTP server');?></div>
			<div class="formfieldcontrols"><?php gu_theme_text_control('smtp_server'); ?></div>
			<div class="formfielddivider"></div>
			<div class="formfieldlabel"><?php echo t('SMTP port');?></div>
		  <div class="formfieldcontrols"><?php gu_theme_int_control('smtp_port', 5); ?>
			<?php echo t('Encryption');?> <?php gu_theme_list_control('smtp_encryption', array(array('', t('None')), array('SSL', 'SSL'), array('TLS', 'TLS'))); ?></div>
			<div class="formfielddivider"></div>
			<div class="formfieldlabel"><?php echo t('SMTP username<');?></div>
			<div class="formfieldcontrols"><?php gu_theme_text_control('smtp_username'); ?></div>
			<div class="formfielddivider"></div>
			<div class="formfieldlabel"><?php echo t('SMTP password');?></div>
			<div class="formfieldcontrols"><?php gu_theme_text_control('smtp_password'); ?></div>
		</div>
		<div class="formfield">
			<div class="formfieldcomment"><?php echo t('If Sendmail is available on your server you can enable it here');?></div>
			<div class="formfieldlabel"><?php echo t('Use Sendmail');?></div>
			<div class="formfieldcontrols"><?php gu_theme_bool_control('use_sendmail'); ?></div>
		</div>
		<div class="formfield">
			<div class="formfieldcomment"><?php echo t('You may experience problems with sending to large address lists with PHP <code>mail()</code>');?></div>
			<div class="formfieldlabel"><?php echo t('Use PHP mai');?>l</div>
			<div class="formfieldcontrols"><?php gu_theme_bool_control('use_phpmail'); ?></div>
		</div>
		<div class="formfield">
			<div class="formfieldcomment"><?php echo t('Some SMTP servers have restrictions on the number of emails that can be sent per connection so you can limit the number of messages sent in a single batch. You can also set a time limit on batch sends to avoid timeouts.');?></div>
			<div class="formfieldlabel"><?php echo t('Max batch size');?></div>
			<div class="formfieldcontrols"><?php gu_theme_int_control('batch_max_size'); ?><?php echo t(' emails');?></div>
			<div class="formfielddivider"></div>
			<div class="formfieldlabel"><?php echo t('Batch time limit');?></div>
			<div class="formfieldcontrols"><?php gu_theme_int_control('batch_time_limit'); ?> <?php echo t('seconds');?></div>						
		</div>
		
<?php } elseif ($section == 'messages') { ?>

		<div class="formfield">
			<div class="formfieldcomment"><?php echo t('List names can be automatically added to the subject of sent newsletters, e.g. <code>[List] Subject</code>');?></div>
			<div class="formfieldlabel"><?php echo t('Prefix list name on subjects');?></div>
			<div class="formfieldcontrols"><?php gu_theme_bool_control('msg_prefix_subject'); ?></div>
		</div>
		<div class="formfield">
			<div class="formfieldcomment"><?php echo t('If a newsletter is sent to more than one list, and a person is subscribed to more than one of those lists, the name of the list that occurs first in the <i>To</i> field will be used as the subject prefix. You can override this however, and use the collective name instead for all newsletters sent to more than one list');?></div>
			<div class="formfieldlabel"><?php echo t('Use collective name for multi-list subjects');?></div>
			<div class="formfieldcontrols"><?php gu_theme_bool_control('msg_coll_name_on_multilist'); ?></div>
		</div>
		<div class="formfield">
			<div class="formfieldcomment"></div><?php echo t('A signature containing an unsubscribe link can be automatically appended to all sent newsletters');?></div>
			<div class="formfieldlabel"><?php echo t('Append unsubscribe link to sent newsletters');?></div>
			<div class="formfieldcontrols"><?php gu_theme_bool_control('msg_append_signature'); ?></div>
		</div>
		<div class="formfield">
			<div class="formfieldlabel"><?php echo t('Send copy of sent newsletters to Administrator');?></div>
			<div class="formfieldcontrols"><?php gu_theme_bool_control('msg_admin_copy'); ?></div>
		</div>
		<div class="formfield">
			<div class="formfieldcomment"><?php echo t('Subscriber confirmations');?></div>
			<div class="formfieldlabel"><?php echo t('Send welcome message to new subscribers');?></div>
			<div class="formfieldcontrols"><?php gu_theme_bool_control('list_send_welcome'); ?></div>
			<div class="formfielddivider"></div>
			<div class="formfieldlabel"><?php echo t('Send goodbye message to unsubscribers');?></div>
			<div class="formfieldcontrols"><?php gu_theme_bool_control('list_send_goodbye'); ?></div>
		</div>
		<div class="formfield">
			<div class="formfieldcomment"><?php echo t('Administrator notifications');?></div>
			<div class="formfieldlabel"><?php echo t('Notify of new subscribes');?></div>
			<div class="formfieldcontrols"><?php gu_theme_bool_control('list_subscribe_notify'); ?></div>
			<div class="formfielddivider"></div>
			<div class="formfieldlabel"><?php echo t('Notify of unsubscribes');?></div>
			<div class="formfieldcontrols"><?php gu_theme_bool_control('list_unsubscribe_notify'); ?></div>
		</div>
	<div>
		
<?php } ?>
	</div>
</form>
<p>&nbsp;</p>