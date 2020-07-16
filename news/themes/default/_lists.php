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
	<form method="post" name="add_form" id="add_form" action="" onsubmit="gu_list_add(this.new_list_name.value, this.new_list_private.checked); return false;">
		<p id="sectionmenu" class="plx<?php echo str_replace('.','',PLX_VERSION) ?>">
			<?php echo t('Name');?>&nbsp;<input name="new_list_name" type="text" class="textfield" id="new_list_name" placeholder="(<?php echo t('Create new list');?>)" /><br class="med-hide"/>
			<?php echo t('Private');?>&nbsp;<input type="checkbox" id="new_list_private" name="new_list_private" /><br />
			<input name="add_list" type="submit" id="add_list" class="green" value="<?php echo t('Add');?>" />
		</p>
	</form>
</div>
<?php gu_theme_messages(); ?>
<div class="formfieldset">
	<div class="formfield">
		<div class="formfieldcomment"><?php echo t('If the list is marked as private then people cannot subscribe to it, and it will not be listed on the default subscribe page.');?></div>
	</div>
</div>
<h3 class="text-center"><b><?php echo t('Tools');?></b></h3>
<div class="pager">
	<div class="formfieldflo@t float-left">&nbsp;</div>
	<div class="formfieldflo@t float-left"><script type="text/javascript">document.write(gu_lists_tools_menu())</script></div>
	<div class="formfieldflo@t float-right">&nbsp;</div>
</div>
<h5><?php echo t('These are the lists which have already been created.');?></h5>
<form method="post" name="lists_form" id="lists_form" action=""><input name="num_lists" type="hidden" id="num_lists" value="<?php echo count($lists); ?>" />
	<div class="scrollable-table">
		<table border="0" cellspacing="0" cellpadding="0" class="table full-width results" id="liststable">
			<tr>
				<td class="checkbox" title="<?php echo t('Actions');?>"><script type="text/javascript">document.write(gu_lists_thead_menu())</script><?php for($n=0;$n<16;$n++)echo'&nbsp;' ?></td>
				<td class="sml-text-center"><strong><?php echo t('Private');?></strong></td>
				<td class="cell-off"><b>(<?php echo t('Addresses');?>)</b><b>(<?php echo t('In transit');?>)</b><b><?php echo t('Name');?></b>
				<span>(<?php echo t('Public');?>)</span></td>
			</tr>
<?php
if (count($lists) > 0) {
	foreach($lists as $list) {
		$list_is_private = $list->is_private();
		$lid = $list->get_id();
?>
			<tr id="row_<?php echo $lid; ?>">
				<td><script type="text/javascript">document.write(gu_list_menu(<?php echo $lid; ?>))</script></td>
				<td class="sml-text-center"><?php echo $list_is_private ? t('Yes') : t('No'); ?></td>
				<td class="cell-off"><b>(<span id="size_<?php echo $lid; ?>"><?php echo $list->get_size(); ?></span>)</b><b><i id="link_<?php echo $lid; ?>i" style="<?php echo ($list_is_private||!@$listsTmpSize[$lid])?'display:none':''; ?>"><script type="text/javascript">document.write(gu_list_menu(<?php echo $lid; ?>, "tmp"))</script></i>(<span id="size_<?php echo $lid; ?>i"><?php echo @$listsTmpSize[$lid] ?></span>)</b><b><span class="should-cut-off"><?php echo $list->get_name(); ?></span></b>
				<span class="should-cut-off">(<?php echo $list->get_friend(); ?>)</span></td>
			</tr>
<?php
	}
}
?>
			<tr id="row_empty" style="display: <?php echo (count($lists) == 0) ? 'table-row' : 'none'; ?>"><td colspan="4" class="emptyresults"><?php echo t('No lists');?></td></tr>
		</table>
	</div>
</form>
<h5><?php echo t('Import list');?></h5>
<!-- The data encoding type, enctype, MUST be specified as below -->
	<!-- MAX_FILE_SIZE must precede the file input field -->
<div class="formfieldset">
	<div class="formfield">
		<div class="formfieldcomment"><?php echo t('A new list can be created from a CSV file of addresses. The format of this file should be email addresses in the first column - other columns will be ignored.');?></div>
	</div>
</div>
<form enctype="multipart/form-data" method="post" name="import_form" id="import_form" action="lists.php">
	<div class="menubar">
		<div><input name="import_file" type="file" id="import_file" /><br /><br />
			<?php echo t('Separate by').'&nbsp;'; gu_theme_list_control('sep', array(array(';',t('Semicolon (;)')),array(',',t('Comma (,)'))),';') ?> &amp;
			<?php echo t('Ingnore first line');?>&nbsp;<input type="checkbox" id="first" name="first" checked="" /> &nbsp;<input name="import_submit" type="submit" id="import_submit" class="green" value="<?php echo t('Import');?>" />
		</div>
	</div>
</form>