<?php /************************************************************************
 * @project Gutuma Newsletter Managment
 * @author Rowan Seymour
 * @copyright This source is distributed under the GPL
 * @file included compose page
 * @modifications Cyril Maguire
 */
/* Gutama plugin package
 * @version 1.6
 * @date	01/10/2013
 * @author	Cyril MAGUIRE
*/

?>

<div id="sectionheader" class="inline-form action-bar">
	<h2><?php echo $preview_mode ? t('Preview ') : t('Compose '); echo t('newsletter');?></h2>
	<p id="sectionmenu">
		<a href="compose.php" class="button blue"><?php echo t('Compose');?></a>
		<a href="newsletters.php?box=drafts" class="button"><?php echo t('Drafts');?>&nbsp;(<?php echo count($mailbox['drafts']) ?>)</a>
		<a href="newsletters.php?box=outbox" class="button"><?php echo t('Outbox');?>&nbsp;(<?php echo count($mailbox['outbox']) ?>)</a>
	</p>
</div>
<?php
if (count($lists) == 0)
	gu_error(t("A address list must be created before a newsletter can be sent"));
if (gu_config::get('admin_email') == '')
	gu_error(t("A valid admin email must be specified before a newsletter can be sent"));

gu_theme_messages();

?>
<form enctype="multipart/form-data" id="send_form" name="send_form" method="post" action="compose.php<?php echo gu_is_debugging() ? '?DEBUG' : ''; ?>"><input type="hidden" id="msg_id" name="msg_id" value="<?php echo $newsletter->get_id(); ?>" /><input type="hidden" id="is_modified" name="is_modified" value="<?php echo $is_modified; ?>" />
	<div class="menubar in-action-bar section sml-12 med-9 med-offset-3 lrg-10 lrg-offset-2">
		<input class="green" name="save_submit" type="submit" id="save_submit" value="<?php echo t('Save');?>" onclick="gu_cancel_unsaved_warning();" />
<?php if ($preview_mode) { ?>
		<input class="blue" name="edit_submit" type="submit" id="edit_submit" value="<?php echo t('Edit');?>" onclick="gu_cancel_unsaved_warning();" />
<?php } else { ?>
		<input class="blue" name="preview_submit" type="submit" id="preview_submit" value="<?php echo t('Preview');?>" onclick="gu_cancel_unsaved_warning();" />
<?php } ?>
		<input class="orange" name="send_submit" type="submit" id="send_submit" value="<?php echo t('Send');?>" onclick="gu_cancel_unsaved_warning(); return gu_presend_check();" />
	</div>
	<div class="formfieldset">
		<div class="formfield">
			<div class="formfieldlabel"><?php echo t('Address book');?>:</div>
			<div class="formfieldcontrols show">
				<select name="send_lists" id="send_lists">
<?php
	foreach($lists as $l) {
		echo '					<option value="'.$l->get_name().'">'.$l->get_name().' ('.$l->get_size().')</option>'.PHP_EOL;
	}
?>
				</select>
				<input name="send_add_recip" type="button" id="send_add_recip" class="green" value="<?php echo t('Add');?>" onclick="gu_add_recipient();" />
			</div>
			<br />
			<div class="formfieldlabel"><?php echo t('To');?>:</div>
			<div class="formfieldcontrols"><input name="msg_recips" type="text" class="textfield" id="msg_recips" value="<?php echo $newsletter->get_recipients(); ?>" onchange="gu_set_modified(true)" /></div>
			<br />
			<div class="formfieldlabel"><?php echo t('Subject');?>:</div>
			<div class="formfieldcontrols"><input name="msg_subject" type="text" class="textfield" id="msg_subject" value="<?php echo $newsletter->get_subject(); ?>" onchange="gu_set_modified(true)" /></div>
		</div>
	</div>
	<div class="menubar" style="margin-top: 10px; padding-bottom: 3px;">
		<div class="float-left"><input name="attach_file" type="file" id="attach_file" />&nbsp;<input name="attach_submit" type="submit" id="attach_submit" class="green" value="<?php echo t('Attach');?>" onclick="gu_cancel_unsaved_warning();" /></div>
		<div class="float-right">
<?php
	if (count($attachments) > 0) {
		echo '<select id="msg_attachments" name="msg_attachments">';
		foreach ($attachments as $attachment) {
			$name = str_limit($attachment['name'], 25);		
			$sizeKB = round($attachment['size'] / 1024.0, 2);
			echo '<option value="'.$attachment['name'].'">'.$name.' ('.$sizeKB.' KB)</option>';
		}
		echo '</select> <input class="red" name="remove_submit" type="submit" id="remove_submit" value="'.t('Remove').'" onclick="gu_cancel_unsaved_warning();" />';
	}
?>
		</div>
	</div>
	<div class="clearer"></div>
	<br/>
<?php if ($preview_mode) { ?>
	<div class="messagepreview"><?php echo $newsletter->get_html(); ?></div><input name="msg_html" id="msg_html" type="hidden" value="<?php echo htmlentities($newsletter->get_html()); ?>" />
	<br/>
	<p><?php echo t('Below is the plain text version that will also be sent so that users with non-HTML email clients can receive this newsletter. You can edit this before the email is sent.');?></p>
	<br/>
	<textarea name="msg_text" id="msg_text" rows="20" cols="13" onchange="gu_set_modified(true)"><?php echo $newsletter->get_text(); ?></textarea>
<?php } else { ?>
	<textarea name="msg_html" id="msg_html" style="width: 100%; height: 360px" rows="20" cols="30" onchange="gu_set_modified(true)"><?php echo $newsletter->get_html(); ?></textarea>
<?php } ?>
</form>