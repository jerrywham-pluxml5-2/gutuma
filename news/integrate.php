<?php
/************************************************************************
 * @project Gutuma Newsletter Managment
 * @author Rowan Seymour
 * @copyright This source is distributed under the GPL
 * @file The integration page
 * @modifications Cyril Maguire
 */
/* Gutama plugin package
 * @version 1.6
 * @date	01/10/2013
 * @author	Cyril MAGUIRE
*/

include 'inc/gutuma.php';

if ($_SESSION['profil'] != PROFIL_ADMIN){
	header('Location:compose.php');
	exit();
}

gu_init();
gu_theme_start();

$lists = gu_list::get_all(FALSE, FALSE);

$script_import = '<script type="text/javascript" src="'.absolute_url('js/gadgets.js.php').'"></script>';

$gadget_type = is_post_var('gadget_type') ? get_post_var('gadget_type') : '';
$generate = is_post_var('gadget_generate') && ($gadget_type != '');

// Default to first list if one exists
$example_list_id = (count($lists) > 0) ? $lists[0]->get_id() : 0;

if ($generate) {
	$gadget_list = is_post_var('gadget_list') ? get_post_var('gadget_list') : $example_list_id;
	
	switch ($gadget_type) {
		case 'basic_link':
			$gadget_text = is_post_var('gadget_text') ? get_post_var('gadget_text') : t('Subscribe to my newsletter');
			$script_create = 'gu_gadgets_create_basic_link('.$gadget_list.', "'.$gadget_text.'")';
			$script_write = '<script type="text/javascript">gu_gadgets_write_basic_link('.$gadget_list.', "'.$gadget_text.'")</script>';
			$gadget_params = array('list', 'text');
			$gadget_requires_import = FALSE;
			break;
		case 'basic_form':
			$gadget_btn_text = is_post_var('gadget_btn_text') ? get_post_var('gadget_btn_text') : t('Subscribe');
			$gadget_prefix = is_post_var('gadget_prefix') ? get_post_var('gadget_prefix') : 'gu_';			
			$script_create = 'gu_gadgets_create_basic_form('.$gadget_list.', "'.$gadget_btn_text.'", "'.$gadget_prefix.'")';
			$script_write = '<script type="text/javascript">gu_gadgets_write_basic_form('.$gadget_list.', "'.$gadget_btn_text.'", "'.$gadget_prefix.'")</script>';
			$gadget_params = array('list', 'btn_text', 'prefix');
			$gadget_requires_import = FALSE;
			break;
		case 'ajax_link':
			$gadget_text = is_post_var('gadget_text') ? get_post_var('gadget_text') : t('Subscribe to my newsletter');
			$script_create = 'gu_gadgets_create_ajax_link('.$gadget_list.', "'.$gadget_text.'")';
			$script_write = '<script type="text/javascript">gu_gadgets_write_ajax_link('.$gadget_list.', "'.$gadget_text.'")</script>';
			$gadget_params = array('list', 'text');
			$gadget_requires_import = TRUE;	
			break;
		case 'ajax_form':
			$gadget_btn_text = is_post_var('gadget_btn_text') ? get_post_var('gadget_btn_text') : t('Subscribe');
			$gadget_email_hint = is_post_var('gadget_email_hint') ? get_post_var('gadget_email_hint') : t('Your email');
			$gadget_prefix = is_post_var('gadget_prefix') ? get_post_var('gadget_prefix') : 'gu_';	
			$script_create = 'gu_gadgets_create_ajax_form('.$gadget_list.', "'.$gadget_btn_text.'", "'.$gadget_email_hint.'", "'.$gadget_prefix.'")';
			$script_write = '<script type="text/javascript">gu_gadgets_write_ajax_form('.$gadget_list.', "'.$gadget_btn_text.'", "'.$gadget_email_hint.'", "'.$gadget_prefix.'")</script>';
			$gadget_params = array('list', 'btn_text', 'email_hint', 'prefix');
			$gadget_requires_import = TRUE;
			break;
	}
}

function create_list_control($name, $value, $all_option)
{
	global $lists;
	
	$html = '<select id="'.$name.'" name="'.$name.'">';
	if ($all_option)
		$html .= '<option value="0" '.(($value == 0) ? 'selected="selected"' : '').'>('.t('All').')</option>';
	foreach ($lists as $l)
		$html .= '<option value="'.$l->get_id().'" '.(($value == $l->get_id()) ? 'selected="selected"' : '').'>'.$l->get_name().'</option>';
  $html .= '</select>';
	return $html;
}

function create_text_control($name, $value)
{
	return '<input id="'.$name.'" name="'.$name.'" type="text" class="textfield" style="width: 95%" value="'.$value.'" />';
}
$_GET['noajax']=TRUE;
?>
<script type="text/javascript"><?php include("js/gadgets.js.php"); ?></script><!-- <script type="text/javascript" src="js/gadgets.js.php?noajax"></script> error 404 ??? -->

<?php //gu_theme_messages(); ?>

<?php
//Body
include_once 'themes/'.gu_config::get('theme_name').'/_integrate.php';


gu_theme_end();
?>