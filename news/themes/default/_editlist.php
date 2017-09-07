<?php 
/************************************************************************
 * @project Gutuma Newsletter Managment
 * @author Rowan Seymour
 * @copyright This source is distributed under the GPL
 * @file included editlist page
 * @modifications Cyril Maguire
 *
 * Gutama plugin package
 * @version 1.6
 * @date	01/10/2013
 * @author	Cyril MAGUIRE
*/
?>
<div id="sectionheader" class="inline-form action-bar">
	<h2><?php echo t('Edit list');?></h2>
	<form method="post" name="edit_form" id="edit_form" action="">
		<p id="sectionmenu">
			<?php echo t('Name');?>&nbsp;<input type="text" class="textfield" name="list_name" id="list_name" value="<?php echo $list->get_name(); ?>" placeholder="<?php echo t('Name');?>" /><br class="med-hide"/>
			<?php echo t('Private');?>&nbsp;<input name="list_private" type="checkbox" id="list_private" value="1" <?php echo $list->is_private() ? 'checked="checked"' : ''; ?> /><br />
			<input name="list_back" type="button" id="list_back" class="blue" value="<?php echo t('Back');?>" onclick="location.href='lists.php'" />
			<input name="list_update" type="submit" id="list_update" class="green" value="<?php echo t('Save');?>" />
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
<h3><?php echo t('Subscribers');?></h3>
<div class="pager">
	<div style="" class="formfieldfloat float-right">
		<form method="get" name="filter_form" id="filter_form" action="" onsubmit="filter_addresses(this); return false;">
			<input name="filter_list_name" type="text" class="textfield" id="filter_list_name" value="<?php echo $filter; ?>" placeholder="<?php echo t('Search');?>" />
			<input id="filter_submit" name="filter_submit" type="submit" class="blue" value="<?php echo t('Search');?>" /><?php if (!empty($filter)){?>&nbsp;<input id="filter_clear" name="filter_clear" type="button" class="red" value="<?php echo t('Clear');?>" onclick="reset_filter(this.form);" /><?php } ?>
		</form>
	</div>
	<div style="" class="formfieldfloat float-left">
		<form method="post" name="add_form" id="add_form" action="" onsubmit="return check_add(this);">
			<input name="new_address" type="text" class="textfield" id="new_address" placeholder="<?php echo t('Add');?>: mail@exemple.com" />
			<input name="add_address" type="submit" id="add_address" class="green" value="<?php echo t('Add');?>" />
		</form>
	</div>
</div>
<table border="0" cellspacing="0" cellpadding="0" class="results">
<tr>
	<td>&nbsp;</td>
	<td><strong><?php echo t('Address');?></strong></td>  
</tr>
<?php
$filtered_total = 0;

if ($list->get_size() > 0) {
	$address_id = 1000;
	$selection = $list->select_addresses($filter, $start, GUTUMA_PAGE_SIZE, $filtered_total);
	gu_theme_pager('pager_addresses', 'editlist.php?list='.$list->get_id().'&amp;filter='.$filter, $start, GUTUMA_PAGE_SIZE, $filtered_total);
	foreach ($selection as $address) {
?>
	<tr id="row_<?php echo ++$address_id; ?>">
		<td style="width:36px;"><a href="javascript:gu_remove_address('<?php echo $address; ?>', <?php echo $address_id; ?>)" class="imglink" title="<?php echo t('Delete');?>"><img src="themes/<?php echo gu_config::get('theme_name'); ?>/images/icon_delete.png" /></a></td>
		<td><?php echo $address; ?></td>
	</tr>
<?php
	}
}
?>
	<tr id="row_empty" style="display: <?php echo ($list->get_size() == 0) ? 'table-row' : 'none'; ?>"><td colspan="2" class="emptyresults"><?php echo t('No addresses');?></td></tr>	
</table>
<br/>
<?php
//gu_theme_pager('pager_addresses', 'editlist.php?list='.$list->get_id().'&amp;filter='.$filter, $start, GUTUMA_PAGE_SIZE, $filtered_total);

