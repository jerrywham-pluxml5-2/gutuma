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

include_once '_menu.php';?>

<h2><?php echo t('Edit list');?> <sup><sub>(<?php echo t((!$tmp?$list->is_private()?'Private':'real':'temporary')) ?>) <a style="<?php echo $seeOtherFace?'':'display:none' ?>" href="editlist.php?list=<?php echo $list->get_id().($tmp?'':'&amp;tmp=i'); ?>">(<?php echo t('See').' '.t(($tmp?'real':'temporary')) ?>)</a></sub></sup></h2>
<?php gu_theme_messages(); ?>
<form method="post" name="edit_form" id="edit_form" action="">
	<div class="menubar"><input name="list_back" type="button" id="list_back" value="<?php echo t('Back');?>" onclick="location.href='lists.php'" /><input name="see_other_face" type="button" id="see_other_face" style="<?php echo $seeOtherFace?'':'display:none' ?>" onclick="location.href='editlist.php?list=<?php echo $list->get_id().($tmp?'':'&tmp=i') ; ?>'" title="<?php echo t('See').' '.t(($tmp?'real':'temporary')) ?>" value="<?php echo t(($tmp?'real':'temporary')) ?>"><input name="list_update" type="submit" id="list_update" value="<?php echo t('Save');?>" style="<?php echo $tmp?'display:none':'' ?>" /><input name="num_addresses" type="hidden" id="num_addresses" value="<?php echo $list->get_size(); ?>" /></div>
	<div class="formfieldset">
		<div class="formfield">
			<div class="formfieldlabel"><?php echo t('Name');?></div>
				<div class="formfieldcontrols"><input type="text" class="textfield" name="list_name" id="list_name" value="<?php echo $list->get_name(); ?>" style="width: 97%;<?php echo $tmp?' cursor:not-allowed;" readonly="readonly':'' ?>" /></div>
			</div>
		<div class="formfield">
			<div class="formfieldcomment"><?php echo t('If the list is marked as private then people cannot subscribe to it, and it will not be listed on the default subscribe page.');?></div>
			<div class="formfieldlabel"><?php echo t('Private');?></div>
				<div class="formfieldcontrols"><input name="list_private" type="checkbox" id="list_private" value="1"<?php echo ($list->is_private()?' checked="checked"':'') . ($tmp?' readonly="readonly" style="cursor:not-allowed"':''); ?> /></div>
			</div>
	</div>
</form>
<h3><?php echo t('Subscribers') . ($tmp?' ('.t('In transit') . ')':'');?></h3>
<div class="menubar">
	<div style="<?php echo $tmp?'display:none':'float: left' ?>">
		<form method="post" name="add_form" id="add_form" action="" onsubmit="return check_add(this);">
			<input name="new_address" type="text" class="textfield" id="new_address" />
			<input name="add_address" type="submit" id="add_address" value="<?php echo t('Add');?>" />
		</form>
	</div>
	<div style="float: right">
		<form method="get" name="filter_form" id="filter_form" action="" onsubmit="filter_addresses(this); return false;">
			<input name="filter_list_name" type="text" class="textfield" id="filter_list_name" value="<?php echo $filter; ?>" />
			<input id="filter_submit" name="filter_submit" type="submit" value="<?php echo t('Search');?>" /><?php if (!empty($filter)){?><input id="filter_clear" name="filter_clear" type="button" value="<?php echo t('Clear');?>" onclick="reset_filter(this.form);" /><?php } ?>
		</form>
	</div>
</div>
<table border="0" cellspacing="0" cellpadding="0" class="results">
<tr>
	<td><strong><?php t('Addresses');?></strong></td>
	<td>&nbsp;</td>
</tr>
<?php
$filtered_total = 0;
if ($list->get_size() > 0){
	$address_id = 1000;
	$selection = $list->select_addresses($filter, $start, GUTUMA_PAGE_SIZE, $filtered_total,($tmp?TRUE:FALSE));
	$keycode = $datetmp ='';
	$valtime = gu_config::get('days')*24*60*60;
	$baseUrl = absolute_url('subscribe.php').'?list='.$list->get_id().'&addr=';
	foreach ($selection as $address){
		$keycode = $datetmp = '';//uneeded if real list
//icons of ²opt in/out
		if ($tmp){//temp list : hide timestamp of io list
			$address = explode(';', $address);
			$icon = in_array($address[1],$maddresses)?'out':'ok';
			$noci = $icon!='ok'?'un':'';
			$txtDate = $icon!='ok'?'departure':'arrival';
			$datetmp = t('Time of '.$txtDate.':').' '.date(t('Y-m-d H:i'),$address[0]).PHP_EOL.t('Valid until:').' '.date(t('Y-m-d H:i'),($address[0] + $valtime));
			$address = $address[1];
			$keycode = $list->get_tmp_key($address);
			$keycode = '&nbsp;<br class="sml-show med-hide" /> <span title="🔗 '.t(ucfirst(($noci?$noci.'s':'s')).'ubscribe').' ('.t('Basic subscribe form').')"><a class="imglink" target="_blank" href="'.$baseUrl.$address.'&action='.$noci.'subscribe&k='.$keycode.'"><img src="themes/'.gu_config::get('theme_name').'/images/icon_'.$noci.'valid.png" /></a></span> <span class="imglink" title="'.t('View or hide key code with single click').'" onclick="hideShow(\'key_'.$address_id.'\')" style="cursor:pointer"><img src="themes/'.gu_config::get('theme_name').'/images/icon_key.png" /></span> <span id="key_'.$address_id.'" style="display:none">'.$keycode.'</span>';//bep...
		}else{//real list
			$icon = (in_array($address,$maddresses))?'out':'in';
			if($icon!='in'){//is in real and tmp (goto out)
				$keycode = $mist->get_tmp_key($address);
				$keycode = '&nbsp;<br class="sml-show med-hide" /><span title="🔗 '.t('Unsubscribe').' ('.t('Basic subscribe form').')"><a class="imglink" target="_blank" href="'.$baseUrl.$address.'&action=unsubscribe&k='.$keycode.'"><img src="themes/'.gu_config::get('theme_name').'/images/icon_unvalid.png" /></a></span> <span class="imglink" title="'.t('View or hide key code with single click').'" onclick="hideShow(\'key_'.$address_id.'\')" style="cursor:pointer"><img src="themes/'.gu_config::get('theme_name').'/images/icon_key.png" /></span> <span id="key_'.$address_id.'" style="display:none">'.$keycode.'</span>';//bep...
				$datetmp = t('Time of departure:').' '.date(t('Y-m-d H:i'),$mist->timeAddress).PHP_EOL.t('Valid until:').' '.date(t('Y-m-d H:i'),($mist->timeAddress + $valtime));
			}
		}
?>
	<tr id="row_<?php echo ++$address_id; ?>">
		<td><span title="<?php echo $datetmp ?>"><img src="themes/<?php echo gu_config::get('theme_name'); ?>/images/icon_<?php echo $icon ?>.png" />&nbsp;<?php echo $address; ?></span><?php echo $keycode; ?></td>
		<td style="text-align: right"><a href="javascript:gu_remove_address('<?php echo $address; ?>', <?php echo $address_id; ?>, '<?php echo $tmp; ?>')" class="imglink" title="<?php echo t('Delete');?>"><img src="themes/<?php echo gu_config::get('theme_name'); ?>/images/icon_delete.png" /></a></td>
	</tr>
<?php
	}
}
?>
	<tr id="row_empty" style="display: <?php echo ($list->get_size() == 0) ? 'table-row' : 'none'; ?>"><td colspan="2" class="emptyresults"><?php echo t('No addresses');?></td></tr>
</table>
<?php
gu_theme_pager('pager_addresses', 'editlist.php?list='.$list->get_id().($tmp?'&amp;tmp=i':'').'&amp;filter='.$filter, $start, GUTUMA_PAGE_SIZE, $filtered_total);