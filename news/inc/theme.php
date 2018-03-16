<?php
/************************************************************************
 * @project Gutuma Newsletter Managment
 * @author Rowan Seymour
 * @copyright This source is distributed under the GPL
 * @file Theme functions
 * @modifications Cyril Maguire
 * Gutama plugin package
 * @version 1.8.7
 * @date	16/03/2018
 * @author	Cyril MAGUIRE, Th0m@s
*/

/**
 * Outputs the start of the site-wide theme
 */
function gu_theme_start($nomenu = FALSE){
	include RPATH.'themes/'.gu_config::get('theme_name').'/header.php';
}

/**
 * Outputs the end of the site-wide theme
 */
function gu_theme_end(){
	include RPATH.'themes/'.gu_config::get('theme_name').'/footer.php';
}

/**
 * Creates a pager for results
 * @param string $id Id of the pager
 * @param string $baseurl Page url for generating pager links
 * @param int $start The item offset in the results
 * @param int $pagesize The number of items per page
 * @param $total The total number of items
 */
function gu_theme_pager($id, $baseurl, $start, $pagesize, $total){
	//gu_debug('gu_pager_create("'.$id.'", "'.$baseurl.'", '.$start.', '.$pagesize.', '.$total.')');
?>
	<div class="pager" id="<?php echo $id; ?>" style="display: <?php echo ($total > 0) ? 'block' : 'none'; ?>">
		<div class="pagercontrols">
<?php
	$last_pg = (0==($total % $pagesize))?$pagesize:($total % $pagesize);
	echo ($start > 0) ? ('<a href="'.$baseurl.'&amp;start=0#'.$id.'">&lt;&lt;</a>') : '&lt;&lt;';
	echo '&nbsp;&nbsp;';
	echo ($start > 0) ? ('<a id="'.$id.'_prev" href="'.$baseurl.'&amp;start='.max(0, $start - $pagesize).'#'.$id.'">&lt;</a>&nbsp;&nbsp;') : '&lt;';
	echo '&nbsp;&nbsp;';
	echo (($start + $pagesize) < $total) ? ('<a href="'.$baseurl.'&amp;start='.min($start + $pagesize, $total).'#'.$id.'">&gt;</a>&nbsp;&nbsp;') : '&gt;';
	echo '&nbsp;&nbsp;';
	echo (($start + $pagesize) < $total) ? ('<a href="'.$baseurl.'&amp;start='.($total - $last_pg).'#'.$id.'">&gt;&gt;</a>') : '&gt;&gt;';
?>
		</div>
		<div class="pagerinfo">
<?php
	echo t('Showing <span id="%_start">%</span> to <span id="%_end">%</span> of <span id="%_total">%</span>',array($id,($start + 1),$id,min(($start + $pagesize), $total),$id,$total));	
?>
		</div>
	</div>
<?php
}
/**
 * Outputs any messages set by gu_error or gu_success
 */
function gu_theme_messages(){
	echo '<span id="msg">';
	if (isset($_SERVER['GU_ERROR_MSG'])){
		echo '<p id="errormsg" class="notification error">';
		if (isset($_SERVER['GU_ERROR_EXTRA'])){
			echo '  '.$_SERVER['GU_ERROR_MSG'];
			echo '  <div id="errormore"><a onclick="gu_messages_toggle_error_extra()" href="#">'.t('More').'</a></div>';
			echo '  <div id="errorless" style="display: none"><a onclick="gu_messages_toggle_error_extra()" href="#">'.t('Less').'</a></div>';
			echo '  <div id="errorextra" style="display: none;">'.$_SERVER['GU_ERROR_EXTRA'].'</div>';
		}
		else
			echo $_SERVER['GU_ERROR_MSG'].'</p>';
	}else {
		echo '<p id="errormsg" class="notification error" style="display:none;"></p>';
	}
	if (isset($_SERVER['GU_STATUS_MSG'])){
		echo '<p id="statusmsg" class="notification success">'.$_SERVER['GU_STATUS_MSG'].'</p>';
	} else {
		echo '<p id="statusmsg" class="notification success" style="display:none;"></p>';
	}
	echo '</span>';
}
/**
 * Outputs a password control for the specified config setting
 * @param string $setting_name The config setting name
 */
function gu_theme_password_control($setting_name,$option=false){
	$val = is_post_var($setting_name) ? get_post_var($setting_name) : gu_config::get($setting_name);
	echo '<input id="'.$setting_name.'" name="'.$setting_name.'" type="password" class="textfield" style="width: 95%" value="'.$val.'"  '.$option.'/>';
}
/**
 * Outputs a text control for the specified config setting
 * @param string $setting_name The config setting name
 */
function gu_theme_text_control($setting_name,$option=false){
	$val = is_post_var($setting_name) ? get_post_var($setting_name) : gu_config::get($setting_name);
	echo '<input id="'.$setting_name.'" name="'.$setting_name.'" type="text" class="textfield" style="width: 95%" value="'.$val.'" '.$option.'/>';
}
/**
 * Outputs a checkbox (boolean) control for the specified config setting
 * @param string $setting_name The config setting name
 */
function gu_theme_bool_control($setting_name){
	$val = is_post_var($setting_name) ? TRUE : gu_config::get($setting_name);
	echo '<input id="'.$setting_name.'" name="'.$setting_name.'" type="checkbox" value="1" '.($val ? 'checked="checked"' : '').' />';
}
/**
 * Outputs a textbox (integer only) control for the specified config setting
 * @param string $setting_name The config setting name
 * @param int $max_chars The maximum length in characters of any inputted integer
 */
function gu_theme_int_control($setting_name, $max_chars = 10){
	$val = is_post_var($setting_name) ? get_post_var($setting_name) : gu_config::get($setting_name);
	echo '<input id="'.$setting_name.'" name="'.$setting_name.'" type="text" class="textfield" style="width: 70px" maxlength="'.$max_chars.'" onkeypress="return gu_is_numeric_key(event);" value="'.$val.'" />';
}
/**
 * Outputs a dropdown list control for the specified config setting
 * @param string $setting_name The config setting name
 * @param array $options The 2D array of possible options - [n][0] is the value of the nth option and [n][1] is the display name
 */
function gu_theme_list_control($setting_name, $options, $control = FALSE){
	$val = is_post_var($setting_name) ? get_post_var($setting_name) : ($control ? $control : gu_config::get($setting_name));
	echo '<select name="'.$setting_name.'" id="'.$setting_name.'">';
	foreach ($options as $option)
		echo '<option value="'.$option[0].'" '.(($val == $option[0]) ? 'selected="selected"' : '').'>'.$option[1].'</option>';
	echo '</select>';
}