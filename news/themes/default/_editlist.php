<?php
/************************************************************************
 * @project Gutuma Newsletter Managment
 * @author Rowan Seymour
 * @copyright This source is distributed under the GPL
 * @file included editlist page
 * @modifications Cyril Maguire, thomas Ingles
 *
 * Gutama plugin package
 * @version 2.2.2
 * @date	07/04/2023
 * @author	Cyril MAGUIRE, Thomas INGLES
*/
?>
<div id="sectionheader" class="inline-form action-bar">
	<form method="post" name="edit_form" id="edit_form" class="inline-form" action="">
		<h2>
			<?php echo t('Edit list');?>
			<sup><sub>(<?php echo t($list->is_private()?'Private':'Public') . ' ' . t($tmp?'temporary':'real') ?>)</sub></sup>
			<label for="list_private"><?php echo t('Private');?>&nbsp;<input name="list_private" type="checkbox" id="list_private" value="1"<?php echo ($list->is_private()?' checked="checked"':'') . ($tmp?' readonly="readonly" style="cursor:not-allowed"':''); ?> /></label><br />
		</h2>
		<p id="sectionmenu" class="<?php echo GU_PLXTHEMEV ?>">
			<label for="list_name"><?php echo t('Name');?>&nbsp;<input type="text" class="textfield" name="list_name" id="list_name" value="<?php echo $list->get_name(); ?>" placeholder="<?php echo t('Name') . ' (' . t('Private');?>)"<?php echo $tmp?' readonly="readonly" style="cursor:not-allowed"':'' ?> /></label><br class="med-hide"/>
			<label for="list_friend"><?php echo t('Public');?>&nbsp;<input type="text" class="textfield" name="list_friend" id="list_friend" value="<?php echo $list->get_friend(); ?>" placeholder="<?php echo t('Name') . ' (' . t('Public');?>)"<?php echo $tmp?' readonly="readonly" style="cursor:not-allowed"':'' ?> /></label><br />
			<input name="list_back" type="button" id="list_back" class="blue" value="<?php echo t('Back');?>" onclick="location.href='lists.php'" />
			<input name="see_other_face" type="button" id="see_other_face" style="<?php echo $seeOtherFace?'':'display:none' ?>" onclick="location.href='editlist.php?list=<?php echo $list->get_id().($tmp?'':'&amp;tmp=i') ; ?>'" title="<?php echo t('See').' '.t(($tmp?'real':'temporary')) ?>" value="<?php echo t(($tmp?'real':'temporary')) ?>">
			<input name="list_update" type="submit" id="list_update" class="green" value="<?php echo t('Save');?>" style="<?php echo $tmp?'display:none':'' ?>" />
			<input name="num_addresses" type="hidden" id="num_addresses" value="<?php echo $list->get_size(); ?>" />
		</p>
	</form>
</div>
<?php gu_theme_messages(); ?>
<div class="formfieldset">
	<div class="formfield">
		<div class="formfieldcomment"><?php echo t('If the list is marked as private then people cannot subscribe to it, and it will not be listed on the default subscribe page.');?></div>
	</div>
</div>
<h3><?php echo t('Subscribers') . ($tmp?' ('.t('In transit').')':'');?> </h3>
<div class="pager">
	<div style="" class="formfieldfloat float-right">
		<form method="get" name="filter_form" id="filter_form" action="" onsubmit="filter_addresses(this); return false;">
			<input name="filter_list_name" id="filter_list_name" type="text" class="textfield" value="<?php echo $filter; ?>" placeholder="<?php echo t('Search');?>" />
			<input name="filter_submit" id="filter_submit" type="submit" class="blue" value="<?php echo t('Search');?>" /><?php if (!empty($filter)){?>&nbsp;<input id="filter_clear" name="filter_clear" type="button" class="red" value="<?php echo t('Clear');?>" onclick="reset_filter(this.form);" /><?php } ?>
		</form>
	</div>
	<div style="<?php echo $tmp?'display:none':'' ?>" class="formfieldfloat float-left">
		<form method="post" name="add_form" id="add_form" action="" onsubmit="return check_add(this);">
			<input name="new_address" type="text" class="textfield" id="new_address" placeholder="<?php echo t('Add');?>: mail@exemple.com" />
			<input name="add_address" type="submit" id="add_address" class="green" value="<?php echo t('Add');?>" />
		</form>
	</div>
</div>
<?php
	gu_theme_pager('pager_addresses', 'editlist.php?list='.$list->get_id().($tmp?'&amp;tmp=i':'').'&amp;filter='.$filter, $start, GUTUMA_PAGE_SIZE, $filtered_total);
?>
<form>
<table border="0" cellspacing="0" cellpadding="0" class="results">
<tr>
	<td class="checkbox"><script type="text/javascript">document.write(gu_editlist_thead_menu())</script><?php for($n=0;$n<7;$n++)echo '&nbsp;';?></td>
	<td><strong><?php echo t('Addresses');?></strong></td>
</tr>
<?php echo $address_rows;# in editlist.php (init) ?>
</table>
</form>
