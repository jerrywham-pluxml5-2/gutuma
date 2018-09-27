<?php
/************************************************************************
 * @project Gutuma Newsletter Managment
 * @author Rowan Seymour
 * @copyright This source is distributed under the GPL
 * @file The subscribe page
 * @modifications Cyril Maguire, Thomas Ingles
 *
 * Gutama plugin package
 * @version 2.0.0
 * @date	23/09/2018
 * @author	Cyril MAGUIRE, Thomas Ingles
*/
$slang = @$_GET['l'];//subscibe lang (url query trick)
if(!empty($slang) AND $slang!='en' AND $slang!='fr') unset($slang);//no translated lang goto default config
include_once 'inc/gutuma.php';
include_once 'inc/subscription.php';

// Initialize Gutuma without validation or redirection
gu_init(FALSE, FALSE);

gu_theme_start('ok');

// If variables have been posted then they may have prefixes
$posted_address = '';
$posted_lists = array();
$posted_action = '';
$posted_k = '';
foreach (array_keys($_POST) as $var) {
	$val = get_post_var($var);
	if (strpos($var, 'subscribe_address') !== FALSE)
		$posted_address = trim($val);
	elseif (strpos($var, 'subscribe_list') !== FALSE)
		$posted_lists = is_array($val) ? $val : (((int)$val != 0) ? array((int)$val) : array());//Maybe array or int
	elseif (strpos($var, 'unsubscribe_submit') !== FALSE)
		$posted_action = 'unsubscribe';
	elseif (strpos($var, 'subscribe_submit') !== FALSE)
		$posted_action = 'subscribe';
	elseif (strpos($var, 'send_k') !== FALSE)#BEP :: verif si k_submit > 4 alors c'est une clé == tentative de valid
		$posted_k = '0000';#tep ::: resend hash validate key
	elseif (strpos($var, 'k_submit') !== FALSE)
		$posted_k = trim($val);#tep
}
// Get querystring variables
$list_id = is_get_var('list') ? (int)get_get_var('list') : 0;
$address = is_get_var('addr') ? get_get_var('addr') : '';
$action = is_get_var('action') ? get_get_var('action') : '';
$k = is_get_var('k') ? get_get_var('k') : $posted_k;//hash key ²opt
// Check if we've got what we need to do a posted (un)subscription
if ($posted_action != '' && $posted_address != '' && (count($posted_lists) > 0)) {
	gu_subscription_process($posted_address, $posted_lists, ($posted_action == 'subscribe'), $posted_k);
}
else{
//	Check if we've got what we need to do a querystring (un)subscription
	if ($list_id > 0 && $address != '' && $action != '') {
		$list_ids = array($list_id);
		gu_subscription_process($address, $list_ids, ($action == 'subscribe'), $k);
	}
}
$list_exist = FALSE;
// Check to see if a single valid list was specified
if ($list_id > 0 || count($posted_lists) == 1) {
	if ($list_id == 0)
		$list_id = $posted_lists[0];
	$list = gu_list::get($list_id , FALSE);
	$list_exist = (!!$list AND !$list->is_private());
}
else{//Load all non-private lists
	$lists = gu_list::get_all(FALSE, FALSE);
	$list_exist = !!$lists;
}
if ($address == '' && $posted_address != '')
	$address = $posted_address;
if ($k == '' && $posted_k != '')#wip
	$k = $posted_k;
if($k == '0000' OR strlen($k) == 40)#tep len ::: (strlen($k) == 40 AND $action)
	$k = '';//reset key field
?>
<script type="text/javascript">
/* <![CDATA[ */
	function checkSubmit(form) {
		if (form.subscribe_address.value == "" || !gu_check_email(form.subscribe_address.value)) {
			alert("<?php echo t('You must enter a valid email address');?>");
			return false;
		}
		return true;
	}
/* ]]> */
</script>
<?php
//Body
include_once 'themes/'.gu_config::get('theme_name').'/_subscribe.php';
gu_theme_end();
