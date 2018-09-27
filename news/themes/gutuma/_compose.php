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

 include_once '_menu.php';?>

<div id="sectionheader">
	<div style="float: left;"><h2><?php echo $preview_mode ? t('Preview ') : t('Compose '); echo t('newsletter');?></h2></div>
	<div style="float: right;">
		<ul id="sectionmenu">
			<li><a href="compose.php" class="current"><?php echo t('Compose');?></a></li>
			<li><a href="newsletters.php?box=drafts"><?php echo t('Drafts');?> (<?php echo count($mailbox['drafts']) ?>)</a></li>
			<li><a href="newsletters.php?box=outbox"><?php echo t('Outbox');?> (<?php echo count($mailbox['outbox']) ?>)</a></li>
		</ul>
	</div>
</div>
<?php
if (count($lists) == 0)
	gu_error(t("A address list must be created before a newsletter can be sent"));
if (gu_config::get('admin_email') == '')
	gu_error(t("A valid admin email must be specified before a newsletter can be sent"));
gu_theme_messages();
?>
<form enctype="multipart/form-data" id="send_form" name="send_form" method="post" action="compose.php<?php echo gu_is_debugging() ? '?DEBUG' : ''; ?>"><input type="hidden" id="msg_id" name="msg_id" value="<?php echo $newsletter->get_id(); ?>" /><input type="hidden" id="is_modified" name="is_modified" value="<?php echo $is_modified; ?>" /><input type="hidden" id="autosave" name="autosave" value="<?php echo $autosave; ?>" />
	<div class="menubar">
		<input name="save_submit" type="submit" id="save_submit" value="<?php echo t('Save');?>" onclick="gu_cancel_unsaved_warning();" />
<?php if ($preview_mode) { ?>
		<input name="edit_submit" type="submit" id="edit_submit" value="<?php echo t('Edit');?>" onclick="gu_cancel_unsaved_warning();" />
<?php } else { ?>
		<input name="preview_submit" type="submit" id="preview_submit" value="<?php echo t('Preview');?>" onclick="gu_cancel_unsaved_warning();" />
<?php } ?>
		<input name="send_submit" type="submit" id="send_submit" value="<?php echo t('Send');?>" onclick="gu_cancel_unsaved_warning(); return gu_presend_check();" />
	</div>
	<div class="formfieldset" style="margin-top: 0px">
		<div class="formfield" style="border-bottom: 0px">
			<div class="formfieldlabel" style="width: 100px">&nbsp;</div>
			<div class="formfieldcontrols" style="text-align: right; width: 580px"><?php echo t('Address book');?>:<select name="send_lists" id="send_lists">
<?php
	foreach($lists as $l) {
		echo '<option value="'.$l->get_name().'">'.$l->get_name().' ('.$l->get_size().')</option>';
	}
?>
					</select>
					<input name="send_add_recip" type="button" id="send_add_recip" value="<?php echo t('Add');?>" onclick="gu_add_recipient();" /></div>
			<div class="formfielddivider"></div>
			<div class="formfieldlabel" style="width: 100px"><?php echo t('To');?>:</div>
    	<div class="formfieldcontrols" style="width: 580px"><input name="msg_recips" type="text" class="textfield" id="msg_recips" style="width: 99%" value="<?php echo $newsletter->get_recipients(); ?>" onchange="gu_set_modified(true)" placeholder="<?php echo t('Add recipient list here with Add button');?>" /></div>
			<div class="formfielddivider"></div>
			<div class="formfieldlabel" style="width: 100px"><?php echo t('Subject');?>:</div>
			<div class="formfieldcontrols" style="width: 580px"><input name="msg_subject" type="text" class="textfield" id="msg_subject" style="width: 99%" value="<?php echo $newsletter->get_subject(); ?>" onchange="gu_set_modified(true)" /></div>
		</div>
	</div>
<?php if ($preview_mode) { ?>
	<div class="messagepreview"><?php echo $newsletter->get_html(); ?></div><input name="msg_html" id="msg_html" type="hidden" value="<?php echo htmlentities($newsletter->get_html()); ?>" />
	<p><?php echo t('Below is the plain text version that will also be sent so that users with non-HTML email clients can receive this newsletter. You can edit this before the email is sent.');?></p>
	<textarea name="msg_text" id="msg_text" style="width: 670px; height: 250px" rows="7" cols="30" onchange="gu_set_modified(true)"><?php echo $newsletter->get_text(); ?></textarea>
<?php } else { ?>
	<textarea name="msg_html" id="msg_html" style="width: 100%; height: 300px" rows="7" cols="30" onchange="gu_set_modified(true)"><?php echo $newsletter->get_html(); ?></textarea>
<?php } ?>		
	<div class="menubar" style="margin-top: 10px">
		<div style="float: left"><input name="attach_file" type="file" id="attach_file" /> <input name="attach_submit" type="submit" id="attach_submit" value="<?php echo t('Attach');?>" onclick="gu_cancel_unsaved_warning();" /></div>
		<div style="float: right">
<?php
	if (count($attachments) > 0) {
		echo '<select id="msg_attachments" name="msg_attachments">';
		foreach ($attachments as $attachment) {
			$name = str_limit($attachment['name'], 25);		
			$sizeKB = round($attachment['size'] / 1024.0, 2);
			echo '<option value="'.$attachment['name'].'">'.$name.' ('.$sizeKB.' KB)</option>';
		}
		echo '</select> <input name="remove_submit" type="submit" id="remove_submit" value="'.t('Remove').'" onclick="gu_cancel_unsaved_warning();" />';
	}
?>
		</div>
	</div>
</form>
