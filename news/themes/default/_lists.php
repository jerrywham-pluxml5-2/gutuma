<?php 
/************************************************************************
 * @project Gutuma Newsletter Managment
 * @author Rowan Seymour
 * @copyright This source is distributed under the GPL
 * @file included lists page
 * @modifications Cyril Maguire
 */
/* Gutama plugin package
 * @version 1.6
 * @date	01/10/2013
 * @author	Cyril MAGUIRE
*/

?>
<div id="sectionheader" class="inline-form action-bar">
	<h2><?php echo t('Manage lists');?></h2>
</div>
<?php gu_theme_messages(); ?>
<h5><?php echo t('These are the lists which have already been created.');?></h5>
<form method="post" name="lists_form" id="lists_form" action=""><input name="num_lists" type="hidden" id="num_lists" value="<?php echo count($lists); ?>" />
	<table border="0" cellspacing="0" cellpadding="0" class="results" id="liststable">
    <tr>
      <td><strong><?php echo t('Name');?></strong></td>
      <td><strong><?php echo t('Addresses');?></strong></td>
	  	<td><strong><?php echo t('Private');?></strong></td>    
      <td>&nbsp;</td>
    </tr>
<?php
if (count($lists) > 0) {
	foreach($lists as $list) {
?>
    <tr id="row_<?php echo $list->get_id(); ?>">
      <td><?php echo $list->get_name(); ?></td>
      <td><?php echo $list->get_size(); ?></td>
      <td><?php echo $list->is_private() ? t('Yes') : t('No'); ?></td>	  	  
      <td style="text-align: right"><script type="text/javascript">document.write(gu_list_menu(<?php echo $list->get_id(); ?>))</script></td>
    </tr>
<?php
	}
}
?>
		<tr id="row_empty" style="display: <?php echo (count($lists) == 0) ? 'table-row' : 'none'; ?>"><td colspan="4" class="emptyresults"><?php echo t('No lists');?></td></tr>
	</table> 
</form>
<form method="post" name="add_form" id="add_form" action="" onsubmit="gu_list_add(this.new_list_name.value, this.new_list_private.checked); return false;">
	<div class="menubar in-action-bar lists">
		<div>
			<p><?php echo t('Create new list');?></p>
			<?php echo t('Name');?> <input name="new_list_name" type="text" class="textfield" id="new_list_name" /> 
			&nbsp;&nbsp;<?php echo t('Private');?> <input type="checkbox" id="new_list_private" name="new_list_private" />	
			&nbsp;&nbsp;<input name="add_list" type="submit" id="add_list" value="<?php echo t('Add');?>" />
		</div>	
	</div>
</form>
<h5><?php echo t('Import list');?></h5>
<!-- The data encoding type, enctype, MUST be specified as below -->
	<!-- MAX_FILE_SIZE must precede the file input field -->
		<p><?php echo t('A new list can be created from a CSV file of addresses. The format of this file should be email addresses in the first column - other columns will be ignored.');?> </p>
<form enctype="multipart/form-data" method="post" name="import_form" id="import_form" action="lists.php">
	<div class="menubar">
		<div><input name="import_file" type="file" id="import_file" />&nbsp;&nbsp;<input name="import_submit" type="submit" id="import_submit" value="<?php echo t('Import');?>" /></div>
	</div>
</form>