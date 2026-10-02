<?php
/************************************************************************
 * @project Gutuma Newsletter Managment
 * @author Rowan Seymour
 * @copyright This source is distributed under the GPL
 * @file Theme functions
 * @modifications Cyril Maguire
 * Gutama plugin package
 * @version 2.2.2
 * @date	  09/08/2023
 * @author  Cyril MAGUIRE, Thomas Ingles
*/


/**
 * Méthode qui inclus dataTable (balise css & js) Inspiré d'adhesion
 * dataTableIncHead(
 * @return	stdio
 * @author	Thomas Ingles
 **/
function gu_theme_inc_head($w=false) {//cdn idée
?>
	<link rel="stylesheet" type="text/css" href="inc/pop.css?v=1.0.0" media="screen" />
	<script type="text/javascript">
		T = new Array();//bep multilingue
		T['Check before use'] = "<?php echo t('Check one or more line before!');?>";//misc.js : gu_allin_anim()
	</script>

<?php
	if(defined('GU_DATATABLE')){#newsletters only #tep
		$v = 'js/Vanilla-DataTables/vanilla-dataTables.min.';//github.com/Mobius1/Vanilla-DataTables/pull/65 & jscompress.com
		if($w)
			$v = $w;
?>
	<link rel="stylesheet" type="text/css" href="<?= $v ?>css?v=1.6.17" media="screen" />
	<script type="text/javascript" src="<?= $v ?>js?v=1.6.17"></script>
<?php
/*
<!-- https://fiduswriter.github.io/Simple-DataTables-classic/7-init-destroy-import-export/
<link href="https://cdn.jsdelivr.net/npm/simple-datatables-classic@latest/dist/style.css" rel="stylesheet" type="text/css">
<script src="https://cdn.jsdelivr.net/npm/simple-datatables-classic@latest" type="text/javascript"></script>
-->
*/
	}
}
/**
 * Outputs the start of the site-wide theme
 */
function gu_theme_start($nomenu = FALSE){//THEMEVERS PluXml 5.3.1 default theme
	include RPATH.'themes/'.gu_config::get('theme_name').'/header'.(gu_config::get('theme_name')=='default'?THEMEVERS:'').'.php';
	#evite le repost
	if(isset($_SESSION['gu_posted'])){
		gu_success($_SESSION['gu_posted']);
		unset($_SESSION['gu_posted']);
	}
/*
	#evite le repost (CODE)
	if($posted){
		$_SESSION['gu_posted'] = $posted;#gu_success msg
		gu_redirect($_SERVER['REQUEST_URI']);#'Location: ' . $_SERVER['REQUEST_URI'] + EXIT; #see inc misc
	}
*/
}

/**
 * Outputs the end of the site-wide theme
 */
function gu_theme_end($mvto=''){//$mvto is id to move notify msg
	$t = gu_config::get('theme_name');
	include RPATH.'themes/'.$t.'/footer'.($t=='default'?THEMEVERS:'').'.php';
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
	if(!$total) return;
	$sp = 1 + intval($start / $pagesize);
	$tp = 1 + intval($total / $pagesize);#ok if size > 1
?>
	<div class="pager" id="<?php echo $id; ?>">
		<div class="pagercontrols">&nbsp;<?php

	if ($total > $pagesize) {
		$last_pg = (0==($total % $pagesize))?$pagesize:($total % $pagesize);
		echo ($start > 0) ? ('<a href="'.$baseurl.'&amp;start=0#'.$id.'">&lt;&lt;</a>') : '&lt;&lt;';
		echo '&nbsp;&nbsp;&nbsp;';
		echo ($start > 0) ? ('<a id="'.$id.'_prev" href="'.$baseurl.'&amp;start='.max(0, $start - $pagesize).'#'.$id.'">&lt;</a>') : '&lt;';
		echo '&nbsp;&nbsp;&nbsp;';
		echo (($start + $pagesize) < $total) ? ('<a href="'.$baseurl.'&amp;start='.min($start + $pagesize, $total).'#'.$id.'">&gt;</a>') : '&gt;';
		echo '&nbsp;&nbsp;&nbsp;';
		echo (($start + $pagesize) < $total) ? ('<a href="'.$baseurl.'&amp;start='.($total - $last_pg).'#'.$id.'">&gt;&gt;</a>') : '&gt;&gt;';
		echo '&nbsp;';
	}
	echo '&nbsp;(&nbsp;'.t('Page').'&nbsp;'.$sp.'&nbsp;/&nbsp;'.$tp.'&nbsp;)';
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
	echo '<span id="gu_msg">';# class="notification success"
	if (isset($_SERVER['GU_ERROR_MSG'])){
		echo '<p id="gu_errormsg" class="gu_notification error" style="display:block;">'.trim($_SERVER['GU_ERROR_MSG'],'<br />').'</p>';
		if (isset($_SERVER['GU_ERROR_EXTRA'])){
			echo '  <div id="gu_errormore" class="gu_notification error"><a onclick="gu_messages_toggle_error_extra()" href="#">'.t('More').'</a></div>';
			echo '  <div id="gu_errorless" class="gu_notification error" style="display: none"><a onclick="gu_messages_toggle_error_extra()" href="#">'.t('Less').'</a></div>';
			echo '  <div id="gu_errorextra" class="gu_notification error" style="display: none;">'.$_SERVER['GU_ERROR_EXTRA'].'</div>';
		}
	} else {
		echo '<p id="gu_errormsg" class="gu_notification error" style="display:none;"></p>';
	}
	if (isset($_SERVER['GU_STATUS_MSG'])){
		echo '<p id="gu_statusmsg" class="gu_notification success" style="display:block;">'.$_SERVER['GU_STATUS_MSG'].'</p>';
	} else {
		echo '<p id="gu_statusmsg" class="gu_notification success" style="display:none;"></p>';
	}
	echo '</span>';
}
/**
 * Outputs a password control for the specified config setting
 * @param string $setting_name The config setting name
 */
function gu_theme_password_control($setting_name,$attrs=''){
	$val = is_post_var($setting_name) ? get_post_var($setting_name) : gu_config::get($setting_name);
	echo '<input id="'.$setting_name.'" name="'.$setting_name.'" type="password" class="textfield" style="width: 95%" value="'.$val.'"'.($attrs?' '.$attrs:'').'/>';
}
/**
 * Outputs a text control for the specified config setting
 * @param string $setting_name The config setting name
 */
function gu_theme_text_control($setting_name,$attrs=''){
	$val = is_post_var($setting_name) ? get_post_var($setting_name) : gu_config::get($setting_name);
	echo '<input id="'.$setting_name.'" name="'.$setting_name.'" type="text" class="textfield" style="width: 95%" value="'.$val.'"'.($attrs?' '.$attrs:'').'/>';
}
/**
 * Outputs a checkbox (boolean) control for the specified config setting
 * @param string $setting_name The config setting name
 */
function gu_theme_bool_control($setting_name){
	$val = is_post_var($setting_name) ? TRUE : gu_config::get($setting_name);
	echo '<input id="'.$setting_name.'" name="'.$setting_name.'" type="checkbox" value="1"'.($val ? ' checked="checked"' : '').' />';
}
/**
 * Outputs a textbox (integer only) control for the specified config setting
 * @param string $setting_name The config setting name
 * @param int $max_chars The maximum length in characters of any inputted integer
 */
function gu_theme_int_control($setting_name,$max_chars=10){
	$val = is_post_var($setting_name) ? get_post_var($setting_name) : gu_config::get($setting_name);
	echo '<input id="'.$setting_name.'" name="'.$setting_name.'" type="number" class="textfield" style="width: 70px" maxlength="'.$max_chars.'" onkeypress="return gu_is_numeric_key(event);" value="'.$val.'" />';
}
/**
 * Outputs a dropdown list control for the specified config setting
 * @param string $setting_name The config setting name
 * @param array $options The 2D array of possible options - [n][0] is the value of the nth option and [n][1] is the display name
 */
function gu_theme_list_control($setting_name,$options,$control=FALSE,$attrs=''){
	$val = is_post_var($setting_name) ? get_post_var($setting_name) : ($control ? $control : gu_config::get($setting_name));
	echo '<select name="'.$setting_name.'" id="'.$setting_name.'"'.($attrs?' '.$attrs:'').'>';
	foreach ($options as $option)
		echo '<option value="'.$option[0].'" '.(($val == $option[0]) ? 'selected="selected"' : '').'>'.$option[1].'</option>';
	echo '</select>';
}