<?php 
/************************************************************************
 * @project Gutuma Newsletter Managment
 * @author Rowan Seymour
 * @copyright This source is distributed under the GPL
 * @file included editlist page
 * @modifications Cyril Maguire
 */
/* Gutama plugin package
 * @version 1.6
 * @date	01/10/2013
 * @author	Cyril MAGUIRE
*/

?>
<div id="sectionheader" class="inline-form action-bar">
	<h2><?php echo t('Edit list');?></h2>
</div>
<?php gu_theme_messages(); ?>
<form method="post" name="edit_form" id="edit_form" action="">

	<div class="formfieldset">
		<div class="formfield">
			<div class="formfieldlabel"><?php echo t('Name');?></div>
      <div class="formfieldcontrols"><input type="text" class="textfield" name="list_name" id="list_name" style="width: 97%" value="<?php echo $list->get_name(); ?>" /></div>
    </div>
		<div class="formfield">
			<div class="formfieldcomment"><?php echo t('If the list is marked as private then people cannot subscribe to it, and it will not be listed on the default subscribe page');?></div>
			<div class="formfieldlabel"><?php echo t('Private');?></div> 
      <div class="formfieldcontrols"><input name="list_private" type="checkbox" id="list_private" value="1" <?php echo $list->is_private() ? 'checked="checked"' : ''; ?> /></div>
    </div>
	</div>
	<br/>
	<div class="menubar">
		<input name="list_back" type="button" id="list_back" value="<?php echo t('Back');?>" onclick="location.href='lists.php'" />
		<input name="list_update" type="submit" id="list_update" value="<?php echo t('Save');?>" />
		<input name="num_addresses" type="hidden" id="num_addresses" value="<?php echo $list->get_size(); ?>" />
	</div>
</form>
<h3><?php echo t('Subscribers');?></h3>
<div class="menubar">
	<div style="float: left">
		<form method="post" name="add_form" id="add_form" action="" onsubmit="return check_add(this);">
			<input name="new_address" type="text" class="textfield" id="new_address" />
			<input name="add_address" type="submit" id="add_address" value="<?php echo t('Add');?>" />		
		</form>
	</div>
	<div style="float: right">
		<form method="get" name="filter_form" id="filter_form" action="" onsubmit="filter_addresses(this); return false;">
			<input name="filter_list_name" type="text" class="textfield" id="filter_list_name" value="<?php echo $filter; ?>" />
			<input id="filter_submit" name="filter_submit" type="submit" value="<?php echo t('Search');?>" /><input id="filter_clear" name="filter_clear" type="button" value="<?php echo t('Clear');?>" onclick="reset_filter(this.form);" />
		</form>
	</div>	
</div>
<table border="0" cellspacing="0" cellpadding="0" class="results">
<tr>
	<td><strong><?php echo t('Address');?></strong></td>  
	<td>&nbsp;</td>
</tr>
<?php
$filtered_total = 0;

if ($list->get_size() > 0) {
	$address_id = 1000;
	$selection = $list->select_addresses($filter, $start, GUTUMA_PAGE_SIZE, $filtered_total);
	
	foreach ($selection as $address) {
?>
	<tr id="row_<?php echo ++$address_id; ?>">
		<td><?php echo $address; ?></td> 
		<td style="text-align: right"><a href="javascript:gu_remove_address('<?php echo $address; ?>', <?php echo $address_id; ?>)" class="imglink" title="<?php echo t('Delete');?>"><img src="themes/<?php echo gu_config::get('theme_name'); ?>/images/icon_delete.png" /></a></td>
	</tr>	
<?php
	}
}
?>
	<tr id="row_empty" style="display: <?php echo ($list->get_size() == 0) ? 'table-row' : 'none'; ?>"><td colspan="2" class="emptyresults"><?php echo t('No addresses');?></td></tr>	
</table>
<br/>
<?php

gu_theme_pager('pager_addresses', 'editlist.php?list='.$list->get_id().'&amp;filter='.$filter, $start, GUTUMA_PAGE_SIZE, $filtered_total);