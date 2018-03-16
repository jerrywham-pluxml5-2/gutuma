<?php 
/************************************************************************
 * @project Gutuma Newsletter Managment
 * @author Rowan Seymour
 * @copyright This source is distributed under the GPL
 * @file included settings page
 * @modifications Cyril Maguire
 *
 * Gutama plugin package
 * @version 1.6
 * @date	01/10/2013
 * @author	Cyril MAGUIRE
*/
?>
<form id="edit_form" name="edit_form" method="post" action="">
<div id="sectionheader" class="inline-form action-bar">
	<h2><?php echo $section_titles[$section]; ?></h2>
	<p id="sectionmenu">
		<a href="settings.php" class="button<?php echo ($section == 'general') ? ' blue' : ''; ?>"><?php echo t('General');?></a>
		<a href="settings.php?section=transport" class="button<?php echo ($section == 'transport') ? ' blue' : ''; ?>"><?php echo t('Transport');?></a>
		<a href="settings.php?section=messages" class="button<?php echo ($section == 'messages') ? ' blue' : ''; ?>"><?php echo t('Messages');?></a>
	</p>
</div>
<div class="menubar in-action-bar<?php echo defined('PLX_MYMULTILINGUE') ? ' multilingue' : '';?> section sml-12 med-9 med-offset-3 lrg-10 lrg-offset-2">
<?php if ($section == 'transport'){ ?>
	<input name="test_settings" type="submit" id="test_settings" class="orange" value="<?php echo t('Test');?>" />
<?php } ?>
	<input name="save_settings" type="submit" id="save_settings" class="green" value="<?php echo t('Save');?>" />
</div>
<?php gu_theme_messages(); ?>
	<div class="formfieldset">
<?php if ($section == 'general'){ ?>
		<div class="formfield">
			<div class="formfieldcomment"><?php echo t('The following is the name of the application ("Newsletters" by default)');?></div>
			<div class="formfieldlabel"><?php echo t('Application name');?>:</div>
			<div class="formfieldcontrols"><?php gu_theme_text_control('application_name'); ?></div>
		</div>
		<div class="formfield">
			<div class="formfieldcomment"><?php echo t('The following is usually the name of your organization or company. It is used in messages not specific to one list');?></div>
			<div class="formfieldlabel"><?php echo t('Collective name');?>:</div>
			<div class="formfieldcontrols"><?php gu_theme_text_control('collective_name'); ?></div>
		</div>
		<div class="formfield">
			<div class="formfieldcomment"><?php echo t('The following user details are used as the <em>From</em> field of all sent messages. To minimize the chances of your messages being misidentified as spam, ensure that the email address is valid');?></div>
			<div class="formfieldlabel"><?php echo t('Administrator name');?>:</div>
			<div class="formfieldcontrols"><?php gu_theme_text_control('admin_name','readonly="readonly"'); ?></div>
			<div class="formfielddivider"></div>
			<div class="formfieldlabel"><?php echo t('Administrator email');?>:</div>
			<div class="formfieldcontrols"><?php gu_theme_text_control('admin_email'); ?></div>
		</div>
<!--
		<div class="formfield">
			<div class="formfieldcomment"><?php echo t('Your login details');?>:</div>
			<div class="formfieldlabel"><?php echo t('Administrator username');?>:</div>
			<div class="formfieldcontrols"><?php gu_theme_text_control('admin_username'); ?></div>
			<div class="formfielddivider"></div>
			<div class="formfieldlabel"><?php echo t('Change administrator password');?>:</div>
			<div class="formfieldcontrols"><input type="password" id="admin_password" name="admin_password" class="textfield" size="12" /> <?php echo t('Retype');?> <input type="password" id="admin_password_retype" name="admin_password_retype" class="textfield" size="12" /></div>
		</div>
-->
		<div class="formfield">
			<div class="formfieldcomment"><?php echo t('The following selector choose text editor comportement when compose a newsletter.');?></div>
			<div class="formfieldlabel" title="tinyMCE"><?php echo t('Text editor');?>:</div>
			<div class="formfieldcontrols"><?php gu_theme_list_control('tiny_tools', array(array('all',t('Menu and toolbar')) , array('tools',t('Only toolbar')) , array('menu',t('Only menu')) , array('no',t('Editor Off')))); ?></div>
		</div>
		<div class="formfield">
			<div class="formfieldcomment"><?php echo t('The following selector choose spell checker comportement.');?></div>
			<div class="formfieldlabel"><?php echo t('Spell check');?>:</div>
			<div class="formfieldcontrols"><?php gu_theme_list_control('spell_check', array(array('browser',t('Provided by the web browser')) , array('no',t('Off')))); ?></div>
		</div>
		<div class="formfield">
			<div class="formfieldcomment"><?php echo t('The following selector list is for choose one of all Theme\'s available in subfolders of Gutuma.');?></div>
			<div class="formfieldlabel"><?php echo t('Name of the theme');?>:</div>
			<div class="formfieldcontrols"><?php
				$thms = array();
				$dir = dirname(__FILE__).'/../';
				$thm_dir = array_diff(scandir($dir), array('..', '.'));
				foreach($thm_dir AS $thm)
					$thms[] = array($thm,$thm);
				gu_theme_list_control('theme_name', $thms); ?></div>
		</div>
		<div class="formfield">
			<div class="formfieldcomment"><?php echo t('List of valid users');?></div>
<?php
		$users = unserialize(str_replace('\"','"',gu_config::get('users')));
		asort($users);
		$noUser = true;
		$unified = null;
		foreach ($users as $key => $value) :
			if ($plxPlugin->getParam('user_'.$value['id']) == 'activé'&&!isset($unified[$value['id']])) : 
				$noUser = false;
				$unified[$value['id']]=true;
?>
			<div class="formfieldlabel"><?php echo t('Login of user').' '.$key.'<br />Id:&nbsp;'.$value['id'].' ('.$value['profil'].')';?>:</div>
			<div class="formfieldcontrols"><input type="text" name="user_<?php echo $key; ?>" class="textfield users" value="<?php echo $value['login'];?>" readonly="readonly" style="width:95%;"/></div>	
			<div class="formfielddivider"></div>
<?php 
			endif;
		endforeach;
?>
		</div>
<?php if ($noUser) :?>
			<div class="formfieldlabel"><?php echo t('No other user'); ?></div>
<?php endif; ?>
<?php } elseif ($section == 'transport') { ?>
		<div class="formfield transport">
			<div class="formfieldcomment"><?php echo t('Gutuma will first try to use an SMTP server to send messages. If that fails, <code>Sendmail</code> may be tried followed by PHP <code>mail()</code>');?></div>
			<div class="formfieldlabel"><?php echo t('Use SMTP');?>:</div>
			<div class="formfieldcontrols"><?php gu_theme_bool_control('use_smtp'); ?></div>
			<div class="formfielddivider"></div>
			<div class="formfieldcomment"><?php echo t('If server and port are left blank, Gutuma will attempt to auto-detect them');?><code></code></div>
			<div class="formfieldlabel"><?php echo t('SMTP server');?>:</div>
			<div class="formfieldcontrols"><?php gu_theme_text_control('smtp_server'); ?></div>
			<div class="formfielddivider"></div>
			<div class="formfieldlabel"><?php echo t('SMTP port');?>:</div>
			<div class="formfieldcontrols"><?php gu_theme_int_control('smtp_port', 5); ?></div>
			<div class="formfielddivider"></div>
			<div class="formfieldlabel"><?php echo t('Encryption');?>:</div>
			<div class="formfieldcontrols"><?php gu_theme_list_control('smtp_encryption', array(array('', t('None')), array('SSL', 'SSL'), array('TLS', 'TLS'))); ?></div>
			<div class="formfielddivider"></div>
			<div class="formfieldlabel"><?php echo t('SMTP username');?>:</div>
			<div class="formfieldcontrols"><?php gu_theme_text_control('smtp_username'); ?></div>
			<div class="formfielddivider"></div>
			<div class="formfieldlabel"><?php echo t('SMTP password');?>:</div>
			<div class="formfieldcontrols"><?php gu_theme_password_control('smtp_password'); ?></div>
		</div>
		<div class="formfield">
			<div class="formfieldcomment"><?php echo t('If Sendmail is available on your server you can enable it here');?></div>
			<div class="formfieldlabel"><?php echo t('Use Sendmail');?>:</div>
			<div class="formfieldcontrols"><?php gu_theme_bool_control('use_sendmail'); ?></div>
		</div>
		<div class="formfield">
			<div class="formfieldcomment"><?php echo t('You may experience problems with sending to large address lists with PHP <code>mail()</code>');?></div>
			<div class="formfieldlabel"><?php echo t('Use PHP mail');?>:</div>
			<div class="formfieldcontrols"><?php gu_theme_bool_control('use_phpmail'); ?></div>
		</div>
		<div class="formfield">
			<div class="formfieldcomment"><?php echo t('Some SMTP servers have restrictions on the number of emails that can be sent per connection so you can limit the number of messages sent in a single batch. You can also set a time limit on batch sends to avoid timeouts.');?></div>
			<div class="formfieldlabel"><?php echo t('Max batch size');?>:</div>
			<div class="formfieldcontrols"><?php gu_theme_int_control('batch_max_size'); ?><?php echo t(' emails');?></div>
			<div class="formfielddivider"></div>
			<div class="formfieldlabel"><?php echo t('Batch time limit');?>:</div>
			<div class="formfieldcontrols"><?php gu_theme_int_control('batch_time_limit'); ?> <?php echo t('seconds');?></div>
		</div>
<?php } elseif ($section == 'messages') { ?>
		<div class="formfield messages">
			<div class="formfieldcomment"><?php echo t('List names can be automatically added to the subject of sent newsletters, e.g. <code>[List] Subject</code>');?></div>
			<div class="formfieldcontrols"><?php gu_theme_bool_control('msg_prefix_subject'); ?></div>
			<div class="formfieldlabel"><?php echo t('Prefix list name on subjects');?></div>
		</div>
		<div class="formfield messages">
			<div class="formfieldcomment"><?php echo t('If a newsletter is sent to more than one list, and a person is subscribed to more than one of those lists, the name of the list that occurs first in the <i>To</i> field will be used as the subject prefix. You can override this however, and use the collective name instead for all newsletters sent to more than one list');?></div>
			<div class="formfieldcontrols"><?php gu_theme_bool_control('msg_coll_name_on_multilist'); ?></div>
			<div class="formfieldlabel"><?php echo t('Use collective name for multi-list subjects');?></div>
		</div>
		<div class="formfield messages">
			<div class="formfieldcomment"><?php echo t('A signature containing an unsubscribe link can be automatically appended to all sent newsletters');?></div>
			<div class="formfieldcontrols"><?php gu_theme_bool_control('msg_append_signature'); ?></div>
			<div class="formfieldlabel"><?php echo t('Append unsubscribe link to sent newsletters');?></div>
		</div>
		<div class="formfield messages">
			<div class="formfieldcontrols"><?php gu_theme_bool_control('msg_admin_copy'); ?></div>
			<div class="formfieldlabel"><?php echo t('Send copy of sent newsletters to Administrator');?></div>
		</div>
		<div class="formfield messages">
			<div class="formfieldcomment"><?php echo t('Subscriber confirmations');?></div>
			<div class="formfieldcontrols"><?php gu_theme_bool_control('list_send_welcome'); ?></div>
			<div class="formfieldlabel"><?php echo t('Send welcome message to new subscribers');?></div>
			<div class="formfielddivider"></div>
			<div class="formfieldcontrols"><?php gu_theme_bool_control('list_send_goodbye'); ?></div>
			<div class="formfieldlabel"><?php echo t('Send goodbye message to unsubscribers');?></div>
		</div>
		<div class="formfield messages">
			<div class="formfieldcomment"><?php echo t('Administrator notifications');?></div>
			<div class="formfieldcontrols"><?php gu_theme_bool_control('list_subscribe_notify'); ?></div>
			<div class="formfieldlabel"><?php echo t('Notify of new subscribes');?>:</div>
			<div class="formfielddivider"></div>
			<div class="formfieldcontrols"><?php gu_theme_bool_control('list_unsubscribe_notify'); ?></div>
			<div class="formfieldlabel"><?php echo t('Notify of unsubscribes');?>:</div>
		</div>
	<div>
<?php } ?>
	</div>
</form>