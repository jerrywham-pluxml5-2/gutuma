<?php 
/************************************************************************
 * @project Gutuma Newsletter Managment
 * @author Rowan Seymour
 * @copyright This source is distributed under the GPL
 * @file included subscribe page
 * @modifications Cyril Maguire
 *
 * Gutama plugin package
 * @version 1.6
 * @date	01/10/2013
 * @author	Cyril MAGUIRE
*/
$plxMotor = plxMotor::getInstance();
?>
<div style="text-align: center">
<?php gu_theme_messages(); ?>
<form action="" name="subscribe_form" method="post" onsubmit="return checkSubmit(this);">
<?php
if (isset($list) && is_object($list))
	echo '<h2>'.$list->get_name().'</h2><input type="hidden" id="subscribe_list" name="subscribe_list[]" value="'.$list->get_id().'" />';
elseif (isset($lists)) {
	echo '<h2>'.t('Newsletters').'</h2>';
	echo '<table border="0" style="max-width: 300px; margin: auto" class="results subscribe" cellpadding="0" cellspacing="0">';
	foreach ($lists as $list) {
?>
		<tr>
			<td class="sml-text-left"><input id="subscribe_list" name="subscribe_list[]" type="checkbox" value="<?php echo $list->get_id(); ?>" /></td>
			<td class="sml-text-left should-cut-off"><?php echo $list->get_name(); ?></td>
		</tr>
<?php
	}
	echo '</table>';
}
echo '<p>'.t('Your email address').'</p>';
if ($address != '')
	echo '<h3>'.$address.'</h3><input name="subscribe_address" type="hidden" id="subscribe_address" value="'.$address.'" />';
else
	echo '<p><input name="subscribe_address" type="text" class="textfield" id="subscribe_address" style="width: 200px" /></p>';
?>
	<p>&nbsp;</p>
	<p><input name="subscribe_submit" type="submit" id="subscribe_submit" value="<?php echo t('Subscribe');?>" />
	<input name="unsubscribe_submit" type="submit" id="unsubscribe_submit" value="<?php echo t('Unsubscribe');?>" />
	</p>
	<p><br/><a href="<?php echo PLX_GROOT; ?>"><?php echo t('Back home'); ?></a></p>
</form>
</div>
