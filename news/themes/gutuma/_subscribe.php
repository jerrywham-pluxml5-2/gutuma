<?php
/************************************************************************
 * @project Gutuma Newsletter Managment
 * @author Rowan Seymour
 * @copyright This source is distributed under the GPL
 * @file included subscribe page
 * @modifications Cyril Maguire, thomas Ingles
 *
 * Gutama plugin package
 * @version 2.1.0
 * @date	01/10/2018
 * @author	Cyril MAGUIRE, Thomas INGLES
*/
$s = '';# Pluriel(s) : click on the link'.$s.' in the email ...
?>
		</div>
		<div id="content">
			<?php gu_theme_messages(); if(!$list_exist){ echo t('No List').'!'; return;} ?>
			<div style="text-align: center">
				<p id="mvto" class="notification success" style="opacity:0"></p>
				<form action="" name="subscribe_form" method="post" onsubmit="return checkSubmit(this);">
<?php
				if (isset($list) && is_object($list))
					echo '<h2>'.$list->get_friend().'</h2><input type="hidden" id="subscribe_list" name="subscribe_list[]" value="'.$list->get_id().'" />';
				elseif (isset($lists)) {
					$s = 's';# Pluriels
					echo '<h2>'.t('Newsletters').'</h2>';
					echo '<table border="0" style="width: 300px; margin: auto" class="results" cellpadding="0" cellspacing="0">';
					foreach ($lists as $list) {
						$list_id = $list->get_id();
						$checked = in_array($list_id,$posted_lists)?' checked':'';
?>
						<tr>
							<td style="text-align: left;"><?php echo $list->get_friend(); ?></td>
							<td style="text-align: right;"><input id="subscribe_list" name="subscribe_list[]" type="checkbox" value="<?php echo $list->get_id(); ?>"<?php echo $checked ?> /></td>
						</tr>
<?php
					}
					echo '</table>';
				}
				echo '<p>'.t('Email address').'</p>';
				if ($address != '' && check_email($address))
					echo '<h3>'.$address.'</h3><input name="subscribe_address" type="hidden" id="subscribe_address" value="'.$address.'" />';
				else
					echo '<p><input name="subscribe_address" type="email" value="'.@$address.'" class="textfield" id="subscribe_address" style="width: 200px" placeholder="address@email.com" required="required" /></p>';
?>
					<p><input name="subscribe_submit" type="submit" id="subscribe_submit" value="<?php echo t('Subscribe');?>" />
					<input name="unsubscribe_submit" type="submit" id="unsubscribe_submit" value="<?php echo t('Unsubscribe');?>" />
					</p>
				</form>
<div id="gu_help"<?php echo (!gu_config::get('subscribe_help') OR @$_GET['help']=='no')?' style="display:none"':''; ?>>
	<p><a title="<?php echo t('See').'/'.t('Hide') ?>" href="javascript:hideShow('help')"><?php echo t('Help') ?></a></p>
<p id="help" style="text-align:left;margin:1rem;display:none;"><?php echo t('This newsletters use two step system to validate subscribtion.') ?>
<br /><br /><b><i><?php echo t('For subscribe:') ?></i></b><br /><b><?php echo t('First step') ?></b>, <?php echo t('enter an email address and clic on subscribe button. The system send in enter address an email with the steps to follow.') ?>
<br /><b><?php echo t('Second step') ?></b>, <?php echo t('click on the link'.$s.' in the email received to validate the subscription.') ?>
<br /><br /><b><i><?php echo t('For unsubscribe:') ?></i></b>
<br /><?php echo t('Same procedure of subscribe but use unsubscribe button.') ?>
<br /><br /><b><i><?php echo t('Remarks:') ?></i></b>
<br /><i><?php echo t('Remember to <b>check in spam mails</b> if the message are not found in your <b>inbox</b>') ?>.</i>
<br /><i><?php echo t('If you encounter problems registering / unsubscribing, please <a href="%">contact the site author</a> so that they can do it for you',array($contact_url)) ?>.</i></p>
</div><!-- gu_help -->
	<p<?php echo (!gu_config::get('show_home_link') OR @$_GET["backlink"]=='no')?' style="display:none"':''; ?>><br /><a href="<?php echo $plxMotor->racine; ?>"><?php echo t('Back home'); ?></a></p>
			</div>
