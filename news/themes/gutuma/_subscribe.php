<?php 
/************************************************************************
 * @project Gutuma Newsletter Managment
 * @author Rowan Seymour
 * @copyright This source is distributed under the GPL
 * @file included subscribe page
 * @modifications Cyril Maguire
 *
 * Gutama plugin package
 * @version 2.0.0
 * @date	23/09/2018
 * @author	Cyril MAGUIRE, Thomas Ingles
*/
$s = '';//pluriel
?>
		</div>
		<div id="content">
			<?php gu_theme_messages(); if(!$list_exist){ echo t('No List').'!'; return;} ?>
			<div style="text-align: center">
				<form action="" name="subscribe_form" method="post" onsubmit="return checkSubmit(this);">
<?php
				if (isset($list) && is_object($list))
					echo '<h2>'.$list->get_name().'</h2><input type="hidden" id="subscribe_list" name="subscribe_list[]" value="'.$list->get_id().'" />';
				elseif (isset($lists)) {
					$s = 's';
					echo '<h2>'.t('Newsletters').'</h2>';
					echo '<table border="0" style="width: 300px; margin: auto" class="results" cellpadding="0" cellspacing="0">';
					foreach ($lists as $list) {
						$list_id = $list->get_id();
						$checked = in_array($list_id,$posted_lists)?' checked':'';
?>
						<tr>
							<td style="text-align: left;"><?php echo $list->get_name(); ?></td>
							<td style="text-align: right;"><input id="subscribe_list" name="subscribe_list[]" type="checkbox" value="<?php echo $list->get_id(); ?>"<?php echo $checked ?> /></td>
						</tr>
<?php
					}
					echo '</table>';
				}
				echo '<p>'.t('Email address').'</p>';
				if ($address != '')
					echo '<h3>'.$address.'</h3><input name="subscribe_address" type="hidden" id="subscribe_address" value="'.$address.'" /><p><input name="k_submit" type="text" class="textfield" id="k_submit" style="width: 366px" value="'.$k.'" autocomplete="off" placeholder="'.t('key code to validate operation').'" /><br/><sup><sub>'.t('Key code is used for approval your (un)subsciption').'</sub></sup><br/><input name="send_k" type="checkbox" id="send_k" />&nbsp;'.t('Resend key code'.$s).'</p>';
				else
					echo '<p><input name="subscribe_address" type="text" class="textfield" id="subscribe_address" style="width: 200px" placeholder="address@email.com" required="required" /></p>';
?>
					<p><input name="subscribe_submit" type="submit" id="subscribe_submit" value="<?php echo t('Subscribe');?>" />
					<input name="unsubscribe_submit" type="submit" id="unsubscribe_submit" value="<?php echo t('Unsubscribe');?>" />
					</p>
				</form>
	<br />
	<p><a title="<?php echo t('See').'/'.t('Hide') ?>" href="javascript:hideShow('help')"><?php echo t('Help') ?></a></p>
<p id="help" style="text-align:left;margin:1rem;display:none;"><?php echo t('This newsletters use two step system to validate subscribtion.') ?>
<br /><br /><b><i><?php echo t('For subscribe:') ?></i></b><br /><b><?php echo t('First step') ?></b>, <?php echo t('enter an email address and clic on subscribe button. The system send in enter address an email with the steps to follow.') ?>
<br /><b><?php echo t('Second step') ?></b>, <?php echo t('clic on link(s) inside sended email <b>OR</b> paste the keycode in good field and clic on subscribe button.') ?>
<br /><br /><b><i><?php echo t('For unsubscribe:') ?></i></b>
<br /><?php echo t('Same procedure of subscribe but use unsubscribe button.') ?>
<br /><br /><b><i><?php echo t('Remarks:') ?></i></b>
<br /><i><?php echo t('Remember to <b>check in spam mails</b> if the messages are not found in your inbox') ?>.</i>
<br /><u><?php echo t('If no message is received, there are other possibilities to validate and (un)subscribe:') ?></u>
<br />*<i><?php echo t('Either tick "%" before clic on (un)subscribe button to send again the steps to be completed.',array(t('Resend key code'.$s))) ?>.</i>
<br />*<i><?php echo t('Or <a href="%">contact the author</a> of the site so that he can validate for you',array($contact_url)) ?>.</i></p>
			</div>
			<p>&nbsp;</p>
